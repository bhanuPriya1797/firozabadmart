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
                                <p class="mb-4">Manage all students with recurring applications in the system.</p>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="studentsTable">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Contact No</th>
                                                <th>Family Lineage</th>
                                                <th>Status</th>
                                                <th>Main Application</th>
                                                <th>Recurring Applications</th>
                                                <th>Registration Date</th>
                                                <!-- <th>Action</th> -->
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
                    ajax: "{{ route($ADMIN_ROUTE_NAME.'.students.recurring') }}",
                    columns: [
                        {data: 'name', name: 'name'},
                        {data: 'email', name: 'email'},
                        {data: 'contact_no', name: 'contact_no'},
                        {data: 'family_lineage', name: 'family_lineage'},
                        {data: 'status', name: 'status'},
                        {data: 'main_application', name: 'main_application', orderable: false, searchable: false},
                        {data: 'recurring_applications', name: 'recurring_applications', orderable: false, searchable: false},
                        {data: 'created_at', name: 'created_at'},
                        // {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    order: [[7, 'desc']]
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
                                    _token: '{{ csrf_token() }}',
                                    id: id
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
                                        'Something went wrong.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });

                // Delete Student
                $(document).on('click', '.btn-delete-student', function() {
                    var id = $(this).data('id');
                    var url = $(this).data('url');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This action cannot be undone!',
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
                                            'Student has been deleted.',
                                            'success'
                                        );
                                        table.ajax.reload();
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message || 'Something went wrong.',
                                            'error'
                                        );
                                    }
                                },
                                error: function() {
                                    Swal.fire(
                                        'Error!',
                                        'Something went wrong.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });

            // Function to show applications modal
            function showApplicationsModal(studentId, studentName) {
                $('#applicationsModalLabel').text('Applications for ' + studentName);
                $('#applicationsContent').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                $('#applicationsModal').modal('show');
                
                $.ajax({
                    url: "{{ route($ADMIN_ROUTE_NAME.'.students.applications', ':id') }}".replace(':id', studentId),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var html = '';
                            if (response.applications.length > 0) {
                                // Check if there are any closed applications to determine if we need extra columns
                                let hasClosedApplications = response.applications.some(app => app.status === 5);
                                let colspan = hasClosedApplications ? "8" : "6";
                                
                                html += '<div class="table-responsive">';
                                html += '<table class="table table-sm">';
                                html += '<thead><tr><th>Application #</th><th>Course</th><th>Type</th><th>Status</th><th>Submitted</th>';
                                
                                // Add amount columns if there are closed applications
                                if (hasClosedApplications) {
                                    html += '<th>Amount Needed</th><th>Finance Total</th>';
                                }
                                
                                html += '<th>Action</th></tr></thead>';
                                html += '<tbody>';
                                
                                response.applications.forEach(function(app) {
                                    html += '<tr>';
                                    html += '<td>' + app.application_number + '</td>';
                                    html += '<td>' + app.course_name + '</td>';
                                    html += '<td><span class="badge ' + app.type_class + '">' + app.type_text + '</span></td>';
                                    html += '<td><span class="badge ' + app.status_class + '">' + app.status_text + '</span></td>';
                                    html += '<td>' + app.submitted_at + '</td>';
                                    
                                    // Add amount columns if there are closed applications
                                    if (hasClosedApplications) {
                                        if (app.status === 5) {
                                            // Show amounts for closed applications
                                            html += '<td>₹' + (app.amount_needed ? parseFloat(app.amount_needed).toFixed(2) : '0.00') + '</td>';
                                            html += '<td>₹' + (app.total_finance_amount ? parseFloat(app.total_finance_amount).toFixed(2) : '0.00') + '</td>';
                                        } else {
                                            // Show dashes for non-closed applications
                                            html += '<td>-</td><td>-</td>';
                                        }
                                    }
                                    
                                    html += '<td><a href="' + app.view_url + '" class="btn btn-sm btn-outline-primary">View</a></td>';
                                    html += '</tr>';
                                });
                                
                                html += '</tbody></table></div>';
                            } else {
                                html = '<p class="text-center text-muted">No applications found.</p>';
                            }
                            $('#applicationsContent').html(html);
                        } else {
                            $('#applicationsContent').html('<p class="text-center text-danger">Error loading applications.</p>');
                        }
                    },
                    error: function() {
                        $('#applicationsContent').html('<p class="text-center text-danger">Error loading applications.</p>');
                    }
                });
            }
        </script>
    @endslot
@endcomponent
