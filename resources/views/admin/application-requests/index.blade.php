@component('admin.layouts.main')

@slot('title')
    Application Requests - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Application Requests Management</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-refresh-table">
                <i class="ti tabler-refresh me-1"></i> Refresh
            </button>
        </div>
    </div>

    <div class="card">
        <!-- Filters -->
        <div class="card-header border-bottom pb-2">
            <form class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter-status" class="form-label">Status</label>
                    <select class="form-select" id="filter-status">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-request-type" class="form-label">Request Type</label>
                    <select class="form-select" id="filter-request-type">
                        <option value="">All Types</option>
                        <option value="new">New Application</option>
                        <option value="recurring">Recurring Application</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-start-date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="filter-start-date">
                </div>
                <div class="col-md-3">
                    <label for="filter-end-date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="filter-end-date">
                </div>
            </form>
        </div>

        <div class="card-datatable table-responsive pt-0">
            <table class="table table-striped table-hover" id="application-requests-table">
                <thead>
                    <tr>
                        <th>Student Details</th>
                        <th>Request Type</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Approved At</th>
                        <th>Approved By</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Handled by DataTable --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Request Details Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRequestModalLabel">Application Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="request-details-content">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Request Modal -->
<div class="modal fade" id="approveRequestModal" tabindex="-1" aria-labelledby="approveRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveRequestModalLabel">Approve Application Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approve-request-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approve-admin-notes" class="form-label">Admin Notes (Optional)</label>
                        <textarea class="form-control" id="approve-admin-notes" name="admin_notes" rows="3" placeholder="Add any notes for the student..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="ti tabler-info-circle me-2"></i>
                        The student will receive an email notification about the approval.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti tabler-check me-1"></i> Approve Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Request Modal -->
<div class="modal fade" id="rejectRequestModal" tabindex="-1" aria-labelledby="rejectRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectRequestModalLabel">Reject Application Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reject-request-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject-admin-notes" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject-admin-notes" name="admin_notes" rows="3" placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="ti tabler-alert-triangle me-2"></i>
                        The student will receive an email notification about the rejection with your notes.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti tabler-x me-1"></i> Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
$(function () {
    let table = $('#application-requests-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href,
            data: function (d) {
                d.status = $('#filter-status').val();
                d.request_type = $('#filter-request-type').val();
                d.start_date = $('#filter-start-date').val();
                d.end_date = $('#filter-end-date').val();
            }
        },
        columns: [
            { data: 'student_details', name: 'student_details', orderable: false, searchable: false },
            { data: 'request_type_badge', name: 'request_type', searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'requested_at_formatted', name: 'requested_at' },
            { data: 'approved_at_formatted', name: 'approved_at', searchable: false },
            { data: 'approved_by_name', name: 'approved_by_name', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
        order: [[3, 'desc']], // Order by requested_at descending
        language: {
            searchPlaceholder: 'Search students...',
            emptyTable: 'No application requests found.',
            processing: 'Loading...'
        }
    });

    // Filter change handlers
    $('#filter-status, #filter-request-type, #filter-start-date, #filter-end-date').on('change', function() {
        table.draw();
    });

    // Refresh button
    $('#btn-refresh-table').on('click', function() {
        table.ajax.reload();
    });

    // View request details
    $(document).on('click', '.btn-view-request', function() {
        const requestId = $(this).data('id');
        
        $.ajax({
            url: `{{ route($ADMIN_ROUTE_NAME . '.students.application-requests.show', ':id') }}`.replace(':id', requestId),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Student Information</h6>
                                <p><strong>Name:</strong> ${data.student.name}</p>
                                <p><strong>Email:</strong> ${data.student.email}</p>
                                <p><strong>Phone:</strong> ${data.student.contact_no || 'N/A'}</p>
                                <p><strong>Gender:</strong> ${data.student.gender || 'N/A'}</p>
                                <p><strong>Family Lineage:</strong> ${data.student.family_lineage || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Request Information</h6>
                                <p><strong>Type:</strong> <span class="badge ${data.request_type === 'recurring' ? 'bg-warning' : 'bg-info'}">${data.request_type === 'recurring' ? 'Recurring' : 'New'}</span></p>
                                <p><strong>Status:</strong> <span class="badge ${data.status === 'approved' ? 'bg-success' : data.status === 'rejected' ? 'bg-danger' : 'bg-warning'}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span></p>
                                <p><strong>Requested At:</strong> ${data.requested_at}</p>
                                ${data.approved_at ? `<p><strong>Approved At:</strong> ${data.approved_at}</p>` : ''}
                                ${data.approved_by ? `<p><strong>Approved By:</strong> ${data.approved_by}</p>` : ''}
                            </div>
                        </div>
                    `;
                    
                    if (data.application) {
                        content += `
                            <hr>
                            <h6>Previous Application Details</h6>
                            <p><strong>Application Number:</strong> ${data.application.application_number}</p>
                            <p><strong>Case ID:</strong> ${data.application.case_id}</p>
                            <p><strong>Status:</strong> ${data.application.status_text}</p>
                        `;
                    }
                    
                    if (data.admin_notes) {
                        content += `
                            <hr>
                            <h6>Admin Notes</h6>
                            <p>${data.admin_notes}</p>
                        `;
                    }
                    
                    $('#request-details-content').html(content);
                    $('#viewRequestModal').modal('show');
                } else {
                    toastr.error('Failed to load request details.');
                }
            },
            error: function() {
                toastr.error('Failed to load request details.');
            }
        });
    });

    // Approve request
    let currentRequestId = null;
    
    $(document).on('click', '.btn-approve-request', function() {
        currentRequestId = $(this).data('id');
        $('#approve-admin-notes').val('');
        $('#approveRequestModal').modal('show');
    });

    $('#approve-request-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            admin_notes: $('#approve-admin-notes').val(),
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: `{{ route($ADMIN_ROUTE_NAME . '.students.application-requests.approve', ':id') }}`.replace(':id', currentRequestId),
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#approveRequestModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.message || 'Failed to approve request.');
                }
            },
            error: function() {
                toastr.error('Failed to approve request.');
            }
        });
    });

    // Reject request
    $(document).on('click', '.btn-reject-request', function() {
        currentRequestId = $(this).data('id');
        $('#reject-admin-notes').val('');
        $('#rejectRequestModal').modal('show');
    });

    $('#reject-request-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            admin_notes: $('#reject-admin-notes').val(),
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: `{{ route($ADMIN_ROUTE_NAME . '.students.application-requests.reject', ':id') }}`.replace(':id', currentRequestId),
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#rejectRequestModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.message || 'Failed to reject request.');
                }
            },
            error: function() {
                toastr.error('Failed to reject request.');
            }
        });
    });
});
</script>
@endslot

@endcomponent