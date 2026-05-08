<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Cms;
use App\Models\Blog;
use App\Helpers\CustomHelper;
use App\Helpers\MenuHelper;

class MenuController extends Controller
{
    private $limit;
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->limit = 20;
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

	public function index(Request $request)
	{
	    if ($request->ajax()) {
	        $data = Menu::select(['id', 'title', 'slug', 'position', 'status', 'created_at']);

	        return DataTables::of($data)
	            ->addColumn('action', function ($row) {
	                $encryptedId = CustomHelper::encrypt($row->id);
	                $editUrl = route($this->ADMIN_ROUTE_NAME . '.menus.add', ['id' => $encryptedId]);
	                $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.menus.delete', ['id' => $encryptedId]);

	                $actions = '';
	                $hasAnyAction = false;

	                // Check if user has any permissions to show dropdown
	                if (auth()->user()->hasRole('SuperAdmin') || 
	                    (auth()->user()->can('menus.edit')) || 
	                    (auth()->user()->can('menus.delete'))) {
	                    
	                    $actions = '<div class="dropdown">
	                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
	                            <i class="ti tabler-dots-vertical"></i>
	                        </button>
	                        <div class="dropdown-menu">';

	                    // Edit permission
	                    if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('menus.edit'))) {
	                        $actions .= '<a class="dropdown-item" href="' . $editUrl . '">
	                            <i class="ti tabler-pencil me-1"></i> Edit
	                        </a>';
	                        $hasAnyAction = true;
	                    }

	                    // Delete permission
	                    if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('menus.delete'))) {
	                        $actions .= '<a class="dropdown-item btn-delete-menu" href="javascript:void(0);" data-url="' . $deleteUrl . '">
	                            <i class="ti tabler-trash me-1"></i> Delete
	                        </a>';
	                        $hasAnyAction = true;
	                    }

	                    $actions .= '</div></div>';
	                }

	                // If no actions available, return empty string
	                return $hasAnyAction ? $actions : '';
	            })
	            ->addColumn('items', function ($row) {
	            	$encryptedId = CustomHelper::encrypt($row->id);
	                $itemsUrl = route($this->ADMIN_ROUTE_NAME . '.menus.items', $encryptedId);

	                return '<a href="' . $itemsUrl . '" class="text-body" title="Menu Items">
	                            <i class="ti tabler-list"></i>
	                        </a>';
	            })
	            ->addColumn('status', function ($row) {
	                return $row->status == 1
	                    ? '<span class="badge bg-label-success">Active</span>'
	                    : '<span class="badge bg-label-danger">Inactive</span>';
	            })
	            ->editColumn('created_at', function ($row) {
	                return Carbon::parse($row->created_at)->format('d M, Y h:i A');
	            })
	            ->rawColumns(['action', 'status', 'items'])
	            ->make(true);
	    }

	    return view('admin.menus.index');
	}

    public function add(Request $request)
    {
        $id = !empty($request->id && is_string($request->id)) ? CustomHelper::decrypt($request->id) : "";
        $menu = $id ? Menu::find($id) : null;

        if ($id && !$menu) {
            return back()->with('error', 'Menu not found.');
        }

        if ($request->isMethod('post')) {
            return $this->save($request, $request->id);
        }

        return view('admin.menus.form', [
            'menu' => $menu,
            'page_heading' => $menu ? "Update Menu ($menu->title)" : 'Add Menu',
        ]);
    }

    private function save(Request $request, $id = null)
    {
        try {
        	$id = !empty($id) ? CustomHelper::decrypt($id) : "";
            $rules = [
                'title' => 'required|string|max:255',
                'position' => 'required',
                'status' => 'required|integer',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $data = $request->except(['_token', 'id', 'back_url']);
            $backUrl = $request->back_url ?? $this->ADMIN_ROUTE_NAME . '/menus';
            $data['slug'] = CustomHelper::GetSlug('menus', 'id', $id, $data['title']);

            $menu = Menu::updateOrCreate(['id' => $id], $data);

            // Log activity
            $actionType = $id ? 'Update Menu' : 'Create Menu';
            $description = ($id ? 'Updated' : 'Created') . ' menu: ' . $menu->title;
            
            CustomHelper::recordActionLog(
                url()->current(),
                'menus',
                $menu->id,
                $actionType,
                $description,
                json_encode($data)
            );

            // Clear menu cache after update
            MenuHelper::clearMenuCache();

            return redirect(url($backUrl))->with('success', 'Menu has been saved successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function delete(Request $request, $id)
    {
        try {

            $menu = Menu::findOrFail($id);
            $menu->delete();

            DB::table('menu_items')->where('menu_id', $id)->delete();

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'menus',
                $id,
                'Delete Menu',
                'Deleted menu: ' . $menu->title,
                'Deleted menu: ' . $menu->title . ' (ID: ' . $id . ')'
            );

            // Clear menu cache after deletion
            MenuHelper::clearMenuCache();

            return redirect('admin/menus')->with('success', 'Menu deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting menu. ' . $e->getMessage());
        }
    }

    public function items(Request $request, $id)
    {
    	$id = !empty($id) ? CustomHelper::decrypt($id) : "";
    	$item_id = !empty($request->item_id) ? CustomHelper::decrypt($request->item_id) : "";
        $menu = Menu::find($id);
        if (!$menu) return back()->with('error', 'Menu not found.');

        $menuItem = $item_id ? MenuItem::find($item_id) : null;
    	//prd($menu);

        if ($request->isMethod('post')) {
            return $this->saveMenuItems($request, $menu->id, $menuItem?->id);
        }

        return view('admin.menus.items', [
            'menu' => $menu,
            'menuItem' => $menuItem,
            'page_heading' => $menu->title,
        ]);
    }

    private function saveMenuItems(Request $request, $menuId, $itemId = null)
    {
        try {
            $rules = [
                'title' => 'required|string|max:255',
                'link_type' => 'required|string',
                'target' => 'required|string',
                'status' => 'required|integer|in:0,1',
            ];
            
            // Add conditional validation for page_id based on link_type
            if (in_array($request->link_type, ['cms', 'blog', 'news', 'event', 'category'])) {
                $rules['page_id'] = 'required|integer';
            } elseif (in_array($request->link_type, ['internal', 'external', 'custom'])) {
                $rules['url'] = 'required|string|max:500';
            }
            
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                \Log::error('Menu item validation failed:', $validator->errors()->toArray());
                return back()->withErrors($validator)->withInput();
            }

            $data = $request->except(['_token', 'item_id', 'id', 'back_url']);
            $backUrl = $request->back_url ?? url($this->ADMIN_ROUTE_NAME . '/menus/items/' . CustomHelper::encrypt($menuId));
            $data['slug'] = CustomHelper::GetSlug('menu_items', 'id', $itemId, $data['title']);
            $data['menu_id'] = $menuId;
            
            // Ensure proper data types
            $data['status'] = (int) $data['status'];
            $data['page_id'] = !empty($data['page_id']) ? (int) $data['page_id'] : null;
            $data['parent_id'] = !empty($data['parent_id']) ? (int) $data['parent_id'] : 0;
            
            // Set default sort_order if not provided
            if (empty($data['sort_order'])) {
                $maxOrder = MenuItem::where('menu_id', $menuId)->max('sort_order');
                $data['sort_order'] = $maxOrder ? $maxOrder + 1 : 1;
            }

            \Log::info('Saving menu item data:', $data);

            // Use the approach from your old working code
            $menuItem = new MenuItem;

            if (is_numeric($itemId) && $itemId > 0) {
                $exist = MenuItem::find($itemId);
                if (isset($exist->id) && $exist->id == $itemId) {
                    $menuItem = $exist;
                }
            }

            $menuItem->menu_id = $menuId;

            // Set each field manually like in your old code
            foreach ($data as $key => $val) {
                $menuItem->$key = $val;
            }

            $isSaved = $menuItem->save();

            if ($isSaved) {
                // Clear menu cache after update
                MenuHelper::clearMenuCache();
                return redirect($backUrl)->with('success', 'Menu item has been saved successfully.');
            } else {
                return back()->withInput()->with('error', 'Something went wrong, please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Menu item save error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong. ' . $e->getMessage());
        }
    }

    public function ajaxGetLinkTypeList(Request $request)
    {
        $linkType = $request->link_type;
        $pageId = $request->page_id;

        $response = ['success' => false];

        if (in_array($linkType, ['cms', 'service', 'portfolio', 'blog', 'news', 'event', 'category'])) {
            $pages = MenuHelper::getAvailablePages($linkType);

            $listHtml = view('admin.menus._link_type_list', [
                'link_type' => $linkType,
                'page_id' => $pageId,
                'pages' => $pages,
            ])->render();

            return response()->json(['success' => true, 'list' => $listHtml]);
        }

        return response()->json($response);
    }

    public function ajaxUpdateItems(Request $request)
    {
        try {
            // Simple approach - just update sort_order based on position
            $serializedData = $request->all();
            
            \Log::info('=== MENU UPDATE DEBUG START ===');
            \Log::info('Received data:', $serializedData);
            \Log::info('Request method: ' . $request->method());
            \Log::info('Content type: ' . $request->header('Content-Type'));
            \Log::info('Raw input: ' . $request->getContent());
            
            $updatedItems = [];
            $order = 1;
            
            // Extract item IDs from the payload - handle JSON structure
            $itemIds = [];
            $orders = [];
            
            if (isset($serializedData['item_id']) && is_array($serializedData['item_id'])) {
                foreach ($serializedData['item_id'] as $id => $value) {
                    $itemIds[] = (int) $id;
                }
            }
            
            if (isset($serializedData['order']) && is_array($serializedData['order'])) {
                foreach ($serializedData['order'] as $id => $order) {
                    $orders[(int) $id] = (int) $order;
                }
            }
            
            \Log::info('Item IDs to update:', $itemIds);
            \Log::info('Orders mapping:', $orders);
            
            // Update each item with the order from the payload
            foreach ($itemIds as $itemId) {
                $menuItem = MenuItem::find($itemId);
                if ($menuItem) {
                    $newOrder = isset($orders[$itemId]) ? $orders[$itemId] : $order;
                    \Log::info("Updating item {$itemId} to order {$newOrder}");
                    
                    // Direct database update
                    $result = DB::table('menu_items')
                        ->where('id', $itemId)
                        ->update(['sort_order' => $newOrder]);
                    
                    if ($result) {
                        $updatedItems[] = $itemId;
                        \Log::info("Successfully updated item {$itemId} to order {$newOrder}");
                    } else {
                        \Log::error("Failed to update item {$itemId}");
                    }
                    
                    $order++;
                }
            }
            
            \Log::info("Updated items: " . implode(', ', $updatedItems));
            
            // Clear menu cache
            MenuHelper::clearMenuCache();
            
            return response()->json(['success' => true, 'msg' => 'Menu items updated successfully']);
            
        } catch (\Exception $e) {
            \Log::error('Menu update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function ajaxDeleteItem(Request $request)
    {
        try {
            $itemId = (int) $request->id;

            $menuItem = MenuItem::find($itemId);

            if ($menuItem) {
                $menuItem->delete();
                MenuItem::where('parent_id', $itemId)->delete();

                // Clear menu cache after deletion
                MenuHelper::clearMenuCache();

                $msg = 'Menu item(s) deleted successfully.';
                return response()->json(['success' => true, 'msg' => $msg]);
            }

            return response()->json(['success' => false]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
}
