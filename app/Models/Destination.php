<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $table = 'destinations';

    protected $guarded = ['id'];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function infos()
    {
        return $this->hasMany(DestinationInfo::class, 'destination_id')->orderBy('sort_order');
    }

    public function images()
    {
        return $this->hasMany(DestinationImage::class, 'destination_id')->orderBy('sort_order')->orderBy('id', 'desc');
    }
}