@component('admin.layouts.main')

@slot('title')
    Enquiry Details - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
    $usr = auth()->user();
@endphp

@slot('headerBlock')
<style>
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #ebedf2;
        padding: 1.5rem;
    }
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
    }
    .table {
        margin-bottom: 0;
    }
    .table th, .table td {
        vertical-align: middle;
        padding: 1rem;
    }
    .btn-primary {
        background-color: #5e50ee;
        border-color: #5e50ee;
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        background-color: #4a3ed4;
        border-color: #4a3ed4;
    }
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    .status-highlight {
        font-size: 1.25rem;
        font-weight: 700;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        display: inline-block;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-eligible {
        background-color: #d4edda;
        color: #155724;
    }
    .status-approved {
        background-color: #cce5ff;
        color: #004085;
    }
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-closed {
        background-color: #e2e3e5;
        color: #383d41;
    }
    .status-badge {
        font-size: 0.875rem;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        display: inline-block;
    }
    .info-label {
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1rem;
        color: #1a1a1a;
    }
    .modal-content {
        border-radius: 0.5rem;
    }
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 1.5rem;
    }
    /* Style for comment list items */
    .list-group-item {
        background-color: #e3f2fd !important;
        border: 1px solid #90caf9;
        margin-bottom: 0.5rem;
        border-radius: 0.375rem !important;
    }
    @media print {
        .no-print {
            display: none;
        }
    }
</style>
@endslot

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">{{ $student->first_name.' '.$student->surname.' ('.$student->email.')' }}</h4>
            <div class="d-flex align-items-center gap-3">
                @if($currentApplication)
                @php
                    $statusText = 'Pending';
                    $statusClass = 'status-badge status-pending';
                    if ($currentApplication->status == 1) {
                        $statusText = 'Eligible';
                        $statusClass = 'status-badge status-eligible';
                    } elseif ($currentApplication->status == 2) {
                        $statusText = 'Approved';
                        $statusClass = 'status-badge status-approved';
                    } elseif ($currentApplication->status == 3) {
                        $statusText = 'Rejected';
                        $statusClass = 'status-badge status-rejected';
                    } elseif ($currentApplication->status == 4) {
                        $statusText = 'Accepted/Go To Finance';
                        $statusClass = 'status-badge status-approved';
                    } elseif ($currentApplication->status == 5) {
                        $statusText = 'Closed';
                        $statusClass = 'status-badge status-closed';
                    }
                @endphp
                <span>Current Status: <span class="status-highlight {{ $statusClass }}">{{ $statusText }}</span></span>
                @endif
                @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.edit')))
                <a href="{{ route($ADMIN_ROUTE_NAME.'.students.edit', CustomHelper::encrypt($student->id)) }}" class="btn btn-primary btn-sm">
                    <i class="ti tabler-pencil me-1"></i> Edit Student
                </a>
                @endif
                <a href="{{ route($routeName . '.students.index') }}" class="btn btn-secondary btn-sm">Back</a>
            </div>
        </div>
    </div>

    <!-- Student Details -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Student Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $student->first_name }} {{ $student->surname }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Family Lineage</div>
                        <div class="info-value">{{ $student->family_lineage }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ $student->gender }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $student->email }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Contact No</div>
                        <div class="info-value">{{ $student->contact_no }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="info-label">Address</div>
                        <div class="info-value">{{ $student->address ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">State</div>
                        <div class="info-value">{{ $student->state }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">District</div>
                        <div class="info-value">{{ $student->district }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Pincode</div>
                        <div class="info-value">{{ $student->pincode ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Father/Guardian's Full Name</div>
                        <div class="info-value">{{ $student->guardian_name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Guardian Contact</div>
                        <div class="info-value">{{ $student->guardian_contact_no ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Guardian Occupation</div>
                        <div class="info-value">{{ $student->guardian_occupation ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Monthly Income</div>
                        <div class="info-value">{{ $student->monthly_income ? number_format($student->monthly_income, 0) : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Family Members</div>
                        <div class="info-value">{{ $student->family_members ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Status</div>
                        <div class="info-value">{{ $student->status ? 'Active' : 'Inactive' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Details -->
    @if($currentApplication)
    <!-- Financial Details -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Financial Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="info-label">Reason for Aid</div>
                        <div class="info-value">{{ $currentApplication->reason_for_aid ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Received Support from Others</div>
                        <div class="info-value">{{ $currentApplication->received_support_from_others }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="info-label">Received Scholarship</div>
                        <div class="info-value">{{ $currentApplication->received_scholarship }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Scholarship Details</div>
                        <div class="info-value">{{ $currentApplication->scholarship_details ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">How Fees Paid Before</div>
                        <div class="info-value">{{ $currentApplication->how_fees_paid_before ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Details -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Course Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="info-label">Course Name</div>
                        <div class="info-value">{{ $currentApplication->course_name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Branch</div>
                        <div class="info-value">{{ $currentApplication->branch ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Education Stage</div>
                        <div class="info-value">{{ $currentApplication->edu_stage ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Course Duration</div>
                        <div class="info-value">{{ $currentApplication->course_duration_years ?? 'N/A' }} Year(s) {{ $currentApplication->course_duration_months ?? 'N/A' }} Month(s)</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Enrollment Year</div>
                        <div class="info-value">{{ $currentApplication->enrolment_year ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">What Year or Semester are you presently enrolled in?</div>
                        <div class="info-value">
                            @if($currentApplication->current_number)
                                {{ $currentApplication->current_number }}
                                @if($currentApplication->current_number == 1)st
                                @elseif($currentApplication->current_number == 2)nd
                                @elseif($currentApplication->current_number == 3)rd
                                @else th
                                @endif
                                {{ $currentApplication->current_year_or_sem ?? '' }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Total Course Fees</div>
                        <div class="info-value">{{ $currentApplication->total_course_fees ? 'Rs. ' . number_format($currentApplication->total_course_fees, 0) : 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="info-label">Current Year/Semester Fees</div>
                        <div class="info-value">{{ $currentApplication->current_year_semester_fees ? 'Rs. ' . number_format($currentApplication->current_year_semester_fees, 0) : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Last date of Fees/Amount Submission</div>
                        <div class="info-value">
                            @if($currentApplication->fees_submission_status == 'date' && $currentApplication->last_fees_submission_date)
                                {{ \Carbon\Carbon::parse($currentApplication->last_fees_submission_date)->format('d/m/Y') }}
                            @else
                                Not yet known
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Semester/Year Amount</div>
                        <div class="info-value">{{ $currentApplication->semester_year_amount ? 'Rs. ' . number_format($currentApplication->semester_year_amount, 0) : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Amount Needed</div>
                        <div class="info-value">{{ $currentApplication->amount_needed ? 'Rs. ' . number_format($currentApplication->amount_needed, 0) : 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Support Required</div>
                        <div class="info-value">{{ $currentApplication->support_required ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">College Name</div>
                        <div class="info-value">{{ $currentApplication->college_name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">College Address</div>
                        <div class="info-value">{{ $currentApplication->college_address ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <h6 class="mt-4 mb-3">Fee Entries/Breakup</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Purpose</th>
                            <th>Description</th>
                            <th>Duration</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $totalAmount = 0; 
                        @endphp

                        @foreach($currentApplication->fees_entries ?? [] as $entry)
                            @php 
                                $totalAmount += $entry['fees_amount']; 
                            @endphp
                            <tr>
                                <td>{{ $entry['fees_purpose'] }}</td>
                                <td>{{ $entry['fees_purpose_description'] ?? 'N/A' }}</td>
                                <td>{{ $entry['fees_duration'] }}</td>
                                <td>{{ number_format($entry['fees_amount'], 0) }}</td>
                            </tr>
                        @endforeach

                        <!-- Total Row -->
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total Amount</td>
                            <td class="fw-bold">{{ number_format($totalAmount, 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bank Details -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Bank Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="info-label">Account Number</div>
                        <div class="info-value">{{ $currentApplication->account_no ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Account Name</div>
                        <div class="info-value">{{ $currentApplication->account_name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="info-label">Bank Name</div>
                        <div class="info-value">{{ $currentApplication->bank_name ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Bank Branch</div>
                        <div class="info-value">{{ $currentApplication->bank_branch ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">IFSC Code</div>
                        <div class="info-value">{{ $currentApplication->ifsc_code ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Documents</h5>
        </div>
        <div class="card-body">
            @if($documents->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>College</th>
                            <th>Academic Year</th>
                            <th>Percentage</th>
                            <th>Document Name</th>
                            <th class="no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                        <tr>
                            <td>{{ $document->course_name ?? 'N/A' }}</td>
                            <td>{{ $document->college ?? 'N/A' }}</td>
                            <td>{{ $document->academic_year ?? 'N/A' }}</td>
                            <td>{{ $document->percentage ?? 'N/A' }}</td>
                            <td>{{ $document->document_name ?? 'N/A' }}</td>
                            <td class="no-print">
                                <a href="{{ asset('storage/' . $document->document_file) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">No documents uploaded for this application.</p>
            @endif
        </div>
    </div>

    @if($currentApplication)
    <!-- Action Buttons for Current Application -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Actions for Current Application</h5>
            <div>
                @php
                    $alreadyApproved = $approvals->contains('created_by', auth()->id());
                    $isApprover = auth()->user()->role === 'Approver';
                    $statusEligible = in_array($currentApplication->status, [1, 2]);
                    $isApplicationClosed = $currentApplication->status == 5;
                @endphp

                @if(!$isApplicationClosed)
                    @if($currentApplication->is_submitted == 1)
                        @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('students.edit'))
                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#messageModal">
                                <i class="fas fa-envelope me-1"></i> Message Student
                            </button>
                        @endif

                        @if(!$alreadyApproved && (!$isApprover || ($isApprover && $statusEligible)))
                            @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.approve')) || (auth()->user()->can('students.reject')))
                            <button class="btn btn-danger btn-lg no-print me-2" data-bs-toggle="modal" data-bs-target="#approvalModal">Add Approval Info</button>
                            @endif
                        @endif

                        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.approve')) || (auth()->user()->can('students.reject')))
                        <button class="btn btn-danger btn-lg no-print" data-bs-toggle="modal" data-bs-target="#commentModal">Add Comment</button>
                        @endif
                    @endif
                @else
                    <span class="badge bg-secondary">Application Closed - No Actions Available</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if(!$isApplicationClosed)
                <p class="text-muted">Use the buttons above to add approval information or comments for the current application.</p>
            @else
                <p class="text-muted">This application is closed. No further actions can be performed.</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Approval Info -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Update</h5>
        </div>
        <div class="card-body">
            <!-- Current Application Approvals -->
            <h6 class="text-danger mb-3"><b>Current Application (#{{ $currentApplication->application_number }})</b></h6>
            @if($approvals->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Eligibility</th>
                            <th>Amount</th>
                            <th>Recommended By</th>
                            <th>Reviewed At</th>
                            <th>Reviewer Name</th>
                            <th>Reviewer Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvals as $approval)
                        <tr>
                            @if($approval->user->role == "Approver")
                                <td>{{ $approval->is_eligible ? 'Approved' : 'Not Approved' }}</td>
                            @elseif($approval->user->role == "SuperAdmin")
                                <td>{{ $approval->is_eligible ? 'Accepted' : 'Not Accepted' }}</td>
                            @else
                                <td>{{ $approval->is_eligible ? 'Eligible' : 'Not Eligible' }}</td>
                            @endif
                            <td>{{ number_format($approval->amount, 0) }}</td>
                            <td>{{ $approval->recommended_by ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($approval->created_at)->format('d M Y h:i A') }}</td>
                            <td>{{ $approval->user->name ?? 'N/A' }}</td>
                            <td>{{ $approval->user->role ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">No approval recorded for this application.</p>
            @endif

            <!-- Previous Applications Approvals -->
            @if(!empty($previousApprovalsGrouped))
            <hr class="my-4">
            <h6 class="text-secondary mb-3">Previous Applications</h6>
            @foreach($previousApprovalsGrouped as $group)
            <div class="mb-4">
                <h6 class="text-info mb-2">
                    Application #{{ $group['application']->application_number }}
                    <small class="text-muted">({{ \Carbon\Carbon::parse($group['application']->created_at)->format('d M Y') }})</small>
                    <span class="badge {{ $group['application']->status_badge_class }} ms-2">{{ $group['application']->status_text }}</span>
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Eligibility</th>
                                <th>Amount</th>
                                <th>Recommended By</th>
                                <th>Reviewed At</th>
                                <th>Reviewer Name</th>
                                <th>Reviewer Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['approvals'] as $approval)
                            <tr>
                                @if($approval->user->role == "Approver")
                                    <td>{{ $approval->is_eligible ? 'Approved' : 'Not Approved' }}</td>
                                @elseif($approval->user->role == "SuperAdmin")
                                    <td>{{ $approval->is_eligible ? 'Accepted' : 'Not Accepted' }}</td>
                                @else
                                    <td>{{ $approval->is_eligible ? 'Eligible' : 'Not Eligible' }}</td>
                                @endif
                                <td>{{ number_format($approval->amount, 0) }}</td>
                                <td>{{ $approval->recommended_by ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($approval->created_at)->format('d M Y h:i A') }}</td>
                                <td>{{ $approval->user->name ?? 'N/A' }}</td>
                                <td>{{ $approval->user->role ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            @endif
        </div>
    </div>

    <!-- Feedbacks/Comments -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Feedbacks/Comments</h5>
        </div>
        <div class="card-body">
            <!-- Current Application Comments -->
            <h6 class="text-danger mb-3"><b>Current Application (#{{ $currentApplication->application_number }})</b></h6>
            @if($comments->isNotEmpty())
            <ul class="list-group list-group-flush">
                @foreach($comments as $comment)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $comment->user->name ?? 'User' }}</strong>
                            <span class="text-muted">({{ $comment->user->role ?? 'N/A' }})</span>
                            @if(!empty($comment->show_in_front))
                                <span class="badge bg-success ms-2">Show in Front</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->format('d M Y h:i A') }}</small>
                    </div>
                    <p class="mt-2 mb-0">{{ $comment->comment }}</p>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted">No comments added for this application.</p>
            @endif

            <!-- Previous Applications Comments -->
            @if(!empty($previousCommentsGrouped))
            <hr class="my-4">
            <h6 class="text-secondary mb-3">Previous Applications</h6>
            @foreach($previousCommentsGrouped as $group)
            <div class="mb-4">
                <h6 class="text-info mb-2">
                    Application #{{ $group['application']->application_number }}
                    <small class="text-muted">({{ \Carbon\Carbon::parse($group['application']->created_at)->format('d M Y') }})</small>
                    <span class="badge {{ $group['application']->status_badge_class }} ms-2">{{ $group['application']->status_text }}</span>
                </h6>
                <ul class="list-group list-group-flush">
                    @foreach($group['comments'] as $comment)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $comment->user->name ?? 'User' }}</strong>
                                <span class="text-muted">({{ $comment->user->role ?? 'N/A' }})</span>
                                @if(!empty($comment->show_in_front))
                                    <span class="badge bg-success ms-2">Show in Front</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($comment->created_at)->format('d M Y h:i A') }}</small>
                        </div>
                        <p class="mt-2 mb-0">{{ $comment->comment }}</p>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
            @endif
        </div>
    </div>
    @else
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        No application details available for this student.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Approval Modal -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="approvalForm">
                @csrf
                <input type="hidden" name="student_application_id" value="{{ $currentApplication->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Approval Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Eligibility</label>
                            <select name="is_eligible" class="form-select" required>
                                <option value="">Please Select</option>
                                @if($usr->hasRole('Approver'))
                                    <option value="1">Approve</option>
                                    <option value="0">Disapprove</option>
                                @elseif($usr->hasRole('SuperAdmin'))
                                    <option value="1">Accept</option>
                                    <option value="0">Reject</option>
                                @else
                                    <option value="1">Eligible</option>
                                    <option value="0">Not Eligible</option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control">
                        </div>
                        @if($usr->hasRole('Inspector'))
                        <div class="mb-3">
                            <label class="form-label">Recommended By</label>
                            <input type="text" name="recommended_by" class="form-control">
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Comment</label>
                            <textarea name="comment" class="form-control" rows="5"></textarea>
                        </div>
                        @if($usr->hasRole('SuperAdmin'))
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="approvalShowInFront" name="show_in_front">
                            <label class="form-check-label" for="approvalShowInFront">
                                Show in Front
                            </label>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Approval</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Comment Modal -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="commentForm">
                @csrf
                <input type="hidden" name="student_application_id" value="{{ $currentApplication->id }}">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Comment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Comment</label>
                            <textarea name="comment" class="form-control" rows="5" required></textarea>
                        </div>
                        @if($usr->hasRole('SuperAdmin'))
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="commentShowInFront" name="show_in_front">
                            <label class="form-check-label" for="commentShowInFront">
                                Show in Front
                            </label>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Comment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.students.message_modal')

@slot('footerBlock')
<script>
    $(document).ready(function() {
        $('#approvalForm').submit(function(e) {
            e.preventDefault();

            $.post("{{ route($routeName.'.students.storeApproval') }}", $(this).serialize())
                .done(function(response) {
                    if (response.success) {
                        toastr.success(response.message ?? 'Approval recorded!');
                        $('#approvalModal').modal('hide');
                        location.reload();
                    } else {
                        toastr.error(response.message ?? 'Something went wrong. Please try again.');
                    }
                })
                .fail(function(xhr) {
                    let errorMsg = 'Error saving approval.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errorMsg);
                });
        });

        $('#commentForm').submit(function(e) {
            e.preventDefault();
            $.post("{{ route($routeName.'.students.storeComment') }}", $(this).serialize(), function(response) {
                toastr.success('Comment added!');
                $('#commentModal').modal('hide');
                location.reload();
            }).fail(function(xhr) {
                toastr.error('Error adding comment.');
            });
        });

        // Auto-open Add Approval Info modal for SuperAdmin when allowed
        @php
            $alreadyApproved = $approvals->contains('created_by', auth()->id());
            $isApplicationClosed = $currentApplication ? $currentApplication->status == 5 : true;
        @endphp
        @if($usr->hasRole('SuperAdmin') && !$alreadyApproved && !$isApplicationClosed)
            //$('#approvalModal').modal('show');
        @endif

        // Handle message form submission
        $('#messageForm').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalBtnText = submitBtn.html();
            
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#messageModal').modal('hide');
                        form[0].reset();
                        // Reload the page to show the new message
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error(response.message || 'An error occurred while sending the message.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while sending the message.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
    });
</script>
@endslot

@endcomponent
