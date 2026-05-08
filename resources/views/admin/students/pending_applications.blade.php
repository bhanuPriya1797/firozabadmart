@php $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp
@component('admin.layouts.main')
@slot('title') Pending Applications - {{ config('app.name') }} @endslot
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Applications</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pending-applications-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Student Details</th>
                                    <th>Application Date</th>
                                    <th>Submitted Date</th>
                                    <th>Application Type</th>
                                    <th>Status</th>
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

<script>
$(document).ready(function() {
    $('#pending-applications-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route($ADMIN_ROUTE_NAME . '.students.pending') }}',
            type: 'GET'
        },
        columns: [
            { data: 'student_details', name: 'student_details', orderable: false, searchable: true },
            { data: 'application_date', name: 'application_date', orderable: true, searchable: false },
            { data: 'submitted_date', name: 'submitted_date', orderable: true, searchable: false },
            { data: 'application_type', name: 'application_type', orderable: true, searchable: false },
            { data: 'status_label', name: 'status_label', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: 'No pending applications found',
            zeroRecords: 'No matching pending applications found'
        }
    });
});
</script>
@endcomponent