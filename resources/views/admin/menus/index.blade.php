@component('admin.layouts.main')

@slot('title')
    Manage Menus - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manage Menus</h5>
        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('menus.create')))
        <a href="{{ route($routeName . '.menus.add') }}" class="btn btn-primary">
            <i class="ti tabler-plus me-1"></i> New Menu
        </a>
        @endif
    </div>

    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="menus-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Handled by DataTable --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

@slot('footerBlock')
    <script>
        $(function () {
            $('#menus-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route($routeName . '.menus.index') }}",
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'slug', name: 'slug' },
                    { data: 'position', name: 'position' },
                    { data: 'status', name: 'status', orderable: true, searchable: false },
                    { data: 'items', name: 'items', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[4, 'desc']],
            });

            // Optional: handle delete via AJAX or confirmation
            $(document).on('click', '.btn-delete-menu', function () {
                let url = $(this).data('url');
                if (confirm('Do you really want to delete this menu?')) {
                    $.post(url, {
                        _token: '{{ csrf_token() }}'
                    }).done(function (res) {
                        if (res.success) {
                            $('#menus-table').DataTable().ajax.reload(null, false);
                            toastr.success('Menu deleted successfully');
                        } else {
                            toastr.error('Failed to delete menu');
                        }
                    });
                }
            });
        });
    </script>
@endslot

@endcomponent
