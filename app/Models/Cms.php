<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
    protected $table = 'cms_pages';

    protected $fillable = [
        'title',
        'brief',
        'slug',
        'heading',
        'template',
        'banner',
        'page_image',
        'description',
        'seo',
        'custom_fields',
        'featured',
        'parent_id',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'seo' => 'array',
        'custom_fields' => 'array',
        'featured' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Parent page relationship
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Child pages relationship
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Scope for active pages only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function customFieldValues()
    {
        return $this->hasMany(CustomFieldValue::class, 'module_id')->where('module_type', 'cms');
    }

    // Helper: Return associative array [key => value]
    // Note: This accessor is disabled to allow controller to handle custom field assignment
    // public function getCustomFieldsAttribute()
    // {
    //     return $this->customFieldValues->pluck('value', 'key')->toArray();
    // }

}


