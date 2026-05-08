@component('admin.layouts.main')

    @slot('title')
        Testimonials - {{ config('app.name') }}
    @endslot

    @php
        $routeName = CustomHelper::getAdminRouteName();
    $addBtn = 'Add Testimonial';
    @endphp

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Table Title -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Manage Testimonials</h4>
            <a 
                class="btn create-new btn-primary"
                href="{{ route($routeName . '.success-stories.create') }}"
                data-title="Add New Testimonial"
            >
                <span class="d-flex align-items-center gap-2">
                    <i class="icon-base ti tabler-plus icon-sm"></i>
                    <span class="d-none d-sm-inline-block">{{ $addBtn }}</span>
                </span>
            </a>
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
              <table class="datatables-basic table" id="success-stories-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Client Name</th>
                        <th>Designation</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th>Created</th>
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
            $.fn.dataTable.ext.errMode = 'none';
            const table = $('#success-stories-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route($routeName.'.success-stories.index') }}",
                    data: function (d) {
                        d.status = $('#status_filter').val();
                        d.featured = $('#featured_filter').val();
                    }
                },
                columns: [
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'title', name: 'title' },
                    { data: 'brief', name: 'brief' },
                    { data: 'featured_status', name: 'featured' },
                    { data: 'status_label', name: 'status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[5, 'desc']],
                pageLength: 25,
                responsive: true
            });
            
            table.on('error.dt', function(e, settings, techNote, message) {
                toastr.error('Failed to load testimonials. Please try again.');
                if (window.console && console.error) {
                    console.error('DataTables error:', message);
                }
            });

            // Apply filters
            $('#apply_filters').on('click', function() {
                table.ajax.reload();
            });

            // Clear filters
            $('#clear_filters').on('click', function() {
                $('#status_filter').val('');
                $('#featured_filter').val('');
                table.ajax.reload();
            });

            // Handle delete with SweetAlert
            $(document).on('click', '.btn-delete-story', function (e) {
                e.preventDefault();
                var url = $(this).data('url');

                Swal.fire({
                    title: "Are you sure?",
                    text: "This will permanently delete the testimonial!",
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
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (res) {
                                if (res.success) {
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
