<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackageImage extends Model
{
    protected $table = 'tour_package_images';

    protected $fillable = [
        'tour_package_id',
        'file_name',
        'sort_order',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}

