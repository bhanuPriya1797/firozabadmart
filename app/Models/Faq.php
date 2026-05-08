<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category_id',
        'status',
        'sort_order',
        'page_type',
        'page_id'
    ];

    protected $casts = [
        'status' => 'boolean',
        'page_id' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByCategorySlug($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByPage($query, $pageType, $pageId)
    {
        return $query->where('page_type', $pageType)->where('page_id', $pageId);
    }
}
