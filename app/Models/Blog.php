<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\CustomHelper;

class Blog extends Model{

    protected $table = 'blogs';

    protected $guarded = ['id'];

    protected $fillable = [
        'category_id',
        'title',
        'post_by',
        'slug',
        'brief',
        'content',
        'image',
        'team_image_1',
        'team_image_2',
        'tags',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'status',
        'featured',
        'blog_date',
        'end_date',
        'posted_by',
        'status',
        'content_type',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;

    public function Images() {
        return $this->hasMany('App\Models\BlogImage', 'blog_id');
    }

    function Category(){
        return $this->belongsTo('App\Models\BlogCategory', 'category_id');
    }

    function User(){
        return $this->belongsTo('App\Models\User', 'post_by');
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) return asset('admin/assets/img/default.png');
        if (\Illuminate\Support\Str::startsWith($this->image, 'media/')) {
            return asset('storage/' . $this->image);
        }
        $base = \Illuminate\Support\Str::startsWith($this->image, 'uploads/blogs/') ? 'uploads/blogs/' : 'blogs/';
        return CustomHelper::getImageUrl($this->image, $base, 'large');
    }

    public function getMediumImageUrlAttribute()
    {
        if (empty($this->image)) return asset('admin/assets/img/default.png');
        if (\Illuminate\Support\Str::startsWith($this->image, 'media/')) {
            return asset('storage/' . $this->image);
        }
        $base = \Illuminate\Support\Str::startsWith($this->image, 'uploads/blogs/') ? 'uploads/blogs/' : 'blogs/';
        return CustomHelper::getImageUrl($this->image, $base, 'medium');
    }

    public function getThumbImageUrlAttribute()
    {
        if (empty($this->image)) return asset('admin/assets/img/default.png');
        if (\Illuminate\Support\Str::startsWith($this->image, 'media/')) {
            return asset('storage/' . $this->image);
        }
        $base = \Illuminate\Support\Str::startsWith($this->image, 'uploads/blogs/') ? 'uploads/blogs/' : 'blogs/';
        return CustomHelper::getImageUrl($this->image, $base, 'thumb');
    }

    public function getTeamImage1UrlAttribute()
    {
        $p = $this->team_image_1 ?? '';
        if (empty($p)) return null;
        if (str_contains($p, '/')) {
            return asset('storage/' . ltrim($p, '/'));
        }
        return asset('storage/events/' . $p);
    }

    public function getTeamImage2UrlAttribute()
    {
        $p = $this->team_image_2 ?? '';
        if (empty($p)) return null;
        if (str_contains($p, '/')) {
            return asset('storage/' . ltrim($p, '/'));
        }
        return asset('storage/events/' . $p);
    }
}
