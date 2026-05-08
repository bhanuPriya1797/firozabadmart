<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArcherApplication extends Model
{
    protected $fillable = [
        'archer_id',
        'application_number',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function archer()
    {
        return $this->belongsTo(Archer::class);
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'approved':
                return 'Approved';
            case 're_evaluate':
                return 'Re-evaluate';
            case 'renewal_pending':
                return 'Renewal Pending';
            case 'rejected':
                return 'Rejected';
            case 'pending':
            default:
                return 'Pending';
        }
    }

    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'approved':
                return 'badge bg-success';
            case 're_evaluate':
                return 'badge bg-info';
            case 'renewal_pending':
                return 'badge bg-warning text-dark';
            case 'rejected':
                return 'badge bg-danger';
            case 'pending':
            default:
                return 'badge bg-warning';
        }
    }
}
