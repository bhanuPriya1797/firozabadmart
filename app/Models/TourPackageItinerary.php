<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackageItinerary extends Model
{
    protected $table = 'tour_package_itineraries';

    protected $fillable = [
        'tour_package_id',
        'day_number',
        'title',
        'content',
        'sort_order',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}

