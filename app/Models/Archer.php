<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Archer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'surname',
        'father_name',
        'mother_name',
        'email',
        'phone',
        'alternate_phone',
        'whatsapp_number',
        'gender',
        'dob',
        'marital_status',
        'is_minor',
        'aadhar_card_number',
        'aadhar_document',
        'password',
        'category',
        'member_id',
        'member_association',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'is_minor' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    public function applications()
    {
        return $this->hasMany(ArcherApplication::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return asset('admin/assets/img/blank_user.jpg');
    }
}

