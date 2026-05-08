@component('admin.layouts.main')

@slot('title')
    Manage Partners - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Partners</h4>
        @if(auth()->user()->can('partners.create'))
        <a href="{{ route($routeName . '.partners.add') }}" class="btn btn-primary">
            <i class="icon-base ti tabler-plus icon-sm me-2"></i> Add Partner
        </a>
        @endif
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">            
          <table class="datatables-basic table" id="partners-table">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Sort Order</th>
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
        const table = $('#partners-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($routeName.'.partners.index') }}",
            columns: [
                { data: 'logo', name: 'logo', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'status', name: 'status' },
                { data: 'sort_order', name: 'sort_order' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[3, 'asc']]
        });

        window.deletePartner = function(url) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
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
                            if (res.success) {
                                toastr.success(res.message);
                                table.ajax.reload();
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function(err) {
                            toastr.error('Something went wrong.');
                        }
                    });
                }
            });
        }
    });
</script>
@endslot

@endcomponent
