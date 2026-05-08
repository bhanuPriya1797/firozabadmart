<?php

namespace App\Http\Controllers\Admin;

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\CustomHelper;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Validator;

class BlogCategoryController extends Controller
{
    private $limit;
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = BlogCategory::withCount('blogs');

                // Apply filters
                if (!empty($request->name)) {
                    $query->where('name', 'like', '%' . $request->name . '%');
                }

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }

                return DataTables::of($query)
                    ->addIndexColumn()

                    // Blog Category Name
                    ->editColumn('name', function ($row) {
                        return e($row->name);
                    })

                    // Blog Count
                    ->editColumn('blogs_count', function ($row) {
                        return $row->blogs_count;
                    })

                    // Status Badge
                    ->editColumn('status', function ($row) {
                        return $row->status == 1
                            ? '<span class="badge bg-label-primary">Active</span>'
                            : '<span class="badge bg-label-danger">Inactive</span>';
                    })

                    // Action Buttons
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.blogs_categories.add', ['id' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.blogs_categories.delete', $encryptedId);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('blog_categories.edit')) || 
                            (auth()->user()->can('blog_categories.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // Edit permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('blog_categories.edit'))) {
                                $actions .= '<a class="dropdown-item" href="' . $editUrl . '" data-id="' . $row->id . '">
                                    <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('blog_categories.delete'))) {
                                $actions .= '<a class="dropdown-item btn-delete-blog" href="javascript:void(0);" data-url="' . $deleteUrl . '" data-id="' . $row->id . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }

                            $actions .= '</div></div>';
                        }

                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->editColumn('created_at', fn($row) => Carbon::parse($row->created_at)->format('d, M Y h:i A'))
                    ->rawColumns(['status', 'action'])
                    ->make(true);

            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Something went wrong!',
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        return view('admin.blogs_categories.index', [
            'page_heading' => 'Blog Categories'
        ]);
    }

    public function add(Request $request)
    {
        try {
            $id = !empty($request->id) ? CustomHelper::decrypt($request->id) : 0;
            $category = $id ? BlogCategory::findOrFail($id) : new BlogCategory();

            if ($request->isMethod('post')) {
                $rules = [
                    'name' => 'required|max:255',
                    'status' => 'required|in:0,1',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $slug = CustomHelper::GetSlug('blog_categories', 'id', $id, $request->name);

                $data = $request->only(['name', 'status', 'meta_title', 'meta_keyword', 'meta_description', 'sort_order']);
                $data['slug'] = $slug;

                if ($id) {
                    $category->update($data);
                    $logAction = 'Edit On Blog Category List';
                } else {
                    $category = BlogCategory::create($data);
                    $id = $category->id;
                    $logAction = 'Add On Blog Category List';
                }

                CustomHelper::recordActionLog(
                    url()->current(),
                    'blog_categories',
                    $id,
                    $logAction,
                    $logAction . ' (' . $request->name . ')',
                    json_encode($data)
                );

                return redirect()->route($this->ADMIN_ROUTE_NAME . '.blogs_categories.index')
                    ->with('success', 'Blog category saved successfully.');
            }

            $heading = $id ? 'Edit Blog Category - ' . $category->name : 'Add Blog Category';

            return view('admin.blogs_categories.form', compact('category', 'id') + ['page_heading' => $heading]);
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $id = !empty($id) ? CustomHelper::decrypt($id) : 0;
            $category = BlogCategory::findOrFail($id);

            // Check for associated blogs
            if ($category->blogs()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This category has associated blogs. Remove them first.'
                ], 400);
            }

            // Check for child categories
            if ($category->children()->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This category has sub-categories. Remove them first.'
                ], 400);
            }

            $categoryName = $category->name;
            $category->delete();

            // Log the action
            CustomHelper::recordActionLog(
                url()->current(),
                'blog_categories',
                $id,
                'Delete Blog Category',
                'Deleted Blog Category (' . $categoryName . ')',
                'Deleted Blog Category (' . $categoryName . ')'
            );

            return response()->json([
                'status' => true,
                'message' => 'Blog category deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function categories_view(Request $request)
    {
        $id = !empty($request->id) ? CustomHelper::decrypt($request->id) : 0;
        $category = BlogCategory::find($id);
        $title = 'Blog Category View';

        return view('admin.blogs_categories.view', compact('category', 'id') + ['page_heading' => $title]);
    }
}