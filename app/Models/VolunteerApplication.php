<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'interests',
        'availability',
        'hours',
        'skills',
        'motivation',
        'terms_accepted',
        'ip_address',
        'status',
        'admin_notes'
    ];

    protected $casts = [
        'interests' => 'array',
        'terms_accepted' => 'boolean',
    ];

    /**
     * Get the full name of the volunteer
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the interests as a comma-separated string
     */
    public function getInterestsStringAttribute()
    {
        return is_array($this->interests) ? implode(', ', $this->interests) : '';
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
