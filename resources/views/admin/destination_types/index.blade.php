@component('admin.layouts.main')

@slot('title')
    Destination Types - {{ config('app.name') }}
@endslot

@php
    $routeName = App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Destination Types</h4>
        @hasPermission('destinations.edit')
        <a href="{{ route($routeName . '.destinations.types.create') }}" class="btn btn-primary">
            <i class="ti tabler-plus me-1"></i> Add Type
        </a>
        @endHasPermission
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="table table-bordered table-hover" id="destination-types-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
$(function () {
    var table = $('#destination-types-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route($routeName . ".destinations.types.index") }}',
        columns: [
            { data: 'name', name: 'name' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true
    });

    $(document).on('click', '.btn-delete-destination-type', function (e) {
        e.preventDefault();
        var url = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res.status || res.success) {
                            toastr.success(res.message || 'Destination Type deleted');
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error(res.message || 'Failed to delete type');
                        }
                    },
                    error: function () {
                        toastr.error('Something went wrong.');
                    }
                });
            }
        });
    });
});
</script>
@endSlot

@endcomponent
