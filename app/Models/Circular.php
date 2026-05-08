<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Circular extends Model
{
    protected $table = 'circulars';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'banner_image',
        'document_path',
        'seo',
        'featured',
        'status',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'seo' => 'array',
        'featured' => 'boolean',
        'status' => 'boolean',
    ];
}
