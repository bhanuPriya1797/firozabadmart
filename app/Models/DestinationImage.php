<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationImage extends Model
{
    protected $table = 'destination_images';

    protected $fillable = [
        'destination_id',
        'file_name',
        'sort_order',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }
}

