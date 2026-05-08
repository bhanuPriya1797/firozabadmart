<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempImage extends Model
{
    protected $table = 'temp_images';

    protected $fillable = [
        'upload_key',
        'file_name',
        'mime',
        'size',
        'created_by',
    ];
}
