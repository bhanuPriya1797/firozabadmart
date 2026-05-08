<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerImage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banner_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'banner_id',
        'image_name',
        'title',
        'sub_title',
        'link_text_1',
        'link_1',
        'link_text_2',
        'link_2',
        'sort_order'
    ];

    /**
     * Get the banner that owns the image.
     */
    public function banner()
    {
        return $this->belongsTo(Banner::class, 'banner_id');
    }

    /**
     * Get the image URL attribute.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_name) {
            if (str_starts_with($this->image_name, 'media/')) {
                return asset('storage/' . $this->image_name);
            }
            return asset('storage/banners/' . $this->image_name);
        }
        return null;
    }

    /**
     * Get the thumbnail URL attribute.
     *
     * @return string
     */
    public function getThumbUrlAttribute()
    {
        if ($this->image_name) {
            if (str_starts_with($this->image_name, 'media/')) {
                 return asset('storage/' . $this->image_name);
            }
            return asset('storage/banners/thumb/' . $this->image_name);
        }
        return null;
    }
}