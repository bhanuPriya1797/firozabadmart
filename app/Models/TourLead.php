<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourLead extends Model
{
    protected $table = 'tour_leads';

    protected $guarded = ['id'];

    protected $fillable = [
        'tour_package_id',
        'full_name',
        'email',
        'phone',
        'message',
        'status',
        'is_read',
    ];

    public function tour()
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }

    public function scopeUnread($q)
    {
        return $q->where('is_read', 0);
    }
}
