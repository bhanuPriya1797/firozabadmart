@component('admin.layouts.main')

@slot('title')
    Students - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

@slot('headerBlock')
<!-- You can inject filters UI here if needed globally  -->
@endslot

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0" id="page-title">{{ $page_title ?? 'Students' }}</h4>
        <div class="d-flex gap-2">

            @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.create')))
            <a 
                class="btn create-new btn-primary"
                href="{{ route($routeName.'.students.create') }}"
                data-title="Add New Student"
            >
                <span class="d-flex align-items-center gap-2">
                    <i class="icon-base ti tabler-plus icon-sm"></i>
                    <span class="d-none d-sm-inline-block">Add New Student</span>
                </span>
            </a>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="card">

        {{-- Card Header: Filter Form --}}
        <div class="card-header border-bottom pb-2">
            <form class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" id="filter_start_date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">End Date</label>
                    <input type="date" id="filter_end_date" class="form-control">
                </div>
                <!-- <div class="col-md-2">
                    <select class="form-control" id="filter_lineage">
                        <option value="">All Lineage</option>
                        <option value="SYED">SYED</option>
                        <option value="NON-SYED">NON-SYED</option>
                    </select>
                </div> -->
                <div class="col-md-2">
                    <select class="form-control" id="filter_status">
                        <option value="">All Status</option>
                        <option value="1" {{ isset($defaultFilters['status']) && $defaultFilters['status'] === 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ isset($defaultFilters['status']) && $defaultFilters['status'] === 0 ? 'selected' : '' }}>Deactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="filter_application_status">
                        <option value="">Application Status</option>
                        <option value="submitted" {{ $defaultFilters['application_status'] == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="not_submitted" {{ $defaultFilters['application_status'] == 'not_submitted' ? 'selected' : '' }}>Not submitted Yet</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="filter_eligibility_status">
                        <option value="">All Eligibility</option>
                        <option value="1" {{ isset($defaultFilters['eligibility_status']) && $defaultFilters['eligibility_status'] == 1 ? 'selected' : '' }}>Eligible</option>
                        <option value="2" {{ isset($defaultFilters['eligibility_status']) && $defaultFilters['eligibility_status'] == 2 ? 'selected' : '' }}>Approved</option>
                        <option value="0" {{ isset($defaultFilters['eligibility_status']) && $defaultFilters['eligibility_status'] == 0 ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="reloadDataGrid()">Filter</button>
                    <button type="button" class="btn btn-outline-info" onclick="resetDataGrid()">Reset</button>
                </div>
            </form>
        </div>

        <div class="card-datatable table-responsive pt-0">
            <table class="table datatables-basic" id="student-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Details</th>
                        <th>Gender</th>
                        <th>Status</th>
                        <th>Application Status</th>
                        <th>Eligibility</th>
                        <th>Created</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Student Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="documentModalBody">
                    <p class="text-muted">Loading documents...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
let studentsTable;

$(function () {
    studentsTable = $('#student-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: @json(route($routeName . '.students.index')),
            data: function (d) {
                d.status = $('#filter_status').val();
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
                d.family_lineage = $('#filter_lineage').val();
                d.application_status = $('#filter_application_status').val();
                d.eligibility_status = $('#filter_eligibility_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'details', name: 'details', orderable: true, searchable: false },
            { data: 'gender', name: 'gender' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'application_status', name: 'application_status', orderable: false, searchable: false },
            { data: 'eligibility_status', name: 'eligibility_status', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            searchPlaceholder: 'Search Student...',
            emptyTable: 'No students found.',
            processing: 'Loading...',
        }
    });

    $(document).on('click', '.btn-delete-student', function (e) {
        e.preventDefault();
        const url = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        toastr.success(response.message || 'Student deleted.');
                        table.ajax.reload();
                    },
                    error: function (xhr) {
                        toastr.error('Something went wrong while deleting.');
                    }
                });
            }
        });
    });
});

$(document).on('click', '.btn-show-documents', function () {
    const studentId = $(this).data('id');
    $('#documentModal').modal('show');
    $('#documentModalBody').html('<p class="text-muted">Loading documents...</p>');

    $.ajax({
        url: '{{ route($routeName.".students.documents", ":id") }}'.replace(':id', studentId),
        type: 'GET',
        success: function (res) {
            if (res.success && res.data.length) {
                let html = '<div class="table-responsive"><table class="table table-bordered">';
                html += '<thead><tr><th>Course</th><th>College</th><th>Year</th><th>%</th><th>Document Name</th><th>File</th></tr></thead><tbody>';
                res.data.forEach(doc => {
                    html += `<tr>
                        <td>${doc.course_name || '-'}</td>
                        <td>${doc.college || '-'}</td>
                        <td>${doc.academic_year || '-'}</td>
                        <td>${doc.percentage || '-'}</td>
                        <td>${doc.document_name || '-'}</td>
                        <td><a href="{{ asset('/') }}storage/${doc.document_file}" target="_blank" class="btn btn-sm btn-primary">View</a></td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                $('#documentModalBody').html(html);
            } else {
                $('#documentModalBody').html('<div class="alert alert-warning">No documents found for this student.</div>');
            }
        },
        error: function () {
            $('#documentModalBody').html('<div class="alert alert-danger">Failed to load documents.</div>');
        }
    });
});


function reloadDataGrid() {
    updatePageTitle();
    studentsTable.ajax.reload();
}

function resetDataGrid() {
    $('#filter_status').val('');
    $('#filter_lineage').val('');
    $('#filter_start_date').val('');
    $('#filter_end_date').val('');
    $('#filter_application_status').val('');
    $('#filter_eligibility_status').val('');
    updatePageTitle();
    studentsTable.ajax.reload();
}

function updatePageTitle() {
    let status = $('#filter_status').val();
    let appStatus = $('#filter_application_status').val();
    let eligibility = $('#filter_eligibility_status').val();

    let titleParts = [];

    // Status
    if (status === '1') titleParts.push('Active');
    else if (status === '0') titleParts.push('Deactive');

    // Application
    if (appStatus === 'submitted') titleParts.push('Submitted');
    else if (appStatus === 'not_submitted') titleParts.push('Not submitted Yet');

    // Eligibility
    if (eligibility === '1') titleParts.push('Eligible');
    else if (eligibility === '2') titleParts.push('Approved');
    else if (eligibility === '0') titleParts.push('Pending');

    let titleText = titleParts.length > 0 ? titleParts.join(' ') + ' Students' : 'Students';
    $('#pageTitle').text(titleText);
}

</script>
@endslot



@endcomponent
