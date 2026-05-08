<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\GalleryFolder;
use App\Models\GalleryImage;
use App\Models\Media; // Added for Media Manager integration
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class GalleryController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index()
    {
        $folders = GalleryFolder::ordered()->paginate(12);
        $imagesCount = GalleryImage::whereNull('folder_id')->count();
        return view('admin.gallery.index', compact('folders', 'imagesCount'));
    }

    public function createFolder()
    {
        $folder = new GalleryFolder();
        return view('admin.gallery.folder_form', compact('folder'));
    }

    public function storeFolder(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'description' => ['nullable', 'string'],
                'status' => ['nullable', 'in:0,1'],
                'sort_order' => ['nullable', 'integer'],
                'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            ]);
            $data['slug'] = CustomHelper::GetSlug('gallery_folders', 'id', 0, $data['name']);
            $data['status'] = (int)($data['status'] ?? 1);
            if ($request->hasFile('cover_image')) {
                $file = $request->file('cover_image');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext = $file->getClientOriginalExtension();
                $stored = $file->storeAs('gallery', "$filename.$ext", 'public');
                $data['cover_image'] = basename($stored);

                // START: Media Manager Integration
                try {
                    $mediaPath = 'gallery/' . basename($stored);
                    if(Storage::disk('public')->exists($mediaPath)){
                        $mime = Storage::disk('public')->mimeType($mediaPath);
                        $size = Storage::disk('public')->size($mediaPath);
                        
                        Media::create([
                            'name' => basename($stored),
                            'file_name' => basename($stored),
                            'path' => $mediaPath,
                            'disk' => 'public',
                            'mime_type' => $mime,
                            'size' => $size,
                            'folder_id' => null,
                            'user_id' => auth()->id() ?? null
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to register Gallery Folder cover to Media Manager: " . $e->getMessage());
                }
                // END: Media Manager Integration

            } elseif ($request->filled('cover_image_media_path')) {
                $data['cover_image'] = $request->input('cover_image_media_path');
            }
            $folder = GalleryFolder::create($data);
            CustomHelper::recordActionLog(
                url()->current(),
                'gallery_folders',
                $folder->id,
                'Create Gallery Folder',
                'Created gallery folder: ' . $folder->name,
                json_encode($data)
            );
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.gallery.index')->with('success', 'Folder created successfully.');
        } catch (\Exception $e) {
            \Log::error('GalleryController@storeFolder: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create folder.');
        }
    }

    public function editFolder($id)
    {
        $folder = GalleryFolder::findOrFail($id);
        return view('admin.gallery.folder_form', compact('folder'));
    }

    public function updateFolder(Request $request, $id)
    {
        try {
            $folder = GalleryFolder::findOrFail($id);
            $data = $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'description' => ['nullable', 'string'],
                'status' => ['nullable', 'in:0,1'],
                'sort_order' => ['nullable', 'integer'],
                'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            ]);
            $data['slug'] = CustomHelper::GetSlug('gallery_folders', 'id', $folder->id, $data['name']);
            $data['status'] = (int)($data['status'] ?? $folder->status);
            if ($request->hasFile('cover_image')) {
                /*
                // STOPPING physical deletion
                if ($folder->cover_image && !Str::startsWith($folder->cover_image, 'media/') && Storage::disk('public')->exists('gallery/' . $folder->cover_image)) {
                    Storage::disk('public')->delete('gallery/' . $folder->cover_image);
                }
                */
                $file = $request->file('cover_image');
                $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $ext = $file->getClientOriginalExtension();
                $stored = $file->storeAs('gallery', "$filename.$ext", 'public');
                $data['cover_image'] = basename($stored);
            } elseif ($request->filled('cover_image_media_path')) {
                // If replacing old image, delete old one only if it's local
                /*
                // STOPPING physical deletion
                if ($folder->cover_image && $folder->cover_image != $request->input('cover_image_media_path')) {
                    if (!Str::startsWith($folder->cover_image, 'media/') && Storage::disk('public')->exists('gallery/' . $folder->cover_image)) {
                        Storage::disk('public')->delete('gallery/' . $folder->cover_image);
                    }
                }
                */
                $data['cover_image'] = $request->input('cover_image_media_path');
            }
            $folder->fill($data)->save();
            CustomHelper::recordActionLog(
                url()->current(),
                'gallery_folders',
                $folder->id,
                'Update Gallery Folder',
                'Updated gallery folder: ' . $folder->name,
                json_encode($data)
            );
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.gallery.index')->with('success', 'Folder updated successfully.');
        } catch (\Exception $e) {
            \Log::error('GalleryController@updateFolder: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update folder.');
        }
    }

    public function destroyFolder($id)
    {
        try {
            $folder = GalleryFolder::findOrFail($id);
            /*
            // STOPPING physical deletion
            if ($folder->cover_image && !Str::startsWith($folder->cover_image, 'media/') && Storage::disk('public')->exists('gallery/' . $folder->cover_image)) {
                Storage::disk('public')->delete('gallery/' . $folder->cover_image);
            }
            */
            $images = $folder->images()->get();
            foreach ($images as $image) {
                /*
                // STOPPING physical deletion
                if ($image->file_name && !Str::startsWith($image->file_name, 'media/') && Storage::disk('public')->exists('gallery/' . $image->file_name)) {
                    Storage::disk('public')->delete('gallery/' . $image->file_name);
                }
                */
                $image->delete();
            }
            $name = $folder->name;
            $folder->delete();
            try {
                CustomHelper::recordActionLog(
                    url()->current(),
                    'gallery_folders',
                    $id,
                    'Delete Gallery Folder',
                    'Deleted gallery folder: ' . ($name ?? ''),
                    ''
                );
            } catch (\Throwable $e) {}
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Folder deleted successfully.']);
            }
            return back()->with('success', 'Folder deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('GalleryController@destroyFolder: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete folder.'], 500);
            }
            return back()->with('error', 'Failed to delete folder.');
        }
    }

    public function imagesIndex()
    {
        $images = GalleryImage::whereNull('folder_id')->ordered()->paginate(24);
        $folders = GalleryFolder::ordered()->get();
        return view('admin.gallery.images_index', compact('images', 'folders'));
    }

    public function uploadImages(Request $request)
    {
        try {
            $request->validate([
                'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
                'title' => ['nullable', 'string', 'max:150'],
                'folder_id' => ['nullable', 'integer', 'exists:gallery_folders,id'],
            ]);
            $folderId = $request->input('folder_id') ?: null;
            $created = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $ext = $file->getClientOriginalExtension();
                    $stored = $file->storeAs('gallery', "$filename.$ext", 'public');
                    $name = basename($stored);
                    $img = GalleryImage::create([
                        'folder_id' => $folderId,
                        'title' => $request->input('title'),
                        'file_name' => $name,
                        'sort_order' => 0,
                        'status' => 1,
                    ]);

                    // START: Media Manager Integration
                    try {
                        $mediaPath = 'gallery/' . $name;
                        if(Storage::disk('public')->exists($mediaPath)){
                            $mime = Storage::disk('public')->mimeType($mediaPath);
                            $size = Storage::disk('public')->size($mediaPath);
                            
                            Media::create([
                                'name' => $name,
                                'file_name' => $name,
                                'path' => $mediaPath,
                                'disk' => 'public',
                                'mime_type' => $mime,
                                'size' => $size,
                                'folder_id' => null,
                                'user_id' => auth()->id() ?? null
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Failed to register Gallery Image upload to Media Manager: " . $e->getMessage());
                    }
                    // END: Media Manager Integration

                    $created[] = ['id' => $img->id, 'file_name' => $name];
                }
            }
            CustomHelper::recordActionLog(
                url()->current(),
                'gallery_images',
                0,
                'Upload Gallery Images',
                'Uploaded images count: ' . count($created),
                json_encode(['folder_id' => $folderId, 'images' => $created])
            );
            return back()->with('success', 'Images uploaded successfully.');
        } catch (\Exception $e) {
            \Log::error('GalleryController@uploadImages: ' . $e->getMessage());
            return back()->with('error', 'Failed to upload images.');
        }
    }

    public function addMediaFromLibrary(Request $request) {
        $request->validate([
            'file_path' => 'required|string',
            'folder_id' => 'nullable|integer|exists:gallery_folders,id'
        ]);

        try {
            $sourcePath = $request->file_path;
            if (strpos($sourcePath, 'storage/') !== false) {
                $sourcePath = str_replace(asset('storage') . '/', '', $sourcePath);
                $sourcePath = str_replace('/storage/', '', $sourcePath);
            }
            
            // Check if file exists (relative to public disk root)
            if (!Storage::disk('public')->exists($sourcePath)) {
                 // Try to strip leading slash if present
                 $sourcePath = ltrim($sourcePath, '/');
                 if (!Storage::disk('public')->exists($sourcePath)) {
                    return response()->json(['success' => false, 'message' => 'File not found in library.']);
                 }
            }

            // DO NOT COPY. Use the path directly.
            // Ensure path is relative to storage root (e.g. media/...)
            
            GalleryImage::create([
                'folder_id' => $request->folder_id,
                'title' => basename($sourcePath), // Default title
                'file_name' => $sourcePath, // Store full relative path
                'sort_order' => 0,
                'status' => 1
            ]);

            return response()->json(['success' => true, 'message' => 'Image added successfully!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function folderImages($id)
    {
        $folder = GalleryFolder::findOrFail($id);
        $images = $folder->images()->paginate(24);
        return view('admin.gallery.folder_images', compact('folder', 'images'));
    }

    public function uploadChunk(Request $request)
    {
        $request->validate([
            'chunk' => 'required',
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
            'upload_key' => 'required|string',
            'file_name' => 'required|string',
            'folder_id' => 'nullable|integer|exists:gallery_folders,id',
            'title' => 'nullable|string|max:150',
        ]);
        $uploadKey = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->upload_key);
        $total = (int)$request->total_chunks;
        $index = (int)$request->chunk_index;
        $baseName = basename($request->file_name);
        $tmpBase = storage_path('app/tmp_uploads/' . $uploadKey);
        if (!File::exists($tmpBase)) {
            File::makeDirectory($tmpBase, 0777, true, true);
        }
        $chunkFile = $tmpBase . '/part_' . $index;
        $chunk = $request->file('chunk');
        if ($chunk && $chunk->isValid()) {
            File::put($chunkFile, File::get($chunk->getPathname()));
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid chunk'], 422);
        }
        if ($index < $total - 1) {
            return response()->json(['success' => true, 'partial' => true]);
        }
        $assembledPath = $tmpBase . '/' . $baseName;
        $out = fopen($assembledPath, 'wb');
        for ($i = 0; $i < $total; $i++) {
            $part = $tmpBase . '/part_' . $i;
            $in = fopen($part, 'rb');
            stream_copy_to_stream($in, $out);
            fclose($in);
        }
        fclose($out);
        $path = 'gallery/';
        $thumbPath = 'gallery/thumb/';
        $width = (int)config('images.gallery_width', 1600);
        $height = (int)config('images.gallery_height', 1600);
        $thumbWidth = 480;
        $thumbHeight = 480;
        $uploaded = \App\Helpers\CustomHelper::UploadImage($assembledPath, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight);
        foreach (glob($tmpBase . '/part_*') as $p) { @unlink($p); }
        @unlink($assembledPath);
        @rmdir($tmpBase);
        if (empty($uploaded['success']) || empty($uploaded['file_name'])) {
            return response()->json(['success' => false, 'message' => 'Failed to process image']);
        }

        // START: Media Manager Integration
        try {
            $mediaPath = 'gallery/' . $uploaded['file_name'];
            if(Storage::disk('public')->exists($mediaPath)){
                $mime = Storage::disk('public')->mimeType($mediaPath);
                $size = Storage::disk('public')->size($mediaPath);
                
                Media::create([
                    'name' => $uploaded['file_name'],
                    'file_name' => $uploaded['file_name'],
                    'path' => $mediaPath,
                    'disk' => 'public',
                    'mime_type' => $mime,
                    'size' => $size,
                    'folder_id' => null, // Or create a 'gallery' folder in media table?
                    'user_id' => auth()->id() ?? null
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to register Gallery upload to Media Manager: " . $e->getMessage());
        }
        // END: Media Manager Integration

        $image = GalleryImage::create([
            'folder_id' => $request->input('folder_id') ?: null,
            'title' => $request->input('title'),
            'file_name' => $uploaded['file_name'],
            'sort_order' => 0,
            'status' => 1,
        ]);
        return response()->json([
            'success' => true,
            'id' => $image->id,
            'file_name' => $uploaded['file_name'],
            'preview_url' => asset('storage/gallery/thumb/' . $uploaded['file_name']),
            'full_url' => asset('storage/gallery/' . $uploaded['file_name']),
        ]);
    }

    public function destroyImage($id)
    {
        try {
            $image = GalleryImage::findOrFail($id);
            // Only delete if NOT from media/
            /*
            // STOPPING physical deletion
            if ($image->file_name && !Str::startsWith($image->file_name, 'media/') && Storage::disk('public')->exists('gallery/' . $image->file_name)) {
                Storage::disk('public')->delete('gallery/' . $image->file_name);
            }
            */
            $image->delete();
            CustomHelper::recordActionLog(
                url()->current(),
                'gallery_images',
                $id,
                'Delete Gallery Image',
                'Deleted gallery image: ' . ($image->file_name ?? ''),
                ''
            );
            return back()->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('GalleryController@destroyImage: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete image.');
        }
    }
}
