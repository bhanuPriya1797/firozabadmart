<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = "website_settings";
    protected $fillable = ['key', 'label', 'value', 'old_value', 'type', 'options', 'class', 'group_name', 'validation', 'file_constraints', 'is_fixed', 'created_by', 'updated_by'];

    protected $casts = [
        'options' => 'array',
    ];
}

