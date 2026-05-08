<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = [
        'name',
        'first_name',
        'surname',
        'father_name',
        'mother_name',
        'family_lineage',
        'gender',
        'marital_status',
        'is_minor',
        'dob',
        'email',
        'password',
        'address',
        'state',
        'district',
        'pincode',
        'contact_no',
        'phone',
        'alternate_phone',
        'whatsapp_number',
        'aadhar_card_number',
        'aadhar_document',
        'guardian_name',
        'guardian_contact_no',
        'guardian_occupation',
        'guardian_occupation_details',
        'monthly_income',
        'family_members',
        'status',
        'profile_photo'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function applications()
    {
        return $this->hasMany(StudentApplication::class);
    }

    public function latestApplication()
    {
        return $this->hasOne(StudentApplication::class)->latestOfMany();
    }

    public function approvals()
    {
        return $this->hasMany(ApprovalRecord::class);
    }

    public function comments()
    {
        return $this->hasMany(ApplicationComment::class);
    }

    public function applicationRequests()
    {
        return $this->hasMany(ApplicationRequest::class);
    }

    public function latestApprovedApplicationRequest()
    {
        return $this->hasOne(ApplicationRequest::class)->where('status', 'approved')->latestOfMany();
    }

}
