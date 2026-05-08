@component('admin.layouts.main')

@slot('title')
    Manage Banners - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
    $addBtn = 'Add Banner';
@endphp

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Table Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Banners</h4>
        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('banners.create')))
        <a 
            class="btn create-new btn-primary"
            href="{{ route($routeName . '.banners.add') }}"
        >
            <span class="d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-plus icon-sm"></i>
                <span class="d-none d-sm-inline-block">{{ $addBtn }}</span>
            </span>
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">            
          <table class="datatables-basic table" id="banners-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Media Count</th>
                    <th>Video Info</th>
                    <th>Status</th>
                    <th>Sort Order</th>
                    <th>Created</th>
                    <th width="150">Action</th>
                </tr>
            </thead>
          </table>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
    $(function () {
        const table = $('#banners-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($routeName.'.banners.index') }}",
            columns: [
                { data: 'title', name: 'title' },
                { data: 'type_label', name: 'type' },
                { data: 'media_count', name: 'media_count' },
                { data: 'video_info', name: 'video_info' },
                { data: 'status_label', name: 'status' },
                { data: 'sort_order', name: 'sort_order' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[5, 'asc']]
        });

        // Handle delete with SweetAlert
        $(document).on('click', '.btn-delete-banner', function (e) {
            e.preventDefault();
            var url = $(this).data('url');

            Swal.fire({
                title: "Are you sure?",
                text: "This will permanently delete the banner and all its media!",
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
    });
</script>
@endslot

@endcomponent
