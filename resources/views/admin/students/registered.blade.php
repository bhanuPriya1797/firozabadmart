@component('admin.layouts.main')
    @slot('title')
        {{ $page_title }}
    @endslot

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-12">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ $page_title }}</h5>
                                <p class="mb-4">Manage all registered students in the system.</p>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="studentsTable">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Contact No</th>
                                                <th>Family Lineage</th>
                                                <th>Status</th>
                                                <th>Applications</th>
                                                <th>Registration Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Applications Modal -->
    <div class="modal fade" id="applicationsModal" tabindex="-1" aria-labelledby="applicationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applicationsModalLabel">Student Applications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="applicationsContent">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('footerBlock')
        <script>
            $(document).ready(function() {
                var table = $('#studentsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route($ADMIN_ROUTE_NAME.'.students.registered') }}",
                    columns: [
                        {data: 'name', name: 'name'},
                        {data: 'email', name: 'email'},
                        {data: 'contact_no', name: 'contact_no'},
                        {data: 'family_lineage', name: 'family_lineage'},
                        {data: 'status', name: 'status'},
                        {data: 'applications_count', name: 'applications_count', orderable: false, searchable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    order: [[6, 'desc']]
                });

                // Toggle Status
                $(document).on('click', '.toggle-status', function() {
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
                                        );
                                        table.ajax.reload();
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
                $(document).on('click', '.btn-delete-student', function() {
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
                                        );
                                        table.ajax.reload();
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

                // Show Applications Modal
                window.showApplicationsModal = function(studentId) {
                    $('#applicationsModal').modal('show');
                    $('#applicationsContent').html(`
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `);

                    $.ajax({
                        url: "{{ route($ADMIN_ROUTE_NAME.'.students.applications', ':studentId') }}".replace(':studentId', studentId),
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                // Check if there are any closed applications to determine if we need extra columns
                                let hasClosedApplications = response.applications.some(app => app.status === 5);
                                let colspan = hasClosedApplications ? "8" : "6";
                                
                                let content = `
                                    <h6 class="mb-3">Applications for: <strong>${response.student_name}</strong></h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Application #</th>
                                                    <th>Course</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Submitted</th>`;
                                
                                if (hasClosedApplications) {
                                    content += `
                                                    <th>Amount Needed</th>
                                                    <th>Finance Total</th>`;
                                }
                                
                                content += `
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                                if (response.applications.length > 0) {
                                    response.applications.forEach(function(app) {
                                        let parentInfo = app.is_recurred_from ? `<br><small class="text-muted">Recurring from: ${app.parent_application_number}</small>` : '';
                                        
                                        content += `
                                            <tr>
                                                <td>
                                                    ${app.application_number}
                                                    ${parentInfo}
                                                </td>
                                                <td>${app.course_name}</td>
                                                <td><span class="badge ${app.type_class}">${app.type_text}</span></td>
                                                <td><span class="badge ${app.status_class}">${app.status_text}</span></td>
                                                <td>${app.submitted_at}</td>`;
                                        
                                        if (hasClosedApplications) {
                                            if (app.status === 5) {
                                                // Show amounts for closed applications
                                                content += `
                                                <td>₹${app.amount_needed ? parseFloat(app.amount_needed).toLocaleString() : '0'}</td>
                                                <td>₹${app.total_finance_amount ? parseFloat(app.total_finance_amount).toLocaleString() : '0'}</td>`;
                                            } else {
                                                // Show dashes for non-closed applications
                                                content += `
                                                <td class="text-muted">-</td>
                                                <td class="text-muted">-</td>`;
                                            }
                                        }
                                        
                                        content += `
                                                <td>
                                                    <a href="${app.view_url}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="ti tabler-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        `;
                                    });
                                } else {
                                    content += `
                                        <tr>
                                            <td colspan="${colspan}" class="text-center text-muted">No applications found</td>
                                        </tr>
                                    `;
                                }

                                content += `
                                            </tbody>
                                        </table>
                                    </div>
                                `;

                                $('#applicationsContent').html(content);
                            } else {
                                $('#applicationsContent').html(`
                                    <div class="alert alert-danger">
                                        <i class="ti ti-alert-circle"></i> ${response.message}
                                    </div>
                                `);
                            }
                        },
                        error: function() {
                            $('#applicationsContent').html(`
                                <div class="alert alert-danger">
                                    <i class="ti tabler-alert-circle"></i> Error loading applications. Please try again.
                                </div>
                            `);
                        }
                    });
                };
            });
        </script>
    @endslot
@endcomponent
