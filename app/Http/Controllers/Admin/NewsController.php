<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
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
                $news = Blog::with('category')->where('content_type', 'news')->select('id', 'title', 'post_by', 'brief', 'featured', 'status', 'category_id', 'content_type');

                // Apply filters
                if ($request->filled('status')) {
                    $news->where('status', $request->status);
                }
                
                if ($request->filled('featured')) {
                    $news->where('featured', $request->featured);
                }

                return DataTables::of($news)
                    ->addColumn('category_name', fn($row) => optional($row->Category)->name ?? '-')
                    ->addColumn('post_by', fn($row) => optional($row->User)->name ?? '-')
                    ->addColumn('featured_status', fn($row) =>
                        $row->featured
                            ? '<span class="badge bg-label-success">Yes</span>'
                            : '<span class="badge bg-label-secondary">No</span>'
                    )
                    ->addColumn('status_label', fn($row) =>
                        $row->status
                            ? '<span class="badge bg-label-primary">Active</span>'
                            : '<span class="badge bg-label-danger">Inactive</span>'
                    )
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.news.edit', ['id' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.news.delete', $encryptedId);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (Gate::allows('news.view') || Gate::allows('news.edit') || Gate::allows('news.delete')) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // View permission
                            if (Gate::allows('news.view')) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect" 
                                    href="' . route($this->ADMIN_ROUTE_NAME . '.news.view', ['id' => $encryptedId]) . '"
                                    data-id="' . $row->id . '"
                                    data-title="Preview"
                                >
                                    <i class="ti tabler-eye me-1"></i> Preview
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Edit permission
                            if (Gate::allows('news.edit')) {
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
                            if (Gate::allows('news.delete')) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-news"
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
                    ->rawColumns(['featured_status', 'status_label', 'action'])
                    ->make(true);
            }

            return view('admin.news.index');
        } catch (\Exception $e) {
            Log::error('NewsController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading news list.');
        }
    }

    public function add(Request $request, $id = 0)
    {
        try {
            $news = $id ? Blog::findOrFail(CustomHelper::decrypt($id)) : new Blog;

            if ($request->isMethod('post')) {
                $rules = [
                    'title' => 'required|string|max:255',
                    'status' => 'required|in:0,1',
                    'image' => 'nullable|image|mimes:jpeg,jpg,png',
                    'blog_date' => 'nullable|date',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $data = $request->except(['_token', 'image', 'image_media_path']);
                $data['content_type'] = 'news'; // Always set to news
                $data['category_id'] = null; // News doesn't need category

                // Auto-save current date if not provided
                if (empty($data['blog_date'])) {
                    $data['blog_date'] = date('Y-m-d');
                }

                // Slug
                $data['slug'] = CustomHelper::GetSlug('blogs', 'id', $id, $request->title);

                // Image upload
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $extension = $file->getClientOriginalExtension();
                    $data['image'] = $file->storeAs('uploads/blogs', "$filename.$extension", 'public');
                } elseif ($request->filled('image_media_path')) {
                    // Use image from Media Manager
                    $data['image'] = $request->image_media_path;
                }

                $data['post_by'] = Auth::id();

                $news->fill($data);
                $news->save();

                // Log
                CustomHelper::recordActionLog(
                    url()->current(),
                    'blogs',
                    $news->id,
                    $id ? 'Edit News' : 'Add News',
                    ($id ? 'Updated' : 'Created') . ' news: ' . $request->title,
                    json_encode($data)
                );

                return redirect()->route($this->ADMIN_ROUTE_NAME . '.news.index')
                    ->with('success', 'News has been saved successfully.');
            }
            
            $page_heading = $id ? "Edit News" : "Add News";
            
            return view('admin.news.form', compact('news', 'page_heading'));
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $news = Blog::findOrFail($id);

            // Remove image
            /*
            // STOPPING physical deletion
            if (!empty($news->image)) {
                Storage::delete($news->image);
            }
            */
            
            $news->delete();

            CustomHelper::recordActionLog(
                url()->current(),
                'blogs',
                $id,
                'Delete News',
                'Deleted news: ' . $news->title,
                'Deleted news: ' . $news->title
            );

            return back()->with('success', 'News has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function ajax_delete_image(Request $request){
        $response = [];
        $response['success'] = false;

        $message = '';
        $id = (isset($request->id))?$request->id:0;
        $is_deleted = 0;

        if(is_numeric($id) && $id > 0){

            $news = Blog::find($id);
            if(!empty($news)){

                $storage = Storage::disk('public');
                $is_deleted = true; // Assume deleted for DB update
                
                /*
                // STOPPING physical deletion
                $image = $news->image;
                if(!empty($image) && $storage->exists($image)){
                    $is_deleted = $storage->delete($image);
                }
                */

                if($is_deleted){
                    $news->image = '';
                    $news->save();
                }
            }

            if($is_deleted){
                $response['success'] = true;
                $message = 'Image has been deleted succesfully.';
            }
            else{
                $message = 'Something went wrong, please try again...';
            }

            $response['message'] = $message;
            return response()->json($response);
        }
    }

    public function view($id)
    {
        try {
            $decryptedId = CustomHelper::decrypt($id);
            $news = Blog::with('category')->findOrFail($decryptedId);
            return view('admin.news.view', compact('news'))->with('ADMIN_ROUTE_NAME', $this->ADMIN_ROUTE_NAME);
        } catch (\Exception $e) {
            return back()->with('error', 'News not found.');
        }
    }
}
