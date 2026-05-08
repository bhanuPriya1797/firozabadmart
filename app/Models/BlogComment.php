<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $table = 'blog_comments';

    protected $guarded = ['id'];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function scopeUnread($q)
    {
        return $q->where('is_read', 0);
    }
}
