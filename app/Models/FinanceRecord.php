<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceRecord extends Model
{
    protected $fillable = [
        'student_application_id',
        'appeal_no',
        'amount',
        'payment_mode',
        'payment_status',
        'donation_type',
        'muqalid',
        'details',
        'date',
        'support_type',
    ];

    public function application()
    {
        return $this->belongsTo(StudentApplication::class, 'student_application_id');
    }
}

