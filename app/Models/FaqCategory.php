<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'sort_order'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relationships
    public function faqs()
    {
        return $this->hasMany(Faq::class, 'category_id')->orderBy('sort_order', 'asc');
    }

    public function activeFaqs()
    {
        return $this->hasMany(Faq::class, 'category_id')->where('status', 1)->orderBy('sort_order', 'asc');
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

    // Accessors
    public function getFaqsCountAttribute()
    {
        return $this->faqs()->count();
    }

    public function getActiveFaqsCountAttribute()
    {
        return $this->activeFaqs()->count();
    }
}
