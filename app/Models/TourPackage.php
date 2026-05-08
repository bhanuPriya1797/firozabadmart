<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    protected $table = 'tour_packages';
    protected $guarded = ['id'];

    protected $casts = [
        'status' => 'boolean',
        'included' => 'array',
        'excluded' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'max_seats' => 'integer',
        'booked_seats' => 'integer',
        'price' => 'decimal:2',
        'price_individual' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'is_sukoon' => 'boolean',
        'sukoon_featured' => 'boolean',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }

    public function itineraries()
    {
        return $this->hasMany(TourPackageItinerary::class, 'tour_package_id')->orderBy('day_number')->orderBy('sort_order');
    }

    public function images()
    {
        return $this->hasMany(TourPackageImage::class, 'tour_package_id')->orderBy('sort_order')->orderBy('id', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
    
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())->orderBy('start_date', 'asc');
    }
    
    public function scopeSukoon($query)
    {
        return $query->where('is_sukoon', 1);
    }

    public function getRemainingSeatsAttribute()
    {
        $max = (int)$this->max_seats;
        $booked = (int)$this->booked_seats;
        $left = $max - $booked;
        return $left < 0 ? 0 : $left;
    }

    public function getOriginalPriceAttribute()
    {
        if ($this->type === 'individual' && $this->price_individual !== null) {
            return (float)$this->price_individual;
        }
        return (float)$this->price;
    }
}
