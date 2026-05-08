@component('admin.layouts.main')

@slot('title')
    CMS Pages - {{ config('app.name') }}
@endslot

@slot('headerBlock')

@endslot
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $page_title }}</h5>
                    @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('cms.create')))
                    <a href="{{ route(CustomHelper::getAdminRouteName() . '.cms.create') }}" class="btn btn-primary">
                        <i class="ti tabler-plus me-1"></i> Add New Page
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="cms-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Template</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
$(document).ready(function() {
    var routeName = '{{ CustomHelper::getAdminRouteName() }}';
    
    // Initialize DataTable
    var table = $('#cms-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route(CustomHelper::getAdminRouteName() . ".cms.index") }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'title', name: 'title'},
            {data: 'slug', name: 'slug'},
            {data: 'template_badge', name: 'template', orderable: false, searchable: false},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'featured_badge', name: 'featured', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });

    // Handle delete button clicks
    $(document).on('click', '.btn-delete-cms', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
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
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            table.ajax.reload(null, false);
                        } else {
                            toastr.error(response.message || 'Error deleting CMS page');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error deleting CMS page. Please try again.');
                    }
                });
            }
        });
    });
});
</script>
@endSlot

@endcomponent
