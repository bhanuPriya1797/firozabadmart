<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_name',
        'path',
        'disk',
        'mime_type',
        'size',
        'alt_text',
        'folder_id',
        'user_id',
    ];

    protected $appends = ['url', 'formatted_size'];

    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . ltrim($this->path, '/'));
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
