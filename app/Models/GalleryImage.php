<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $table = 'gallery_images';

    protected $guarded = ['id'];

    protected $fillable = [
        'folder_id',
        'title',
        'file_name',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
        'folder_id' => 'integer',
    ];

    public function folder()
    {
        return $this->belongsTo(GalleryFolder::class, 'folder_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }

    public function getUrlAttribute()
    {
        if (str_starts_with($this->file_name, 'media/')) {
            return asset('storage/' . $this->file_name);
        }
        return asset('storage/gallery/' . $this->file_name);
    }

    public function getThumbUrlAttribute()
    {
        if (str_starts_with($this->file_name, 'media/')) {
             // Try to find thumb in media/thumb/ if needed, or just return original for now
             // Media manager usually has thumbs.
             // If path is media/folder/img.jpg, thumb might be media/folder/thumb/img.jpg?
             // Or CustomHelper::UploadImage structure?
             // Let's assume original for now or check if thumb exists (expensive).
             // Better: return original, or if we know the structure.
             return asset('storage/' . $this->file_name);
        }
        return asset('storage/gallery/' . $this->file_name);
    }
}

