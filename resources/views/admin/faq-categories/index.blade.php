@component('admin.layouts.main')
@slot('title') FAQ Categories @endslot
@php $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">FAQ Categories</h4>
        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('faq_categories.create')))
        <a href="{{ route($ADMIN_ROUTE_NAME.'.faq-categories.add') }}" class="btn btn-primary">
            <i class="ti tabler-plus me-1"></i> Add Category
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="faq-categories-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>FAQs Count</th>
                        <th>Sort Order</th>
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
    $(function () {
        var table = $('#faq-categories-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($ADMIN_ROUTE_NAME.'.faq-categories.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'slug', name: 'slug' },
                { data: 'description', name: 'description' },
                { data: 'status_label', name: 'status_label' },
                { data: 'faqs_count', name: 'faqs_count' },
                { data: 'sort_order', name: 'sort_order' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[5, 'asc']],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries per page",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                processing: "Processing...",
                emptyTable: "No data available in table",
                zeroRecords: "No matching records found"
            }
        });
    });

    // Delete FAQ Category
    $(document).on('click', '.btn-delete-faq-category', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var id = $(this).data('id');

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
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        Swal.fire(
                            'Deleted!',
                            'FAQ category has been deleted.',
                            'success'
                        );
                        $('#faq-categories-table').DataTable().ajax.reload();
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error!',
                            'Something went wrong.',
                            'error'
                        );
                    }
                });
            }
        });
    });
</script>
@endslot
@endcomponent




