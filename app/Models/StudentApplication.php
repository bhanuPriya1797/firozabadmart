<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model
{
    protected $fillable = [
        'student_id', 'application_number', 'case_id', 'reason_for_aid', 'received_support_from_others', 'received_scholarship', 'scholarship_details',
        'how_fees_paid_before', 'course_name', 'branch', 'edu_stage', 'course_duration_years', 'course_duration_months',
        'enrolment_year', 'current_year_or_sem', 'current_number', 'total_course_fees', 'current_year_semester_fees', 'last_fees_submission_date', 'fees_submission_status', 'semester_year_amount', 'fees_type', 'fees_amount',
        'amount_needed', 'support_required', 'college_name', 'college_address', 'account_no', 'account_name',
        'bank_name', 'bank_branch', 'ifsc_code', 'is_submitted', 'submitted_at', 'fees_entries', 'status',
        'approved_date', 'closed_date', 'application_type', 'is_recurred_from', 'is_recurred', 'admin_message', 'admin_message_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function financeRecords()
    {
        return $this->hasMany(FinanceRecord::class, 'student_application_id');
    }

    /**
     * Check if payment is completed
     * Returns true if total paid amount exactly equals amount_needed
     */
    public function isPaymentCompleted()
    {
        if (!$this->amount_needed || $this->amount_needed <= 0) {
            return false;
        }
        
        $totalPaid = $this->financeRecords()->sum('amount');
        return $totalPaid == $this->amount_needed;
    }

    /**
     * Get the payment completion status
     * Returns 'completed', 'pending', or 'no_amount_needed'
     */
    public function getPaymentStatus()
    {
        if (!$this->amount_needed || $this->amount_needed <= 0) {
            return 'no_amount_needed';
        }
        
        return $this->isPaymentCompleted() ? 'completed' : 'pending';
    }

    public function getFeesEntriesAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        return $value ? json_decode($value, true) : [];
    }

    public function setFeesEntriesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['fees_entries'] = json_encode($value);
        } else {
            $this->attributes['fees_entries'] = $value;
        }
    }

    /**
     * Get the application status text.
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            0 => 'Pending',
            1 => 'Eligible',
            2 => 'Approved',
            3 => 'Rejected',
            4 => 'Accepted',
            5 => 'Closed',
            default => 'Draft'
        };
    }

    /**
     * Get the application status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            1 => 'bg-warning',
            2 => 'bg-success',
            3 => 'bg-danger',
            4 => 'bg-primary',
            5 => 'bg-secondary',
            default => 'bg-light text-dark'
        };
    }

    /**
     * Check if application is submitted.
     */
    public function isSubmitted()
    {
        return $this->is_submitted == 1;
    }

    /**
     * Check if application is draft.
     */
    public function isDraft()
    {
        return $this->is_submitted == 0;
    }

    public function applicationRequests()
    {
        return $this->hasMany(ApplicationRequest::class, 'application_id');
    }

    public function latestApprovedApplicationRequest()
    {
        return $this->hasOne(ApplicationRequest::class, 'application_id')->where('status', 'approved')->latestOfMany();
    }

    public function approvalRecords()
    {
        return $this->hasMany(ApprovalRecord::class, 'student_application_id');
    }

    public function applicationComments()
    {
        return $this->hasMany(ApplicationComment::class, 'student_application_id');
    }

    /**
     * Get the application this was recurred from.
     */
    public function recurredFrom()
    {
        return $this->belongsTo(StudentApplication::class, 'is_recurred_from');
    }

    /**
     * Get applications that were recurred from this one.
     */
    public function recurredApplications()
    {
        return $this->hasMany(StudentApplication::class, 'is_recurred_from');
    }

    public function hasNewRecurringApplication()
    {
        if (!$this->case_id) {
            return false;
        }
        
        // Check if there's a newer application with the same case ID
        return StudentApplication::where('case_id', 'LIKE', $this->case_id . '-%')
            ->where('id', '>', $this->id)
            ->exists();
    }

}
