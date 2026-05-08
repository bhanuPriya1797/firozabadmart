@component('admin.layouts.main')

@slot('title')
    Students - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

@slot('headerBlock')
<!-- You can inject filters UI here if needed globally -->
@endslot

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0" id="page-title">{{ $page_title ?? 'Applications' }}</h4>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="table datatables-basic" id="application-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Gender</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
$(function () {
    $('#application-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.location.href,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'student_details', name: 'student_details', orderable: false, searchable: false },
            { data: 'gender', name: 'gender', orderable: false, searchable: false },
            { data: 'application_date', name: 'application_date' },
            { data: 'status_label', name: 'status_label', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        language: {
            searchPlaceholder: 'Search...',
            emptyTable: 'No applications found.',
            processing: 'Loading...'
        }
    });
});
</script>
@endslot

@endcomponent
