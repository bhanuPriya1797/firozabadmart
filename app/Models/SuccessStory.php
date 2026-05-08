<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\CustomHelper;

class SuccessStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'brief',
        'description',
        'image',
        'featured',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Scope for active stories
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope for featured stories
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    // Scope for ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    // Get image URL
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }
        return asset('admin/assets/img/default.png');
    }

    // Get excerpt of description
    public function getExcerptAttribute($length = 150)
    {
        return \Str::limit(strip_tags($this->description), $length);
    }

    // Encrypt ID for URLs
    public function getEncryptedIdAttribute()
    {
        return CustomHelper::encrypt($this->id);
    }

    // Static method to decrypt ID
    public static function decryptId($encryptedId)
    {
        return CustomHelper::decrypt($encryptedId);
    }
}
