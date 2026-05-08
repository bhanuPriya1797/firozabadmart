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
        <div>
            <h4 class="mb-0" id="page-title">{{ $page_title ?? 'Applications' }}</h4>
            @if(isset($currentPaymentStatus) && $currentPaymentStatus)
                <div class="mt-2">
                    <span class="badge bg-label-info">Filter: {{ ucfirst($currentPaymentStatus) }} Applications</span>
                    <a href="{{ route($routeName . '.students.financed') }}" class="btn btn-sm btn-outline-secondary ms-2">Clear Filter</a>
                </div>
            @endif
        </div>
        <div>
            <div class="btn-group" role="group">
                <a href="{{ route($routeName . '.students.financed', ['payment_status' => 'pending']) }}" 
                   class="btn btn-sm {{ ($currentPaymentStatus ?? 'pending') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">Pending</a>
                <a href="{{ route($routeName . '.students.financed', ['payment_status' => 'completed']) }}" 
                   class="btn btn-sm {{ ($currentPaymentStatus ?? '') === 'completed' ? 'btn-success' : 'btn-outline-success' }}">Completed</a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="table datatables-basic" id="application-table">
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Appeal No.</th>
                        <th>Year</th>
                        <th>Student</th>
                        <th>Family Lineage</th>
                        <th>Approval Date</th>
                        <th>Amount Required</th>
                        <th>Support Type</th>
                        <th>Payment Status</th>
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
        ajax: {
            url: window.location.href,
            data: function(d) {
                d.payment_status = '{{ $currentPaymentStatus ?? "pending" }}';
            }
        },
        columns: [
            { data: 'case_id', name: 'case_id' },
            { data: 'appeal_number', name: 'appeal_number', orderable: false, searchable: false },
            { data: 'year', name: 'year' },
            { data: 'student_details', name: 'student_details', orderable: false, searchable: false },
            { data: 'family_lineage', name: 'family_lineage', orderable: false, searchable: false },
            { data: 'approved_date', name: 'approved_date', searchable: false },
            { data: 'amount_needed', name: 'amount_needed' , searchable: false },
            { data: 'support_required', name: 'support_required', searchable: false },
            { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false },
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
