@component('admin.layouts.main')

@slot('title')
    Manage Blogs - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
    $addBtn = 'Add Blog';
@endphp

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Table Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Blogs</h4>
        @hasPermission('blogs.create')
        <a 
            class="btn create-new btn-primary open-user-modal"
            href="{{ route($routeName . '.blogs.add') }}"
            data-title="Add New Page"
        >
            <span class="d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-plus icon-sm"></i>
                <span class="d-none d-sm-inline-block">{{ $addBtn }}</span>
            </span>
        </a>
        @endHasPermission
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label for="status_filter" class="form-label">Status</label>
                    <select class="form-select" id="status_filter">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="featured_filter" class="form-label">Featured</label>
                    <select class="form-select" id="featured_filter">
                        <option value="">All</option>
                        <option value="1">Featured</option>
                        <option value="0">Not Featured</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category_filter" class="form-label">Category</label>
                    <select class="form-select" id="category_filter">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\BlogCategory::where('status', 1)->get() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-primary" id="apply_filters">
                            <i class="ti tabler-filter me-1"></i> Apply Filters
                        </button>
                        <button type="button" class="btn btn-secondary" id="clear_filters">
                            <i class="ti tabler-refresh me-1"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">            
          <table class="datatables-basic table" id="blogs-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Posted By</th>
                    <th>Category</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th width="100">Action</th>
                </tr>
            </thead>
          </table>
        </div>
    </div>
</div>
@slot('footerBlock')
<script>
    $(function () {
        const table = $('#blogs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route($routeName.'.blogs.index') }}",
                data: function (d) {
                    d.status = $('#status_filter').val();
                    d.featured = $('#featured_filter').val();
                    d.category_id = $('#category_filter').val();
                }
            },
            columns: [
                { data: 'title', name: 'title' },
                { data: 'post_by', name: 'post_by' },
                { data: 'category_name', name: 'category_name' },
                { data: 'featured_status', name: 'featured' },
                { data: 'status_label', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.btn-delete-blog', function (e) {
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: "Are you sure?",
                text: "This will permanently delete the category!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: 'btn btn-danger mx-1',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            if (res.status) {
                                toastr.success(res.message);
                                table.ajax.reload();
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function () {
                            toastr.error('Something went wrong!');
                        }
                    });
                }
            });
        });

        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @elseif(session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        // Filter handlers
        $('#apply_filters').on('click', function() {
            table.ajax.reload();
        });

        $('#clear_filters').on('click', function() {
            $('#status_filter').val('');
            $('#featured_filter').val('');
            $('#category_filter').val('');
            table.ajax.reload();
        });

        // Auto-apply filters on change
        $('#status_filter, #featured_filter, #category_filter').on('change', function() {
            table.ajax.reload();
        });
    });
</script>
@endslot

@endcomponent