<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $table = 'contact_enquiries';

    protected $guarded = ['id'];

    protected $fillable = [
        'name', 'phone', 'contact_email', 'comment', 'country', 'ip_address', 'is_read'
    ];

    // Enable timestamps
    public $timestamps = true;

    public function scopeUnread($q)
    {
        return $q->where('is_read', 0);
    }
}
