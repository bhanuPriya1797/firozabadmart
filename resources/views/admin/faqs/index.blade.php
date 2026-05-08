@component('admin.layouts.main')
@slot('title') FAQs @endslot
@php $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">FAQs</h4>
        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('faqs.create')))
        <a href="{{ route($ADMIN_ROUTE_NAME.'.faqs.add') }}" class="btn btn-primary">
            <i class="ti tabler-plus me-1"></i> Add FAQ
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-3">
                    <select id="category-filter" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="status-filter" class="form-select">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" id="filter-btn" class="btn btn-primary">
                        <i class="ti tabler-filter me-1"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <button type="button" id="reset-btn" class="btn btn-secondary">
                        <i class="ti tabler-refresh me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="faqs-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Category</th>
                        <th>Status</th>
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
        var table = $('#faqs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route($ADMIN_ROUTE_NAME.'.faqs.index') }}",
                data: function (d) {
                    d.category_id = $('#category-filter').val();
                    d.status = $('#status-filter').val();
                }
            },
            columns: [
                { data: 'question_short', name: 'question' },
                { data: 'answer_short', name: 'answer' },
                { data: 'category_name', name: 'category_name' },
                { data: 'status_label', name: 'status_label' },
                { data: 'sort_order', name: 'sort_order' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[4, 'asc']],
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

        // Filter button
        $('#filter-btn').on('click', function() {
            table.ajax.reload();
        });

        // Reset button
        $('#reset-btn').on('click', function() {
            $('#category-filter').val('');
            $('#status-filter').val('');
            table.ajax.reload();
        });

        // Filter on Enter key
        $('#category-filter, #status-filter').on('keypress', function(e) {
            if (e.which == 13) {
                table.ajax.reload();
            }
        });
    });

    // Delete FAQ
    $(document).on('click', '.btn-delete-faq', function (e) {
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
                            'FAQ has been deleted.',
                            'success'
                        );
                        $('#faqs-table').DataTable().ajax.reload();
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









