@component('admin.layouts.main')

@slot('title')
    Enquiries - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Enquiries</h4>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="enquiry-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>IP Address</th>
                        <th>Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@slot('footerBlock')

<script>
    $(function () {
        $('#enquiry-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: @json(route($routeName . '.enquiries.index')),
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'country', name: 'country' },
                { data: 'ip_address', name: 'ip_address' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[5, 'desc']],
            language: {
                searchPlaceholder: "Search Enquiries...",
                emptyTable: "No enquiries found.",
                processing: "Loading..."
            }
        });
    });

    $(document).on('click', '.btn-delete-enquiry', function (e) {
        e.preventDefault();
        const url = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: 'You won’t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
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
                            toastr.success(res.message || 'Enquiry deleted successfully.');
                            $('#enquiry-table').DataTable().ajax.reload(null, false);
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function () {
                        toastr.error('Something went wrong.');
                    }
                });
            }
        });
    });

    $(document).on('click', '.btn-mark-read', function(e){
        e.preventDefault();
        const url = $(this).data('url');
        const id = $(this).data('id');
        const read = $(this).data('read');
        $.post(url, {id: id, read: read, _token: '{{ csrf_token() }}'}, function(res){
            if(res && res.status){
                toastr.success(read ? 'Marked as read' : 'Marked as unread');
                $('#enquiry-table').DataTable().ajax.reload(null, false);
            } else {
                toastr.error('Action failed');
            }
        }).fail(function(){
            toastr.error('Action failed');
        });
    });
</script>
@endslot

@endcomponent
