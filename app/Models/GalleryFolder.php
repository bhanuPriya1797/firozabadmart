<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryFolder extends Model
{
    protected $table = 'gallery_folders';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'cover_image',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function images()
    {
        return $this->hasMany(GalleryImage::class, 'folder_id')->orderBy('sort_order')->orderBy('id', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function getCoverUrlAttribute()
    {
        if ($this->cover_image) {
            if (str_starts_with($this->cover_image, 'media/')) {
                return asset('storage/' . $this->cover_image);
            }
            return asset('storage/gallery/' . $this->cover_image);
        }
        return asset('admin/assets/img/default.png');
    }
}

