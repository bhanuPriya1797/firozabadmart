@component('admin.layouts.main')
@slot('title')
    Blog Categories - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
    $BackUrl = CustomHelper::BackUrl();
    $old_name = request()->name ?? '';
    $old_status = request()->status ?? '';
@endphp

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Table Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Blog Category List</h4>
        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('blog_categories.create')))
        <a 
            class="btn create-new btn-primary open-user-modal"
            href="{{ route($routeName.'.blogs_categories.add') }}"
            data-title="Add Blog Category"
        >
            <span class="d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-plus icon-sm"></i>
                <span class="d-none d-sm-inline-block">Add Blog Category</span>
            </span>
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">            
          <table class="datatables-basic table" id="blogCategoryTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>No. of Blogs</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
          </table>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
    $(document).ready(function () {
        var table = $('#blogCategoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route($routeName.'.blogs_categories.index') }}",
                data: function (d) {
                    d.name = $('#filterName').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'name', name: 'name' },
                { data: 'blogs_count', name: 'blogs_count', orderable: false, searchable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            table.draw();
        });

        // SweetAlert Delete Handler
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
    });
</script>
@endslot
@endcomponent
