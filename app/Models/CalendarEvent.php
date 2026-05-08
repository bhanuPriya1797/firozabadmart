<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CalendarEvent extends Model
{
    protected $table = 'calendar_events';

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'location',
        'category',
        'slug',
        'external_url',
        'description',
        'status',
        'featured',
        'sort_order',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'status' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeActive($q)
    {
        return $q->where('status', 1);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order', 'asc')->orderBy('start_date', 'asc')->orderBy('id', 'desc');
    }

    public static function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (self::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
