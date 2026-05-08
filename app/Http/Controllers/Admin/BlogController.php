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
use App\Models\Media;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
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
                $blogs = Blog::with('category')->where('content_type', 'blog')->select('id', 'title', 'post_by', 'brief', 'featured', 'status', 'category_id');

                // Apply filters
                if ($request->filled('status')) {
                    $blogs->where('status', $request->status);
                }
                
                if ($request->filled('featured')) {
                    $blogs->where('featured', $request->featured);
                }
                
                if ($request->filled('category_id')) {
                    $blogs->where('category_id', $request->category_id);
                }

                $user = Auth::user();
                return DataTables::of($blogs)
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
                    ->addColumn('action', function ($row) use ($user) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.blogs.edit', ['id' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.blogs.delete', $encryptedId);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (Gate::allows('blogs.view') || Gate::allows('blogs.edit') || Gate::allows('blogs.delete')) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // View permission
                            if (Gate::allows('blogs.view')) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect" 
                                    href="' . route($this->ADMIN_ROUTE_NAME . '.blogs.view', ['id' => $encryptedId]) . '"
                                    data-id="' . $row->id . '"
                                    data-title="Preview"
                                >
                                    <i class="ti tabler-eye me-1"></i> Preview
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Edit permission
                            if (Gate::allows('blogs.edit')) {
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
                            if (Gate::allows('blogs.delete')) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-blog"
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

            return view('admin.blogs.index');
        } catch (\Exception $e) {
            Log::error('BlogController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading blog list.');
        }
    }

    public function add(Request $request, $id = 0)
    {
        try {
            $blog = $id ? Blog::findOrFail(CustomHelper::decrypt($id)) : new Blog;
            $categories = BlogCategory::where('status', 1)->pluck('name', 'id')->toArray();

            if ($request->isMethod('post')) {
                $rules = [
                    'title' => 'required|string|max:255',
                    'category_id' => 'required',
                    'status' => 'required|in:0,1',
                    'image' => 'nullable|image|mimes:jpeg,jpg,png',
                    'blog_date' => 'nullable|date',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $data = $request->except(['_token', 'image', 'image_media_path']);
                $data['content_type'] = 'blog'; // Always set to blog

                // Auto-save current date if not provided
                if (empty($data['blog_date'])) {
                    $data['blog_date'] = date('Y-m-d');
                }

                // Slug logic
                if (!$id) {
                    // New record: use provided slug or generate from title
                    if (!empty($request->slug)) {
                        $data['slug'] = CustomHelper::GetSlug('blogs', 'id', $id, $request->slug);
                    } else {
                        $data['slug'] = CustomHelper::GetSlug('blogs', 'id', $id, $request->title);
                    }
                } else {
                    // Existing record: only update slug if slug field is provided
                    if (!empty($request->slug) && $request->slug !== $blog->slug) {
                        $data['slug'] = CustomHelper::GetSlug('blogs', 'id', $id, $request->slug);
                    }
                }

                // Image upload
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $path = 'blogs/';
                    $thumbPath = 'blogs/thumb/';
                    $mediumPath = 'blogs/medium/';
                    $width = (int)CustomHelper::getSetting('BLOG_IMG_WIDTH') ?: 1600;
                    $height = (int)CustomHelper::getSetting('BLOG_IMG_HEIGHT') ?: 600;
                    $thumbWidth = (int)CustomHelper::getSetting('BLOG_IMG_THUMB_WIDTH') ?: 400;
                    $thumbHeight = (int)CustomHelper::getSetting('BLOG_IMG_THUMB_HEIGHT') ?: 300;
                    $mediumWidth = (int)CustomHelper::getSetting('BLOG_IMG_MEDIUM_WIDTH') ?: 800;
                    $mediumHeight = (int)CustomHelper::getSetting('BLOG_IMG_MEDIUM_HEIGHT') ?: 600;

                    $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
                    if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                        // if ($id && $blog->image) {
                        //     $this->deleteImageFiles($blog->image);
                        // }
                        $data['image'] = $uploaded['file_name'];
                        
                        // Register to Media Manager
                        try {
                            $mediaPath = $path . $uploaded['file_name'];
                            if(Storage::disk('public')->exists($mediaPath)){
                                $ext = strtolower(pathinfo($mediaPath, PATHINFO_EXTENSION));
                                $mime = $ext === 'jpg' || $ext === 'jpeg' ? 'image/jpeg' : ($ext === 'png' ? 'image/png' : ($ext === 'gif' ? 'image/gif' : ($ext === 'webp' ? 'image/webp' : 'application/octet-stream')));
                                $size = Storage::disk('public')->size($mediaPath);
                                Media::create([
                                    'name' => $uploaded['file_name'],
                                    'file_name' => $uploaded['file_name'],
                                    'path' => $mediaPath,
                                    'disk' => 'public',
                                    'mime_type' => $mime,
                                    'size' => $size,
                                    'folder_id' => null,
                                    'user_id' => Auth::id() ?? null
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error("Failed to register blog upload to Media Manager: " . $e->getMessage());
                        }
                    }
                } elseif ($request->filled('image_media_path')) {
                    // Use image from Media Manager
                    $data['image'] = $request->image_media_path;
                }
                
                $data['post_by'] = Auth::id();

                $blog->fill($data);
                $blog->save();

                // Log
                CustomHelper::recordActionLog(
                    url()->current(),
                    'blogs',
                    $blog->id,
                    $id ? 'Edit Blog' : 'Add Blog',
                    ($id ? 'Updated' : 'Created') . ' blog: ' . $request->title,
                    json_encode($data)
                );

                return redirect()->route($this->ADMIN_ROUTE_NAME . '.blogs.index')
                    ->with('success', 'Blog has been saved successfully.');
            }
            
            $page_heading = $id ? "Edit Blog" : "Add Blog";
            
            return view('admin.blogs.form', compact('blog', 'categories', 'page_heading'));
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            // Remove image
            if (!empty($blog->image)) {
                $this->deleteImageFiles($blog->image);
            }

            $blog->delete();

            CustomHelper::recordActionLog(
                url()->current(),
                'blogs',
                $id,
                'Delete Blog',
                'Deleted blog: ' . $blog->title,
                'Deleted blog: ' . $blog->title
            );

            return back()->with('success', 'Blog has been deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function deleteImageFiles($file)
    {
        if (empty($file)) return false;

        // Do not delete if it is a Media Manager path (starts with media/)
        // However, we are storing just the filename or "blogs/filename" or "media/filename"
        // If it starts with "media/", skip deletion
        if (Str::startsWith($file, 'media/')) {
            return true; 
        }

        /*
        // STOPPING physical deletion as per user request to preserve Media Library files
        // We only want to remove the reference from the database
        
        // Check if it's old path
        if (Str::startsWith($file, 'uploads/blogs/')) {
             if (Storage::disk('public')->exists($file)) {
                return Storage::disk('public')->delete($file);
            }
            return false;
        }

        $path = 'blogs/' . $file;
        $thumb = 'blogs/thumb/' . $file;
        $medium = 'blogs/medium/' . $file;
        $disk = Storage::disk('public');
        $ok = true;
        
        if ($disk->exists($path)) {
            $ok = $disk->delete($path) && $ok;
        }
        if ($disk->exists($thumb)) {
            $ok = $disk->delete($thumb) && $ok;
        }
        if ($disk->exists($medium)) {
            $ok = $disk->delete($medium) && $ok;
        }
        return $ok;
        */
        return true;
    }

    /*delete image*/
    public function ajax_delete_image(Request $request){
        $response = [];
        $response['success'] = false;

        $message = '';
        $id = (isset($request->id))?$request->id:0;
        $is_deleted = 0;

        if(is_numeric($id) && $id > 0){

            $blog = Blog::find($id);
            if(!empty($blog)){

                $storage = Storage::disk('public');
                $is_deleted = true; // Assume success for database update purposes
                
                /*
                // STOPPING physical deletion
                $image = $blog->image;
                if(!empty($image) && $storage->exists($image)){
                    $is_deleted = $storage->delete($image);
                }
                */

                if($is_deleted){
                    $blog->image = '';
                    $blog->save();
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
            $blog = Blog::with('category')->findOrFail($decryptedId);
            return view('admin.blogs.view', compact('blog'))->with('ADMIN_ROUTE_NAME', $this->ADMIN_ROUTE_NAME);
        } catch (\Exception $e) {
            return back()->with('error', 'Blog not found.');
        }
    }

    public function blog_view($id)
    {
        try {
            $blog = Blog::with('category')->findOrFail($id);
            return view('admin.blogs.view', compact('blog'));
        } catch (\Exception $e) {
            return back()->with('error', 'Blog not found.');
        }
    }
}
