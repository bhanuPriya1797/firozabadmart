<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRecord extends Model
{
    protected $table = "approval_record_info";

    protected $fillable = [
        'student_application_id', 'created_by', 'is_eligible', 'amount', 'recommended_by'
    ];

    public function application()
    {
        return $this->belongsTo(StudentApplication::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}

