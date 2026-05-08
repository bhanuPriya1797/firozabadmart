@component('admin.layouts.main')
    @slot('title')
        Student Details - {{ $student->first_name }} {{ $student->surname }}
    @endslot

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
        .info-label {
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-size: 1rem;
            color: #1a1a1a;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }
        .status-badge {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            display: inline-block;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
    @endslot

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ $student->first_name.' '.$student->surname.' ('.$student->email.')' }}</h4>
                <div class="d-flex align-items-center gap-3">
                    <span>Status: 
                        @if($student->status == 1)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-pending">Pending Activation</span>
                        @endif
                    </span>
                    @php
                        $backRoute = request()->get('back_to', 'registered');
                        $backUrl = route($ADMIN_ROUTE_NAME.'.students.' . $backRoute);
                    @endphp
                    <a href="{{ $backUrl }}" class="btn btn-secondary btn-sm">
                        <i class="ti tabler-arrow-left me-1"></i> Back to List
                    </a>
                    @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.edit')))
                    <a href="{{ route($ADMIN_ROUTE_NAME.'.students.edit', CustomHelper::encrypt($student->id)) }}" class="btn btn-primary btn-sm">
                        <i class="ti tabler-pencil me-1"></i> Edit Student
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid-container">
            <!-- Personal Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">{{ $student->first_name }} {{ $student->surname }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $student->email }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Phone</div>
                                <div class="info-value">{{ $student->contact_no }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Gender</div>
                                <div class="info-value">{{ $student->gender }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : 'Not provided' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Family Lineage</div>
                                <div class="info-value">{{ $student->family_lineage }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Student ID</div>
                                <div class="info-value">{{ $student->id }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Account Status</div>
                                <div class="info-value">
                                    @if($student->status == 1)
                                        <span class="status-badge status-active">Active</span>
                                    @else
                                        <span class="status-badge status-pending">Pending Approval</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Registration Date</div>
                                <div class="info-value">{{ $student->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Last Updated</div>
                                <div class="info-value">{{ $student->updated_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Account Status</div>
                                <div class="info-value">
                                    @if($student->status == 1)
                                        <span class="status-badge status-active">Active</span>
                                    @else
                                        <span class="status-badge status-pending">Pending Approval</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Student ID</div>
                                <div class="info-value">{{ $student->id }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            @if($student->address || $student->state || $student->district || $student->pincode)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Address Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Address</div>
                                <div class="info-value">{{ $student->address ?: 'Not provided' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">State</div>
                                <div class="info-value">{{ $student->state ?: 'Not provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">District</div>
                                <div class="info-value">{{ $student->district ?: 'Not provided' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Pincode</div>
                                <div class="info-value">{{ $student->pincode ?: 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Guardian Information -->
            @if($student->guardian_name || $student->guardian_contact_no || $student->guardian_occupation)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Guardian Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Guardian Name</div>
                                <div class="info-value">{{ $student->guardian_name ?: 'Not provided' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Guardian Phone</div>
                                <div class="info-value">{{ $student->guardian_contact_no ?: 'Not provided' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Guardian Occupation</div>
                                <div class="info-value">{{ $student->guardian_occupation ?: 'Not provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Monthly Income</div>
                                <div class="info-value">{{ $student->monthly_income ? '₹' . number_format($student->monthly_income) : 'Not provided' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Family Members</div>
                                <div class="info-value">{{ $student->family_members ?: 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.edit')))
                    <a href="{{ route($ADMIN_ROUTE_NAME.'.students.edit', CustomHelper::encrypt($student->id)) }}" class="btn btn-primary">
                        <i class="ti tabler-pencil me-1"></i> Edit Student
                    </a>
                    @endif
                    
                    @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('students.activate') || auth()->user()->can('students.deactivate'))
                        <button type="button" class="btn btn-warning toggle-status-btn" 
                                data-id="{{ $student->id }}" 
                                data-status="{{ $student->status }}">
                            <i class="ti tabler-toggle-right me-1"></i> 
                            {{ $student->status == 1 ? 'Deactivate' : 'Activate' }} Student
                        </button>
                    @endif
                    
                    @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.delete')))
                    <button type="button" class="btn btn-danger delete-student-btn" data-id="{{ $student->id }}" data-url="{{ route($ADMIN_ROUTE_NAME.'.students.destroy', CustomHelper::encrypt($student->id)) }}">
                        <i class="ti tabler-trash me-1"></i> Delete Student
                    </button>
                    @endif
                    
                    <a href="{{ $backUrl }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    @slot('footerBlock')
        <script>
            $(document).ready(function() {
                // Toggle Status
                $('.toggle-status-btn').on('click', function() {
                    var id = $(this).data('id');
                    var status = $(this).data('status');
                    var statusText = status == 1 ? 'deactivate' : 'activate';
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to ' + statusText + ' this student?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, ' + statusText + '!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route($ADMIN_ROUTE_NAME.'.students.toggle-status') }}",
                                type: 'POST',
                                data: {
                                    id: id,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'Success!',
                                            response.message,
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function() {
                                    Swal.fire(
                                        'Error!',
                                        'Error updating status.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });

                // Delete Student
                $('.delete-student-btn').on('click', function() {
                    var url = $(this).data('url');
                    var id = $(this).data('id');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to delete this student? This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'Deleted!',
                                            response.message,
                                            'success'
                                        ).then(() => {
                                            window.location.href = "{{ route($ADMIN_ROUTE_NAME.'.students.registered') }}";
                                        });
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function() {
                                    Swal.fire(
                                        'Error!',
                                        'Error deleting student.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endslot
@endcomponent
