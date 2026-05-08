@component('admin.layouts.main')

@slot('title')
Archers - {{ config('app.name') }}
@endslot

@php
    $routeName = $ADMIN_ROUTE_NAME ?? \App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0" id="page-title">Archers</h4>
        <div class="d-flex gap-2 align-items-center">
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Bulk Actions
                </button>
                <ul class="dropdown-menu">
                    @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('archers.edit'))
                    <li><a class="dropdown-item archer-bulk-action" href="#" data-action="activate">Activate Selected</a></li>
                    <li><a class="dropdown-item archer-bulk-action" href="#" data-action="deactivate">Deactivate Selected</a></li>
                    @endif
                    @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('archers.delete'))
                    <li><a class="dropdown-item archer-bulk-action" href="#" data-action="delete">Delete Selected</a></li>
                    @endif
                </ul>
            </div>
            @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('archers.export'))
            <button type="button" class="btn btn-outline-primary" id="btn-archers-export">
                <span class="d-flex align-items-center gap-2">
                    <i class="icon-base ti tabler-download icon-sm"></i>
                    <span class="d-none d-sm-inline-block">Export</span>
                </span>
            </button>
            @endif
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="archerStatusTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-app-status="" type="button" role="tab">All</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-app-status="affiliated" type="button" role="tab">Affiliated</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-app-status="rejected" type="button" role="tab">Rejected</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-app-status="pending" type="button" role="tab">Pending</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-app-status="re_evaluate" type="button" role="tab">Re-evaluate</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-app-status="renewal_pending" type="button" role="tab">Renewal Pending</button>
        </li>
    </ul>

    <div class="card">
        <div class="card-header border-bottom pb-2">
            <form class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" id="filter_search" class="form-control" placeholder="Search by name, email or phone">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filter_status">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Gender</label>
                    <select class="form-select" id="filter_gender">
                        <option value="">All</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-primary" id="btn-filter">Filter</button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-reset">Reset</button>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="card-datatable table-responsive pt-0">
                <table class="table datatables-basic" id="archers-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Application Status</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="affiliateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Affiliate Archer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="affiliate_archer_id" />
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" id="affiliate_category" class="form-control" placeholder="Recurve / Compound / Indian Round / Para" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Member ID</label>
                    <input type="text" id="affiliate_member_id" class="form-control" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Member Association</label>
                    <input type="text" id="affiliate_member_association" class="form-control" placeholder="e.g., Odisha Archery Association" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="affiliateSaveBtn">Affiliate</button>
            </div>
        </div>
    </div>
    </div>

@slot('footerBlock')
<script>
let archersTable;

$(function () {
    const indexUrl = @json(route($routeName . '.archers.index'));
    const toggleStatusUrl = @json(route($routeName . '.archers.toggle-status'));
    const bulkStatusUrl = @json(route($routeName . '.archers.bulk-status'));
    const bulkDeleteUrl = @json(route($routeName . '.archers.bulk-delete'));
    const exportUrl = @json(route($routeName . '.archers.export'));
    const updateAppStatusUrl = @json(route($routeName . '.archers.update-app-status'));

    archersTable = $('#archers-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        dom: 'lrtip',
        ajax: {
            url: indexUrl,
            type: 'GET',
            dataType: 'json',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            data: function (d) {
                d.search = $('#filter_search').val();
                d.status = $('#filter_status').val();
                d.gender = $('#filter_gender').val();
                d.app_status = window.currentAppStatus || '';
            },
            error: function (xhr) {
                try {
                    console.error('Archers AJAX error:', xhr.status, xhr.statusText);
                    console.error((xhr.responseText || '').substring(0, 500));
                } catch (e) {}
                toastr.error('Failed to load archers data.');
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'contact', name: 'contact', orderable: false, searchable: false },
            { data: 'app_status', name: 'application_status', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        language: {
            searchPlaceholder: 'Search archers',
            emptyTable: 'No archers found.',
            processing: 'Loading...'
        }
    });

    $('#btn-filter').on('click', function () {
        archersTable.ajax.reload();
    });

    $('#btn-reset').on('click', function () {
        $('#filter_search').val('');
        $('#filter_status').val('');
        $('#filter_gender').val('');
        window.currentAppStatus = '';
        $('#archerStatusTabs .nav-link').removeClass('active');
        $('#archerStatusTabs [data-app-status=""]').addClass('active');
        archersTable.ajax.reload();
    });

    window.currentAppStatus = '';
    $('#archerStatusTabs .nav-link').on('click', function(){
        $('#archerStatusTabs .nav-link').removeClass('active');
        $(this).addClass('active');
        window.currentAppStatus = $(this).data('app-status');
        archersTable.ajax.reload();
    });

    $('#select-all').on('change', function () {
        const checked = $(this).is(':checked');
        $('#archers-table').find('.archer-checkbox').prop('checked', checked);
    });

    function getSelectedIds() {
        const ids = [];
        $('#archers-table').find('.archer-checkbox:checked').each(function () {
            ids.push($(this).val());
        });
        return ids;
    }

    function bulkUpdateStatus(status) {
        const ids = getSelectedIds();
        if (!ids.length) {
            toastr.error('Please select at least one archer.');
            return;
        }
        $.ajax({
            url: bulkStatusUrl,
            type: 'POST',
            data: {
                ids: ids,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                toastr.success(response.message || 'Status updated successfully.');
                archersTable.ajax.reload();
            },
            error: function () {
                toastr.error('Failed to update status.');
            }
        });
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (!ids.length) {
            toastr.error('Please select at least one archer.');
            return;
        }
        Swal.fire({
            title: 'Are you sure?',
            text: 'Selected archers will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: bulkDeleteUrl,
                    type: 'POST',
                    data: {
                        ids: ids,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        toastr.success(response.message || 'Archers deleted successfully.');
                        archersTable.ajax.reload();
                    },
                    error: function () {
                        toastr.error('Failed to delete archers.');
                    }
                });
            }
        });
    }

    // Bulk actions rendered in header; no need to attach inside DataTables

    $(document).on('click', '.archer-bulk-action', function (e) {
        e.preventDefault();
        const action = $(this).data('action');
        if (action === 'activate') {
            bulkUpdateStatus(1);
        } else if (action === 'deactivate') {
            bulkUpdateStatus(0);
        } else if (action === 'delete') {
            bulkDelete();
        }
    });

    $(document).on('click', '.btn-delete-archer', function (e) {
        e.preventDefault();
        const url = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This archer will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        toastr.success(response.message || 'Archer deleted successfully.');
                        archersTable.ajax.reload();
                    },
                    error: function () {
                        toastr.error('Failed to delete archer.');
                    }
                });
            }
        });
    });

    $(document).on('change', '.archer-status-toggle', function () {
        const id = $(this).data('id');
        const status = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: toggleStatusUrl,
            type: 'POST',
            data: {
                id: id,
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                toastr.success(response.message || 'Status updated successfully.');
                archersTable.ajax.reload(null, false);
            },
            error: function () {
                toastr.error('Failed to update status.');
            }
        });
    });

    $('#btn-archers-export').on('click', function () {
        const params = {
            search: $('#filter_search').val(),
            status: $('#filter_status').val(),
            gender: $('#filter_gender').val(),
            app_status: window.currentAppStatus || ''
        };
        const query = $.param(params);
        window.location = exportUrl + (query ? '?' + query : '');
    });

    $(document).on('click', '.btn-update-app-status', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        if (status === 'approved') {
            $('#affiliate_archer_id').val(id);
            $('#affiliate_category').val($(this).data('category') || '');
            $('#affiliate_member_id').val($(this).data('member_id') || '');
            $('#affiliate_member_association').val($(this).data('member_association') || '');
            const modal = new bootstrap.Modal(document.getElementById('affiliateModal'));
            modal.show();
            $('#affiliateModal').data('modalInstance', modal);
        } else {
            $.ajax({
                url: updateAppStatusUrl,
                type: 'POST',
                data: {
                    archer_id: id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    toastr.success(response.message || 'Application status updated.');
                    archersTable.ajax.reload(null, false);
                },
                error: function () {
                    toastr.error('Failed to update application status.');
                }
            });
        }
    });

    $('#affiliateSaveBtn').on('click', function () {
        const id = $('#affiliate_archer_id').val();
        const category = $('#affiliate_category').val();
        const member_id = $('#affiliate_member_id').val();
        const member_association = $('#affiliate_member_association').val();
        if (!category || !member_id || !member_association) {
            toastr.error('Please fill Category, Member ID and Member Association.');
            return;
        }
        $.ajax({
            url: updateAppStatusUrl,
            type: 'POST',
            data: {
                archer_id: id,
                status: 'approved',
                category: category,
                member_id: member_id,
                member_association: member_association,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                toastr.success(response.message || 'Archer affiliated successfully.');
                const modal = $('#affiliateModal').data('modalInstance');
                if (modal) {
                    modal.hide();
                }
                archersTable.ajax.reload(null, false);
            },
            error: function () {
                toastr.error('Failed to affiliate archer.');
            }
        });
    });
});
</script>
@endslot

@endcomponent
