<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model{
    
    protected $table = 'activity_logs';

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'row_id',
        'function_name',
        'action_table',
        'action_type',
        'action_description',
        'description',
        'ip_address'
    ];

    public function activityLogadmin(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    
}