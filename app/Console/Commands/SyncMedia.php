<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Media;
use App\Models\Folder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SyncMedia extends Command
{
    protected $signature = 'media:sync';
    protected $description = 'Sync existing files in storage to media table';

    public function handle()
    {
        $disk = 'public';
        // $root = 'media'; // OLD: Only media folder
        $root = ''; // NEW: Scan entire public disk
        
        $this->info("Syncing files from storage/app/public to database...");

        // Get all files in the public directory
        $allFiles = Storage::disk($disk)->allFiles($root);

        $bar = $this->output->createProgressBar(count($allFiles));
        $bar->start();

        foreach ($allFiles as $filePath) {
            // Skip hidden files or .gitignore
            if (str_starts_with($filePath, '.') || str_contains($filePath, '/.')) {
                $bar->advance();
                continue;
            }

            // Skip if already in DB
            if (Media::where('path', $filePath)->exists()) {
                $bar->advance();
                continue;
            }

            // Get file details
            try {
                $mime = Storage::disk($disk)->mimeType($filePath);
                $size = Storage::disk($disk)->size($filePath);
            } catch (\Exception $e) {
                // Skip files that can't be read
                $bar->advance();
                continue;
            }
            
            $filename = basename($filePath);
            $dirname = dirname($filePath);
            
            // Handle Folders
            // $dirname will be like "banners" or "media/2023" or "."
            
            $parentFolderId = null;

            if ($dirname !== '.' && $dirname !== '') {
                // Fix for Windows paths if any (Storage usually uses forward slashes but dirname might return backslash on Windows)
                $dirname = str_replace('\\', '/', $dirname);
                
                $folders = explode('/', $dirname);
                foreach ($folders as $folderName) {
                    if (empty($folderName)) continue;
                    
                    $folder = Folder::firstOrCreate(
                        ['name' => $folderName, 'parent_id' => $parentFolderId],
                        ['user_id' => null] // System owned
                    );
                    $parentFolderId = $folder->id;
                }
            }

            // Create Media Record
            Media::create([
                'name' => $filename,
                'file_name' => $filename,
                'path' => $filePath,
                'disk' => $disk,
                'mime_type' => $mime,
                'size' => $size,
                'folder_id' => $parentFolderId,
                'user_id' => null, // System owned
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Media sync completed!');
    }
}
