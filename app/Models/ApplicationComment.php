<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationComment extends Model
{
    protected $table = "application_comments";
    protected $fillable = ['student_application_id', 'comment', 'created_by', 'show_in_front'];

    public function application()
    {
        return $this->belongsTo(StudentApplication::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}