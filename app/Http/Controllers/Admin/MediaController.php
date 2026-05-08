<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use App\Models\Folder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;

class MediaController extends Controller {

    private $limit;
    private $diskName;
    private $mediaRoot;
    private $ADMIN_ROUTE_NAME;

    private static $_invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');

    public function __construct(){
        $this->limit = 20;
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        $this->diskName = 'public'; // Default disk
        $this->mediaRoot = 'media';
    }

    public function index(Request $request){
        $data = [];
        $data['page_heading'] = 'Media Manager';
        
        // Pass initial configuration to view
        $data['isPopup'] = $request->has('popup');
        $data['popupFieldId'] = $request->input('field_id');
        $data['ckEditorFuncNum'] = $request->input('CKEditorFuncNum');

        return view('admin.media.index', $data);
    }

    /**
     * JSON: List folders and files from Database
     */
    public function apiFiles(Request $request)
    {
        $folderId = $request->input('folder_id');
        
        // Save current folder to session (if needed for reload, though JS usually handles state)
        // logic for breadcrumbs
        $breadcrumbs = [];
        $breadcrumbs[] = ['label' => 'Home', 'id' => null];

        if ($folderId) {
            $currentFolder = Folder::with('parent')->find($folderId);
            if ($currentFolder) {
                // Build breadcrumbs
                $temp = $currentFolder;
                $stack = [];
                while ($temp) {
                    $stack[] = ['label' => $temp->name, 'id' => $temp->id];
                    $temp = $temp->parent;
                }
                $breadcrumbs = array_merge($breadcrumbs, array_reverse($stack));
                
                $folders = Folder::where('parent_id', $folderId)->orderBy('name')->get();
                $files = Media::where('folder_id', $folderId)->orderBy('created_at', 'desc')->get();
            } else {
                // Folder not found, fallback to root
                $folders = Folder::whereNull('parent_id')->orderBy('name')->get();
                $files = Media::whereNull('folder_id')->orderBy('created_at', 'desc')->get();
            }
        } else {
            $folders = Folder::whereNull('parent_id')->orderBy('name')->get();
            $files = Media::whereNull('folder_id')->orderBy('created_at', 'desc')->get();
        }

        // Transform for frontend
        $foldersData = $folders->map(function($f) {
            return [
                'id' => $f->id,
                'name' => $f->name,
                'type' => 'folder'
            ];
        });

        $filesData = $files->map(function($f) {
            return [
                'id' => $f->id,
                'name' => $f->name ?? $f->file_name,
                'path' => $f->path,
                'url' => $f->url,
                'mime' => $f->mime_type,
                'size' => $f->size,
                'formatted_size' => $f->formatted_size,
                'alt_text' => $f->alt_text,
                'created_at' => $f->created_at->format('Y-m-d H:i:s'),
                'type' => 'file'
            ];
        });

        return response()->json([
            'success' => true,
            'current_folder_id' => $folderId,
            'breadcrumbs' => $breadcrumbs,
            'folders' => $foldersData,
            'files' => $filesData
        ]);
    }

    /**
     * Create a folder in Database
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:folders,id']
        ]);

        $folder = Folder::create([
            'name' => trim($request->name),
            'parent_id' => $request->parent_id,
            'user_id' => auth()->id() ?? null
        ]);

        return response()->json(['success' => true, 'folder' => $folder]);
    }

    /**
     * Update Alt Text in Database
     */
    public function updateAltText(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:media,id',
            'alt_text' => 'nullable|string'
        ]);

        $media = Media::find($request->id);
        $media->update(['alt_text' => $request->alt_text]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete Folder (and contents)
     */
    public function deleteFolder(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:folders,id'
        ]);

        $folder = Folder::find($request->id);
        
        // Recursive deletion of files on disk
        $this->deleteFolderContents($folder);
        
        $folder->delete(); // Database cascade will handle children records

        return response()->json(['success' => true]);
    }

    private function deleteFolderContents($folder) {
        // Delete files in this folder
        foreach ($folder->media as $media) {
            if (Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }
        }
        
        // Recurse for subfolders
        foreach ($folder->children as $child) {
            $this->deleteFolderContents($child);
        }
    }

    /**
     * Delete File
     */
    public function deleteFile(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:media,id'
        ]);

        $media = Media::find($request->id);
        
        /*
        // STOPPING physical deletion
        if (Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }
        */
        
        $media->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Chunk Upload Handler (Pion) + DB Entry
     */
    public function uploadChunkGeneric(Request $request)
    {
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if ($receiver->isUploaded() === false) {
            return response()->json(['success' => false, 'message' => 'Upload failed'], 400);
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            return $this->saveFile($save->getFile(), $request);
        }

        $handler = $save->handler();

        return response()->json([
            'done' => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    protected function saveFile($file, Request $request)
    {
        $folderId = $request->input('folder_id');
        if ($folderId === 'null' || $folderId === 'undefined') $folderId = null;

        $disk = Storage::disk($this->diskName);
        
        // Generate path: media/{year}/{month}/filename
        $datePath = date('Y/m');
        $targetPath = $this->mediaRoot . '/' . $datePath;
        
        $fileName = $file->getClientOriginalName();
        $fileName = preg_replace('/[^A-Za-z0-9._-]/', '_', $fileName);
        $uniqueName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . time() . '.' . $file->getClientOriginalExtension();

        $finalPath = $disk->putFileAs($targetPath, $file, $uniqueName);

        // Delete chunk temp file
        @unlink($file->getPathname());

        // Create DB Record
        $media = Media::create([
            'name' => $fileName,
            'file_name' => $uniqueName,
            'path' => $finalPath,
            'disk' => $this->diskName,
            'mime_type' => $disk->mimeType($finalPath),
            'size' => $disk->size($finalPath),
            'folder_id' => $folderId,
            'user_id' => auth()->id() ?? null
        ]);

        return response()->json([
            'success' => true,
            'path' => $finalPath,
            'url' => $media->url,
            'filename' => $uniqueName,
            'id' => $media->id
        ]);
    }

    // Legacy support or direct upload if needed
    public function store(Request $request) {
        return $this->uploadChunkGeneric($request);
    }
}
