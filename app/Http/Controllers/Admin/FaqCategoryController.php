<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use App\Helpers\CustomHelper;
use App\Helpers\FaqHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Validator;

class FaqCategoryController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $categories = FaqCategory::select('id', 'name', 'slug', 'description', 'status', 'sort_order', 'created_at');

                return DataTables::of($categories)
                    ->addColumn('status_label', fn($row) =>
                        $row->status
                            ? '<span class="badge bg-label-primary">Active</span>'
                            : '<span class="badge bg-label-danger">Inactive</span>'
                    )
                    ->addColumn('faqs_count', fn($row) => $row->faqs_count)
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.faq-categories.edit', ['id' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.faq-categories.delete', $encryptedId);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('faq_categories.edit')) || 
                            (auth()->user()->can('faq_categories.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // Edit permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('faq_categories.edit'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect" 
                                    href="' . $editUrl . '"
                                    data-id="' . $row->id . '"
                                    data-title="Edit"
                                >
                                    <i class="ti tabler-pencil me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('faq_categories.delete'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-faq-category"
                                    href="javascript:void(0);"
                                    data-url="' . $deleteUrl . '"
                                    data-id="' . $row->id . '"
                                >
                                    <i class="ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }

                            $actions .= '</div></div>';
                        }

                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['status_label', 'action'])
                    ->make(true);
            }

            return view('admin.faq-categories.index');
        } catch (\Exception $e) {
            \Log::error('FaqCategoryController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading FAQ categories list.');
        }
    }

    public function add(Request $request, $id = 0)
    {
        try {
            $category = $id ? FaqCategory::findOrFail(CustomHelper::decrypt($id)) : new FaqCategory;

            if ($request->isMethod('post')) {
                $rules = [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'status' => 'required|in:0,1',
                    'sort_order' => 'nullable|integer|min:0',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $data = $request->except(['_token']);

                // Slug logic
                if (!$id) {
                    // New record: use provided slug or generate from name
                    if (!empty($request->slug)) {
                        $data['slug'] = CustomHelper::GetSlug('faq_categories', 'id', $id, $request->slug);
                    } else {
                        $data['slug'] = CustomHelper::GetSlug('faq_categories', 'id', $id, $request->name);
                    }
                } else {
                    // Existing record: only update slug if slug field is provided
                    if (!empty($request->slug) && $request->slug !== $category->slug) {
                        $data['slug'] = CustomHelper::GetSlug('faq_categories', 'id', $id, $request->slug);
                    }
                }

                $category->fill($data);
                $category->save();

                // Clear FAQ cache
                FaqHelper::clearCache();

                // Log
                CustomHelper::recordActionLog(
                    url()->current(),
                    'faq_categories',
                    $category->id,
                    $id ? 'Edit FAQ Category' : 'Add FAQ Category',
                    ($id ? 'Updated' : 'Created') . ' FAQ category: ' . $request->name,
                    json_encode($data)
                );

                return redirect()->route($this->ADMIN_ROUTE_NAME . '.faq-categories.index')
                    ->with('success', 'FAQ category has been saved successfully.');
            }
            
            $page_heading = $id ? "Edit FAQ Category" : "Add FAQ Category";
            
            return view('admin.faq-categories.form', compact('category', 'page_heading'));
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $category = FaqCategory::findOrFail($id);

            // Check if category has FAQs
            if ($category->faqs()->count() > 0) {
                return back()->with('error', 'Cannot delete category. It contains FAQs. Please delete the FAQs first.');
            }

            $category->delete();

            // Clear FAQ cache
            FaqHelper::clearCache();

            CustomHelper::recordActionLog(
                url()->current(),
                'faq_categories',
                $id,
                'Delete FAQ Category',
                'Deleted FAQ category: ' . $category->name,
                'Deleted FAQ category: ' . $category->name
            );

            return back()->with('success', 'FAQ category has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
