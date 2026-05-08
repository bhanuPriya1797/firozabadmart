<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\BannerImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

use App\Helpers\CustomHelper;

use Validator;
use Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use File;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class BannerController extends Controller{

    private $page_arr;
    private $limit;
    private $ADMIN_ROUTE_NAME;

    public function __construct(){
        $this->limit = 20;
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request){
        if ($request->ajax()) {
            $banners = Banner::with('images')->orderBy('sort_order', 'asc');
            
            return DataTables::of($banners)
                ->addColumn('type_label', function($banner) {
                    return $banner->type == 1 ? '<span class="badge bg-primary">Image</span>' : '<span class="badge bg-success">Video</span>';
                })
                ->addColumn('media_count', function($banner) {
                    if ($banner->type == 1) {
                        return $banner->images->count() . ' images';
                    } else {
                        return $banner->video ? '1 video' : 'No video';
                    }
                })
                ->addColumn('video_info', function($banner) {
                    if ($banner->type == 2 && $banner->video) {
                        if ($banner->video_type == 2 && $banner->video_embed) {
                            return '<span class="text-muted">Embed: ' . Str::limit($banner->video_embed, 30) . '</span>';
                        } else {
                            return '<span class="text-muted">File: ' . $banner->video . '</span>';
                        }
                    }
                    return '';
                })
                ->addColumn('status_label', function($banner) {
                    return $banner->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('created_at', function($banner) {
                    return $banner->created_at->format('M d, Y H:i');
                })
                ->addColumn('action', function($banner) {
                    $routeName = $this->ADMIN_ROUTE_NAME;
                    $actions = '<div class="d-flex gap-1">';
                    
                    // Edit permission
                    if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('banners.edit'))) {
                        $actions .= '<a href="' . route($routeName . '.banners.edit', CustomHelper::encrypt($banner->id)) . '" class="btn btn-sm btn-outline-primary" title="Edit Banner"><i class="icon-base ti tabler-edit"></i></a>';
                    }
                    
                    // Media management permission
                    if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('banners.media_manage'))) {
                        $actions .= '<a href="' . route($routeName . '.banners.media', CustomHelper::encrypt($banner->id)) . '" class="btn btn-sm btn-outline-info" title="Manage Media"><i class="icon-base ti tabler-photo"></i></a>';
                    }
                    
                    // Delete permission
                    if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('banners.delete'))) {
                        $actions .= '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-banner" data-url="' . route($routeName . '.banners.delete', CustomHelper::encrypt($banner->id)) . '" title="Delete Banner"><i class="icon-base ti tabler-trash"></i></button>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['type_label', 'status_label', 'action', 'video_info'])
                ->make(true);
        }

        return view('admin.banners.index');
    }

    public function add(Request $request){
        $banner_id = (isset($request->banner_id))?$request->banner_id:0;
        $banner = null;
        $banner_images = collect();
        $title = 'Add Banner';

        if(is_numeric($banner_id) && $banner_id > 0){
            $banner = Banner::find($banner_id);
            $banner_images = BannerImage::where('banner_id', $banner_id)->get();
            $title = 'Edit Banner';
        }
        
        if($request->method() == 'POST' || $request->method() == 'post'){
            
            $rules = [];
            $validation_msg = [];

            $rules['title'] = 'required|max:255';
            $rules['type'] = 'required';
            $rules['status'] = 'required';

            if(!empty($request->type) && $request->type==2){
                if(!empty($request->video_type) && $request->video_type==2){
                    $rules['video_embed'] = 'required';
                }
            }

            $validator = Validator::make($request->all(), $rules, $validation_msg);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $req_data = [];
            $req_data = $request->except(['_token', 'back_url', 'banner_id']);

            // Set default values for missing fields
            if(!isset($req_data['video_type'])) {
                $req_data['video_type'] = 0;
            }
            if(!isset($req_data['sort_order'])) {
                $req_data['sort_order'] = 0;
            }

            if(empty($banner_id)){
                $slug = CustomHelper::GetSlug('banners', 'id', $banner_id, $request->title);
            }
            else{
                $slug = CustomHelper::GetSlug('banners', 'id', $banner_id, $request->slug);
            }

            $req_data['slug'] = $slug;

            if(!empty($banner)){
                $isSaved = Banner::where('id', $banner->id)->update($req_data);
                $msg="The Banner has been updated successfully.";
            }
            else{
                $isSaved = Banner::create($req_data);
                $banner_id = $isSaved->id;
                $msg="The Banner has been added successfully.";
            }

            if ($isSaved) {
                cache()->forget('banners');
                return redirect(route($this->ADMIN_ROUTE_NAME.'.banners.index'))->with('alert-success', $msg);
            } else {
                return back()->with('alert-danger', 'The Banner cannot be added, please try again or contact the administrator.');
            }
        }
        
        $data = [];
        $data['page_heading'] = $title;
        $data['banner'] = $banner;
        $data['banner_images'] = $banner_images;
        $data['banner_id'] = $banner_id;

        return view('admin.banners.form', $data);
    }

    public function edit($encryptedId) {
        $id = CustomHelper::decrypt($encryptedId);
        $banner = Banner::findOrFail($id);
        $banner_images = BannerImage::where('banner_id', $id)->get();
        
        $data = [];
        $data['page_heading'] = 'Edit Banner';
        $data['banner'] = $banner;
        $data['banner_images'] = $banner_images;
        $data['banner_id'] = $id;

        return view('admin.banners.form', $data);
    }

    public function save(Request $request, $encryptedId = 0) {
        $rules = [];
        $validation_msg = [];

        $rules['title'] = 'required|max:255';
        $rules['type'] = 'required';
        $rules['status'] = 'required';

        if(!empty($request->type) && $request->type==2){
            if(!empty($request->video_type) && $request->video_type==2){
                $rules['video_embed'] = 'required';
            }
        }

        $validator = Validator::make($request->all(), $rules, $validation_msg);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $req_data = $request->except(['_token', 'banner_id']);

        // Set default values for missing fields
        if(!isset($req_data['video_type'])) {
            $req_data['video_type'] = 0;
        }
        if(!isset($req_data['sort_order'])) {
            $req_data['sort_order'] = 0;
        }

        if($encryptedId != 0){
            $id = CustomHelper::decrypt($encryptedId);
            $banner = Banner::findOrFail($id);
            $slug = CustomHelper::GetSlug('banners', 'id', $id, $request->title);
            $req_data['slug'] = $slug;
            
            $isSaved = $banner->update($req_data);
            $msg = "The Banner has been updated successfully.";
        } else {
            $slug = CustomHelper::GetSlug('banners', 'id', 0, $request->title);
            $req_data['slug'] = $slug;
            
            $isSaved = Banner::create($req_data);
            $msg = "The Banner has been added successfully.";
        }

        if ($isSaved) {
            // Log activity
            $bannerId = $encryptedId != 0 ? CustomHelper::decrypt($encryptedId) : $isSaved->id;
            $actionType = $encryptedId != 0 ? 'Update Banner' : 'Create Banner';
            $description = ($encryptedId != 0 ? 'Updated' : 'Created') . ' banner: ' . $request->title;
            
            CustomHelper::recordActionLog(
                url()->current(),
                'banners',
                $bannerId,
                $actionType,
                $description,
                json_encode($req_data)
            );
            
            cache()->forget('banners');
            return redirect(route($this->ADMIN_ROUTE_NAME.'.banners.index'))->with('alert-success', $msg);
        } else {
            return back()->with('alert-danger', 'The Banner cannot be saved, please try again.');
        }
    }

    public function delete($encryptedId) {
        try {
            $id = CustomHelper::decrypt($encryptedId);
            $banner = Banner::findOrFail($id);
            
            // Delete associated images
            foreach($banner->images as $image) {
                if(Storage::disk('public')->exists('banners/' . $image->image_name)) {
                    Storage::disk('public')->delete('banners/' . $image->image_name);
                }
                if(Storage::disk('public')->exists('banners/thumb/' . $image->image_name)) {
                    Storage::disk('public')->delete('banners/thumb/' . $image->image_name);
                }
            }
            
            // Delete banner video if exists
            if($banner->video && Storage::disk('public')->exists('banners/' . $banner->video)) {
                Storage::disk('public')->delete('banners/' . $banner->video);
            }
            
            $banner->delete();
            
            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'banners',
                $id,
                'Delete Banner',
                'Deleted banner: ' . $banner->title,
                'Deleted banner: ' . $banner->title . ' (ID: ' . $id . ')'
            );
            
            return response()->json([
                'status' => true,
                'message' => 'Banner deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting banner: ' . $e->getMessage()
            ]);
        }
    }

    public function addMediaFromLibrary(Request $request) {
        $request->validate([
            'banner_id' => 'required|integer|exists:banners,id',
            'file_path' => 'required|string'
        ]);

        try {
            $sourcePath = $request->file_path;
            
            // Handle if full URL is passed
            if (strpos($sourcePath, 'storage/') !== false) {
                $sourcePath = str_replace(asset('storage') . '/', '', $sourcePath);
                // Also handle if just /storage/ is passed
                $sourcePath = str_replace('/storage/', '', $sourcePath);
            }
            
            // Check if file exists in public disk
            if (!Storage::disk('public')->exists($sourcePath)) {
                // Try to strip leading slash if present
                $sourcePath = ltrim($sourcePath, '/');
                if (!Storage::disk('public')->exists($sourcePath)) {
                    return response()->json(['success' => false, 'message' => 'File not found in library: ' . $sourcePath]);
                }
            }

            // DO NOT COPY. Use the path directly.
            // Ensure path is relative to storage root (e.g. media/...)
            
            BannerImage::create([
                'banner_id' => $request->banner_id,
                'image_name' => $sourcePath,
                'title' => basename($sourcePath), // Default title
                'sub_title' => 'Image imported from Media Library',
                'sort_order' => BannerImage::where('banner_id', $request->banner_id)->max('sort_order') + 1
            ]);

            return response()->json(['success' => true, 'message' => 'Image added successfully!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function addVideoFromLibrary(Request $request) {
        $request->validate([
            'banner_id' => 'required|integer|exists:banners,id',
            'file_path' => 'required|string'
        ]);

        try {
            $sourcePath = $request->file_path;
            
            // Handle if full URL is passed
            if (strpos($sourcePath, 'storage/') !== false) {
                $sourcePath = str_replace(asset('storage') . '/', '', $sourcePath);
                $sourcePath = str_replace('/storage/', '', $sourcePath);
            }
            
            // Check if file exists in public disk
            if (!Storage::disk('public')->exists($sourcePath)) {
                $sourcePath = ltrim($sourcePath, '/');
                if (!Storage::disk('public')->exists($sourcePath)) {
                    return response()->json(['success' => false, 'message' => 'File not found in library: ' . $sourcePath]);
                }
            }

            $banner = Banner::find($request->banner_id);
            if($banner) {
                // Delete old video if exists
                if($banner->video && Storage::disk('public')->exists('banners/' . $banner->video)) {
                    Storage::disk('public')->delete('banners/' . $banner->video);
                }
                
                // Update banner with new video path (relative to storage root)
                $banner->update(['video' => $sourcePath]);
                
                return response()->json(['success' => true, 'message' => 'Video added successfully!']);
            }

            return response()->json(['success' => false, 'message' => 'Banner not found.']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function uploadImages(Request $request) {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'banner_id' => 'nullable|integer'
            ]);

            $file = $request->file('file');
            $banner_id = $request->banner_id;
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store the file
            $path = $file->storeAs('banners', $filename, 'public');
            
            // START: Media Manager Integration
            try {
                $mediaPath = 'banners/' . $filename;
                if(Storage::disk('public')->exists($mediaPath)){
                    $mime = Storage::disk('public')->mimeType($mediaPath);
                    $size = Storage::disk('public')->size($mediaPath);
                    
                    Media::create([
                        'name' => $filename,
                        'file_name' => $filename,
                        'path' => $mediaPath,
                        'disk' => 'public',
                        'mime_type' => $mime,
                        'size' => $size,
                        'folder_id' => null,
                        'user_id' => auth()->id() ?? null
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error("Failed to register Banner image upload to Media Manager: " . $e->getMessage());
            }
            // END: Media Manager Integration

            // Create thumbnail
            $this->createThumbnail($file, $filename);
            
            // If banner_id is provided, create banner image record
            if($banner_id) {
                BannerImage::create([
                    'banner_id' => $banner_id,
                    'image_name' => $filename,
                    'title' => 'Uploaded Image',
                    'sub_title' => 'Image uploaded via admin panel',
                    'sort_order' => BannerImage::where('banner_id', $banner_id)->max('sort_order') + 1
                ]);
            }
            
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'message' => 'Image uploaded successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ]);
        }
    }

    public function uploadVideo(Request $request) {
        try {
            $request->validate([
                'file' => 'required|mimes:mp4,avi,mov,wmv,flv|max:102400', // 100MB max
                'banner_id' => 'nullable|integer'
            ]);

            $file = $request->file('file');
            $banner_id = $request->banner_id;
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Store the file
            $path = $file->storeAs('banners', $filename, 'public');
            
            // START: Media Manager Integration
            try {
                $mediaPath = 'banners/' . $filename;
                if(Storage::disk('public')->exists($mediaPath)){
                    $mime = Storage::disk('public')->mimeType($mediaPath);
                    $size = Storage::disk('public')->size($mediaPath);
                    
                    Media::create([
                        'name' => $filename,
                        'file_name' => $filename,
                        'path' => $mediaPath,
                        'disk' => 'public',
                        'mime_type' => $mime,
                        'size' => $size,
                        'folder_id' => null,
                        'user_id' => auth()->id() ?? null
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error("Failed to register Banner video upload to Media Manager: " . $e->getMessage());
            }
            // END: Media Manager Integration

            // If banner_id is provided, update banner video
            if($banner_id) {
                $banner = Banner::find($banner_id);
                if($banner) {
                    // Delete old video if exists
                    if($banner->video && Storage::disk('public')->exists('banners/' . $banner->video)) {
                        Storage::disk('public')->delete('banners/' . $banner->video);
                    }
                    
                    $banner->update(['video' => $filename]);
                }
            }
            
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'message' => 'Video uploaded successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteImage(Request $request) {
        try {
            $request->validate([
                'image_id' => 'required|integer'
            ]);

            $image = BannerImage::findOrFail($request->image_id);
            
            // STOPPING physical deletion as per user request
            /*
            // Delete main image file
            $mainImagePath = 'banners/' . $image->image_name;
            $thumbImagePath = 'banners/thumb/' . $image->image_name;
            
            // Check if files exist and delete them (only if NOT media/)
            if(!Str::startsWith($image->image_name, 'media/') && Storage::disk('public')->exists($mainImagePath)) {
                Storage::disk('public')->delete($mainImagePath);
            }
            
            if(!Str::startsWith($image->image_name, 'media/') && Storage::disk('public')->exists($thumbImagePath)) {
                Storage::disk('public')->delete($thumbImagePath);
            }
            
            // Also try direct file deletion as fallback
            if(!Str::startsWith($image->image_name, 'media/')) {
                $mainImageFullPath = storage_path('app/public/' . $mainImagePath);
                $thumbImageFullPath = storage_path('app/public/' . $thumbImagePath);
                
                if(file_exists($mainImageFullPath)) {
                    unlink($mainImageFullPath);
                }
                
                if(file_exists($thumbImageFullPath)) {
                    unlink($thumbImageFullPath);
                }
            }
            */
            
            $image->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ]);
        }
    }

    private function createThumbnail($file, $filename) {
        try {
            // Create thumb directory if it doesn't exist
            $thumbDir = storage_path('app/public/banners/thumb/');
            if (!file_exists($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }
            
            // Create image manager with GD driver
            $manager = new ImageManager(new Driver());
            
            // Create image instance
            $image = $manager->read($file);
            
            // Resize image maintaining aspect ratio
            $image->scaleDown(300, 200);
            
            // Save thumbnail
            $thumbPath = $thumbDir . $filename;
            $image->save($thumbPath, 80);
            
        } catch (\Exception $e) {
            // Log error but don't fail the upload
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
        }
    }

    // Legacy methods for backward compatibility
    public function images(Request $request) {
        $data['page_heading'] = "Banner Images";
        $data['banner_id'] = $request->banner_id;

        $bannerImages = BannerImage::where('banner_id',$request->banner_id)->orderBy('sort_order','ASC')->get();
        $data['bannerImages'] = $bannerImages;

        return view('admin.banners.upload', $data);
    }

    public function ajax_delete_image(Request $request){
        return $this->deleteImage($request);
    }

    public function ajax_delete_video(Request $request){
        $storage = Storage::disk('public');
        $result['success'] = false;

        $id = ($request->has('id'))?$request->id:0;
        $path = 'banners/';
        if (is_numeric($id) && $id > 0) {
            $banner = Banner::find($id);
            if($banner && !empty($banner->video)) {
                $video = $banner->video;
                if(!empty($video) && $storage->exists($path.$video)){
                    $is_deleted = $storage->delete($path.$video);
                    if($is_deleted){
                        $banner->update(['video' => null]);
                        $result['success'] = true;
                        $result['message'] = 'Video deleted successfully!';
                    }
                }
            }
        }
        return response()->json($result);
    }

    public function media($encryptedId) {
        $id = CustomHelper::decrypt($encryptedId);
        $banner = Banner::findOrFail($id);
        return view('admin.banners.media', compact('banner'));
    }

    public function updateMedia(Request $request) {
        try {
            $request->validate([
                'media_id' => 'required|integer',
                'title' => 'nullable|string|max:255',
                'sub_title' => 'nullable|string',
                'link_text_1' => 'nullable|string|max:255',
                'link_1' => 'nullable|url|max:500',
                'link_text_2' => 'nullable|string|max:255',
                'link_2' => 'nullable|url|max:500'
            ]);

            $image = BannerImage::findOrFail($request->media_id);
            
            $image->update([
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'link_text_1' => $request->link_text_1,
                'link_1' => $request->link_1,
                'link_text_2' => $request->link_text_2,
                'link_2' => $request->link_2
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Media updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating media: ' . $e->getMessage()
            ]);
        }
    }

    public function updateAllMedia(Request $request) {
        try {
            $request->validate([
                'banner_id' => 'required|integer',
                'media_data' => 'required|array'
            ]);

            $updatedCount = 0;
            foreach ($request->media_data as $mediaItem) {
                $image = BannerImage::where('id', $mediaItem['media_id'])
                    ->where('banner_id', $request->banner_id)
                    ->first();

                if ($image) {
                    $image->update([
                        'title' => $mediaItem['title'] ?? null,
                        'sub_title' => $mediaItem['sub_title'] ?? null,
                        'link_text_1' => $mediaItem['link_text_1'] ?? null,
                        'link_1' => $mediaItem['link_1'] ?? null,
                        'link_text_2' => $mediaItem['link_text_2'] ?? null,
                        'link_2' => $mediaItem['link_2'] ?? null
                    ]);
                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'All media updated successfully! (' . $updatedCount . ' items)'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating media: ' . $e->getMessage()
            ]);
        }
    }

    public function updateMediaOrder(Request $request) {
        try {
            $request->validate([
                'banner_id' => 'required|integer',
                'order_data' => 'required|array'
            ]);

            $updatedCount = 0;
            foreach ($request->order_data as $orderItem) {
                $image = BannerImage::where('id', $orderItem['media_id'])
                    ->where('banner_id', $request->banner_id)
                    ->first();

                if ($image) {
                    $image->update(['sort_order' => $orderItem['sort_order']]);
                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully! (' . $updatedCount . ' items)'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage()
            ]);
        }
    }

    public function updateVideoInfo(Request $request) {
        try {
            $request->validate([
                'banner_id' => 'required|integer',
                'title' => 'nullable|string|max:255',
                'link_text_1' => 'nullable|string|max:255',
                'link_1' => 'nullable|url|max:500',
                'link_text_2' => 'nullable|string|max:255',
                'link_2' => 'nullable|url|max:500'
            ]);

            $banner = Banner::findOrFail($request->banner_id);
            
            $banner->update([
                'title' => $request->title,
                'link_text_1' => $request->link_text_1,
                'link_1' => $request->link_1,
                'link_text_2' => $request->link_text_2,
                'link_2' => $request->link_2
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Video information updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating video information: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteVideo(Request $request) {
        try {
            $request->validate([
                'banner_id' => 'required|integer'
            ]);

            $banner = Banner::findOrFail($request->banner_id);
            
            // Delete video file if exists
            if($banner->video && Storage::disk('public')->exists('banners/' . $banner->video)) {
                Storage::disk('public')->delete('banners/' . $banner->video);
            }
            
            // Also try direct file deletion as fallback
            if($banner->video) {
                $videoFullPath = storage_path('app/public/banners/' . $banner->video);
                if(file_exists($videoFullPath)) {
                    unlink($videoFullPath);
                }
            }
            
            // Clear video fields
            $banner->update([
                'video' => null,
                'video_embed' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting video: ' . $e->getMessage()
            ]);
        }
    }

}