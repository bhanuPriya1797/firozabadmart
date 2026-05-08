<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Service;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Helpers\CustomHelper;
use App\Helpers\FaqHelper;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Validator;
use App\Models\Cms;

class FaqController extends Controller
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
                $faqs = Faq::with('category')->select('id', 'question', 'answer', 'category_id', 'status', 'sort_order', 'created_at');

                // Apply filters
                if ($request->filled('status')) {
                    $faqs->where('status', $request->status);
                }
                
                if ($request->filled('category_id')) {
                    $faqs->where('category_id', $request->category_id);
                }

                return DataTables::of($faqs)
                    ->addColumn('category_name', fn($row) => optional($row->category)->name ?? '-')
                    ->addColumn('status_label', fn($row) =>
                        $row->status
                            ? '<span class="badge bg-label-primary">Active</span>'
                            : '<span class="badge bg-label-danger">Inactive</span>'
                    )
                    ->addColumn('question_short', function ($row) {
                        return strlen($row->question) > 50 ? substr($row->question, 0, 50) . '...' : $row->question;
                    })
                    ->addColumn('answer_short', function ($row) {
                        return strlen($row->answer) > 100 ? substr(strip_tags($row->answer), 0, 100) . '...' : strip_tags($row->answer);
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.faqs.edit', ['id' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.faqs.delete', $encryptedId);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('faqs.edit')) || 
                            (auth()->user()->can('faqs.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // Edit permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('faqs.edit'))) {
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
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('faqs.delete'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-faq"
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

            $categories = FaqCategory::active()->ordered()->pluck('name', 'id')->toArray();
            return view('admin.faqs.index', compact('categories'));
        } catch (\Exception $e) {
            \Log::error('FaqController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading FAQs list.');
        }
    }

    public function add(Request $request, $id = 0)
    {
        try {
            $faq = $id ? Faq::findOrFail(CustomHelper::decrypt($id)) : new Faq;
            $categories = FaqCategory::active()->ordered()->pluck('name', 'id')->toArray();

            if ($request->isMethod('post')) {
                $rules = [
                    'question' => 'required|string|max:500',
                    'answer' => 'required|string',
                    'category_id' => 'required|exists:faq_categories,id',
                    'status' => 'required|in:0,1',
                    'sort_order' => 'nullable|integer|min:0',
                    'page_type' => 'nullable|in:service,blog,category,cms',
                    'page_ids' => 'nullable|array|required_with:page_type',
                    'page_ids.*' => 'integer|min:0',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $data = $request->except(['_token']);

                $pageType = $request->get('page_type');
                $pageIds = $request->get('page_ids', []);
                if (is_array($pageIds) && in_array(0, $pageIds)) {
                    $pageIds = [0];
                }

                if ($pageType && is_array($pageIds) && count($pageIds) > 0) {
                    if ($id) {
                        // Update current FAQ to first selection
                        $firstId = (int) $pageIds[0];
                        $faq->fill(array_merge($data, ['page_type' => $pageType, 'page_id' => $firstId]));
                        $faq->save();
                        // Create clones for the rest
                        foreach (array_slice($pageIds, 1) as $pid) {
                            $clone = $faq->replicate();
                            $clone->page_type = $pageType;
                            $clone->page_id = (int) $pid;
                            $clone->save();
                        }
                    } else {
                        // Create one FAQ per selected page
                        $createdAny = false;
                        foreach ($pageIds as $pid) {
                            $newFaq = new Faq();
                            $newFaq->fill(array_merge($data, ['page_type' => $pageType, 'page_id' => (int) $pid]));
                            $newFaq->save();
                            $createdAny = true;
                        }
                        // Use last created as reference for logging
                        if ($createdAny) {
                            $faq = $newFaq;
                        }
                    }
                } else {
                    // No page assignment or single legacy field
                    $faq->fill($data);
                    $faq->save();
                }

                // Clear FAQ cache
                FaqHelper::clearCache();

                // Log
                CustomHelper::recordActionLog(
                    url()->current(),
                    'faqs',
                    $faq->id,
                    $id ? 'Edit FAQ' : 'Add FAQ',
                    ($id ? 'Updated' : 'Created') . ' FAQ: ' . $request->question,
                    json_encode($data)
                );

                return redirect()->route($this->ADMIN_ROUTE_NAME . '.faqs.index')
                    ->with('success', 'FAQ has been saved successfully.');
            }
            
            $page_heading = $id ? "Edit FAQ" : "Add FAQ";
            
            return view('admin.faqs.form', compact('faq', 'categories', 'page_heading'));
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $faq = Faq::findOrFail($id);

            $faq->delete();

            // Clear FAQ cache
            FaqHelper::clearCache();

            CustomHelper::recordActionLog(
                url()->current(),
                'faqs',
                $id,
                'Delete FAQ',
                'Deleted FAQ: ' . $faq->question,
                'Deleted FAQ: ' . $faq->question
            );

            return back()->with('success', 'FAQ has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function ajaxPages(Request $request)
    {
        try {
            $type = $request->get('type');
            $options = [];

            if ($type === 'service') {
                $items = Service::active()->ordered()->get(['id', 'title']);
                foreach ($items as $it) {
                    $options[] = ['id' => $it->id, 'label' => $it->title];
                }
            } elseif ($type === 'blog') {
                $items = Blog::where('content_type', 'blog')->where('status', 1)->orderBy('created_at', 'desc')->get(['id', 'title']);
                foreach ($items as $it) {
                    $options[] = ['id' => $it->id, 'label' => $it->title];
                }
            } elseif ($type === 'category') {
                $items = BlogCategory::orderBy('name', 'asc')->get(['id', 'name']);
                foreach ($items as $it) {
                    $options[] = ['id' => $it->id, 'label' => $it->name];
                }
            } elseif ($type === 'cms') {
                $items = Cms::active()->orderBy('sort_order', 'asc')->orderBy('title', 'asc')->get(['id', 'title']);
                // Add All CMS Pages option at top
                $options[] = ['id' => 0, 'label' => 'All CMS Pages'];
                foreach ($items as $it) {
                    $options[] = ['id' => $it->id, 'label' => $it->title];
                }
            }

            return response()->json(['success' => true, 'options' => $options]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
