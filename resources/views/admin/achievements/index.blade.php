@component('admin.layouts.main')

@slot('title')
Achievements - {{ config('app.name') }}
@endslot

@php
    $routeName = $ADMIN_ROUTE_NAME ?? \App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Achievements</h4>
        @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('achievements.create'))
        <a href="{{ route($routeName.'.achievements.create') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus"></i> Add Achievement
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-header border-bottom pb-2">
            <form class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" id="filter_search" class="form-control" placeholder="Search by winner, event or location">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Medal</label>
                    <select id="filter_medal" class="form-select">
                        <option value="">All</option>
                        <option value="gold">Gold</option>
                        <option value="silver">Silver</option>
                        <option value="bronze">Bronze</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <input type="text" id="filter_category" class="form-control" placeholder="Type a category">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select id="filter_status" class="form-select">
                        <option value="">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-primary" id="btn-filter">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="achievements-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Winner</th>
                            <th>Medal</th>
                            <th>Event</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
let achievementsTable;
$(function(){
    const indexUrl = "{{ route($routeName.'.achievements.index') }}";

    achievementsTable = $('#achievements-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        dom: 'lrtip',
        ajax: {
            url: indexUrl,
            data: function(d){
                d.search = $('#filter_search').val();
                d.medal = $('#filter_medal').val();
                d.category = $('#filter_category').val();
                d.status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'winner_name', name: 'winner_name' },
            { data: 'medal_badge', name: 'medal', orderable: false, searchable: false },
            { data: 'event_name', name: 'event_name' },
            { data: 'location', name: 'location' },
            { data: 'category', name: 'category' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'sort_order', name: 'sort_order' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[7, 'asc']]
    });

    $('#btn-filter, #filter_medal, #filter_category, #filter_status').on('click change', function(){
        achievementsTable.ajax.reload();
    });

    $(document).on('click', '.btn-delete', function(e){
        e.preventDefault();
        const url = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: 'The item will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(url, { _method: 'DELETE', _token: '{{ csrf_token() }}' })
                    .done(function(resp){
                        toastr.success(resp.message || 'Deleted successfully.');
                        achievementsTable.ajax.reload();
                    })
                    .fail(function(){
                        toastr.error('Failed to delete.');
                    });
            }
        });
    });
});
</script>
@endslot

@endcomponent
