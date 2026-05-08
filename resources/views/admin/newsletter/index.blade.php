@component('admin.layouts.main')

@slot('title')
    Manage Newsletter Subscribers - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Table Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Manage Newsletter Subscribers</h4>
        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('newsletter.view')))
        <button type="button" onclick="exportList()" class="btn btn-success">
            <i class="fas fa-file-excel me-1"></i> Export XLS
        </button>
        @endif
    </div>

    <!-- Card Table -->
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="table datatables-basic" id="newsletter-table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th class="text-center" width="100">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Export Form -->
<form name="exportForm" method="GET" action="{{ route($routeName.'.newsletter.export') }}">
    @csrf
</form>

@slot('footerBlock')
<script>
    $(function () {
        // Initialize DataTable
        const table = $('#newsletter-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($routeName.'.newsletter.index') }}",
            columns: [
                { data: 'email', name: 'email' },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    className: 'text-center' 
                }
            ]
        });

        // Export XLS
        window.exportList = function () {
            document.exportForm.submit();
        };

        // Delete Subscriber
        $(document).on('click', '.btn-delete-newsletter', function (e) {
            e.preventDefault();
            const url = $(this).data('url');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This will permanently delete this subscriber!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
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
                                toastr.success(res.msg);
                                table.ajax.reload();
                            } else {
                                toastr.error(res.msg || 'Deletion failed');
                            }
                        },
                        error: function () {
                            toastr.error('Something went wrong.');
                        }
                    });
                }
            });
        });

        // Flash messages
        @if(session('success'))
            toastr.success("{{ session('success') }}");
        @elseif(session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    });
</script>
@endslot

@endcomponent
