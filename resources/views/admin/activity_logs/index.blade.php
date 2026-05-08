@component('admin.layouts.main')

    @slot('title')
        Activity Log - {{ config('app.name') }}
    @endslot

    <?php
    $back_url=CustomHelper::BackUrl();
    $routeName = CustomHelper::getAdminRouteName();
    $id = (request()->has('id'))?request()->id:'';
    $old_action_type = (request()->has('action_type'))?request()->action_type:'';
    ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      <!-- DataTable with Buttons -->

      <!-- Table Title -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Activity Logs</h4>
      </div>
      
      <div class="card">
        <div class="card-datatable table-responsive pt-0">
          <table class="datatables-basic table" id="activity-table">
            <thead>
                <tr>
                    <th>Action Table</th>
                    <th>Action Type</th>
                    <th>Ip Address</th>
                    <th>Action Date</th>
                    <th>Action Performed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
    <!-- / Content -->

    @slot('footerBlock')
    <script type="text/javaScript">
    $(function () {
        // Initialize DataTable
        $('#activity-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            ordering: true,
            ajax: @json(route($routeName . '.activities.index')),
            columns: [
                { data: 'action_table', name: 'action_table', title: 'Action Table' },
                { data: 'action_type', name: 'action_type', title: 'Action Type' },
                { data: 'ip_address', name: 'ip_address', title: 'IP Address' , orderable: false, searchable: false, className: 'text-center' },
                { data: 'action_date', name: 'action_date', title: 'Action Date' },
                { data: 'action_by', name: 'action_by', title: 'Action Performed By' },
                { data: 'action', name: 'action', title: 'Actions', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search activity logs...",
                processing: "Loading...",
                emptyTable: "No activity logs found.",
            }
        });
    });
    </script>
    @endslot
@endcomponent