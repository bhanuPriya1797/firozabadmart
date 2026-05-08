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

class EventsController extends Controller
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
                $events = Blog::with('category')->where('content_type', 'event')->select('id', 'title', 'post_by', 'brief', 'featured', 'status', 'category_id', 'content_type');

                // Apply filters
                if ($request->filled('status')) {
                    $events->where('status', $request->status);
                }
                
                if ($request->filled('featured')) {
                    $events->where('featured', $request->featured);
                }

                return DataTables::of($events)
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
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.events.edit', ['id' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.events.delete', $encryptedId);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (Gate::allows('events.view') || Gate::allows('events.edit') || Gate::allows('events.delete')) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // View permission
                            if (Gate::allows('events.view')) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect" 
                                    href="' . route($this->ADMIN_ROUTE_NAME . '.events.view', ['id' => $encryptedId]) . '"
                                    data-id="' . $row->id . '"
                                    data-title="Preview"
                                >
                                    <i class="ti tabler-eye me-1"></i> Preview
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Edit permission
                            if (Gate::allows('events.edit')) {
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
                            if (Gate::allows('events.delete')) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-event"
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

            return view('admin.events.index');
        } catch (\Exception $e) {
            Log::error('EventsController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while loading events list.');
        }
    }

    public function add(Request $request, $id = 0)
    {
        try {
            $event = $id ? Blog::findOrFail(CustomHelper::decrypt($id)) : new Blog;

            if ($request->isMethod('post')) {
                $rules = [
                    'title' => 'required|string|max:255',
                    'status' => 'required|in:0,1',
                    'image' => 'nullable|image|mimes:jpeg,jpg,png',
                    'team_image_1' => 'nullable|image|mimes:jpeg,jpg,png',
                    'team_image_2' => 'nullable|image|mimes:jpeg,jpg,png',
                    'blog_date' => 'nullable|date',
                    'end_date' => 'nullable|date|after_or_equal:blog_date',
                ];

                $validator = Validator::make($request->all(), $rules);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                $data = $request->except(['_token', 'image', 'image_media_path', 'team_image_1', 'team_image_1_media_path', 'team_image_2', 'team_image_2_media_path']);
                $data['content_type'] = 'event'; // Always set to event
                $data['category_id'] = null; // Events don't need category

                // Auto-save current date if not provided
                if (empty($data['blog_date'])) {
                    $data['blog_date'] = date('Y-m-d');
                }
                
                // For Events, we might want end_date to default to start_date if not provided, or leave null. 
                // User said "auto saved current date if not updated the date by form". 
                // I'll apply it to blog_date. For end_date, maybe default to blog_date? 
                // Let's just default blog_date to current. end_date is optional.
                
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
                    if (!empty($request->slug) && $request->slug !== $event->slug) {
                        $data['slug'] = CustomHelper::GetSlug('blogs', 'id', $id, $request->slug);
                    }
                }

                // Image handling
                $oldImage = $event->image;
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $path = 'events/';
                    $thumbPath = 'events/thumb/';
                    $mediumPath = 'events/medium/';
                    $width = (int)CustomHelper::getSetting('BLOG_IMG_WIDTH') ?: 1600;
                    $height = (int)CustomHelper::getSetting('BLOG_IMG_HEIGHT') ?: 600;
                    $thumbWidth = (int)CustomHelper::getSetting('BLOG_IMG_THUMB_WIDTH') ?: 400;
                    $thumbHeight = (int)CustomHelper::getSetting('BLOG_IMG_THUMB_HEIGHT') ?: 300;
                    $mediumWidth = (int)CustomHelper::getSetting('BLOG_IMG_MEDIUM_WIDTH') ?: 800;
                    $mediumHeight = (int)CustomHelper::getSetting('BLOG_IMG_MEDIUM_HEIGHT') ?: 600;

                    $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
                    if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                        $data['image'] = $uploaded['file_name'];
                    }
                } elseif ($request->filled('image_media_path')) {
                    // Use image from Media Manager
                    $data['image'] = $request->image_media_path;
                }

                // Team images handling (optional)
                if ($request->hasFile('team_image_1')) {
                    $file = $request->file('team_image_1');
                    $uploaded = CustomHelper::UploadImage(
                        $file,
                        'events/teams/',
                        '',
                        800,
                        800,
                        true,
                        'events/teams/thumb/',
                        300,
                        300,
                        false
                    );
                    if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                        $data['team_image_1'] = 'events/teams/' . $uploaded['file_name'];
                    }
                } elseif ($request->filled('team_image_1_media_path')) {
                    $data['team_image_1'] = $request->team_image_1_media_path;
                }
                if ($request->hasFile('team_image_2')) {
                    $file = $request->file('team_image_2');
                    $uploaded = CustomHelper::UploadImage(
                        $file,
                        'events/teams/',
                        '',
                        800,
                        800,
                        true,
                        'events/teams/thumb/',
                        300,
                        300,
                        false
                    );
                    if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                        $data['team_image_2'] = 'events/teams/' . $uploaded['file_name'];
                    }
                } elseif ($request->filled('team_image_2_media_path')) {
                    $data['team_image_2'] = $request->team_image_2_media_path;
                }

                // If image changed and there was an old image, delete it?
                // The original code had deletion logic. I should probably preserve it or adapt it.
                // But with Media Manager, we might be reusing images, so deleting might be risky if we just switch.
                // However, standard upload creates a new file.
                // Let's stick to: if new image is set, we overwrite.
                
                $data['post_by'] = Auth::id();

                $event->fill($data);
                $event->save();

                // Log
                CustomHelper::recordActionLog(
                    url()->current(),
                    'blogs',
                    $event->id,
                    $id ? 'Edit Event' : 'Add Event',
                    ($id ? 'Updated' : 'Created') . ' event: ' . $request->title,
                    json_encode($data)
                );

                return redirect()->route($this->ADMIN_ROUTE_NAME . '.events.index')
                    ->with('success', 'Event has been saved successfully.');
            }
            
            $page_heading = $id ? "Edit Event" : "Add Event";
            
            return view('admin.events.form', compact('event', 'page_heading'));
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $event = Blog::findOrFail($id);

            // Remove image
            if (!empty($event->image)) {
                $this->deleteImageFiles($event->image);
            }

            $event->delete();

            CustomHelper::recordActionLog(
                url()->current(),
                'blogs',
                $id,
                'Delete Event',
                'Deleted event: ' . $event->title,
                'Deleted event: ' . $event->title
            );

            return back()->with('success', 'Event has been deleted successfully.');
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

            $event = Blog::find($id);
            if(!empty($event)){

                $storage = Storage::disk('public');

                $image = $event->image;
                if(!empty($image)){
                    $is_deleted = $this->deleteImageFiles($image);
                }

                if($is_deleted){
                    $event->image = '';
                    $event->save();
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

    private function deleteImageFiles($file)
    {
        if (empty($file)) return false;

        /*
        // STOPPING physical deletion as per user request
        
        // Old path support
        if (Str::startsWith($file, 'uploads/blogs/')) {
            return Storage::disk('public')->exists($file) ? Storage::disk('public')->delete($file) : false;
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

    public function view($id)
    {
        try {
            $decryptedId = CustomHelper::decrypt($id);
            $event = Blog::with('category')->findOrFail($decryptedId);
            return view('admin.events.view', compact('event'))->with('ADMIN_ROUTE_NAME', $this->ADMIN_ROUTE_NAME);
        } catch (\Exception $e) {
            return back()->with('error', 'Event not found.');
        }
    }
}
