@component('admin.layouts.main')
    @slot('title')
        My Profile - {{ config('app.name') }}
    @endslot

    @php
        $routeName = CustomHelper::getAdminRouteName();
    @endphp

    @slot('headerBlock')
   
    @endslot

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="mb-1">Roles List</h4>

      <p class="mb-6">
        A role provided access to predefined menus and features so that depending on <br />
        assigned role an administrator can have access to what user needs.
      </p>
      <!-- Role cards -->
      <div class="row g-6">
        @if($roles->isNotEmpty())
        @foreach($roles as $role)
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">

                @if($role->id != 1)
                    @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('roles.delete')))
                    <a href="javascript:void(0);"
                       class="position-absolute top-0 end-0 m-2 text-danger delete-role"
                       data-id="{{ CustomHelper::encrypt($role->id) }}"
                       title="Delete Role">
                        <i class="icon-base ti tabler-trash icon-sm"></i>
                    </a>
                    @endif
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-normal mb-0 text-body">Total {{ $role->user_count }} User(s)</h6>
                </div>
                <div class="d-flex justify-content-between align-items-end">
                    <div class="role-heading">
                        <h5 class="mb-1">{{ $role->name }}</h5>
                        @if($role->id != 1)
                            @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('roles.edit')))
                            <a
                                href="javascript:void(0);"
                                data-id="{{ CustomHelper::encrypt($role->id) }}"
                                data-mode="edit"
                                data-title="Update Role ({{ $role->name }})"
                                data-bs-toggle="modal"
                                data-bs-target="#manageRoleModal"
                                class="role-edit-modal open-roles-modal"
                                ><span>Edit Role</span></a>
                            @else
                            <a href="javascript:void(0);"><span>No Permission</span></a>
                            @endif
                        @else
                        <a href="javascript:void(0);"><span>Not Available For Update</span></a>
                        @endif
                    </div>
                    <a href="javascript:void(0);" class="copy-role-name" data-role-name="{{ $role->name }}">
                        <i class="icon-base ti tabler-copy icon-md text-heading"></i>
                    </a>
                </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif
        <div class="col-xl-4 col-lg-6 col-md-6">
          <div class="card h-100">
            <div class="row h-100">
              <div class="col-sm-5">
                <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-4">
                  <img
                    src="{{ asset('admin/assets/img/illustrations/auth-verify-email-illustration-light.png') }}"
                    class="img-fluid"
                    alt="Image"
                    width="83" />
                </div>
              </div>
              <div class="col-sm-7">
                <div class="card-body text-sm-end text-center ps-sm-0">
                  @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('roles.create')))
                  <button
                    data-bs-target="#manageRoleModal"
                    data-bs-toggle="modal"
                    data-mode="add"
                    data-title="Add New Role"
                    class="btn btn-sm btn-primary mb-4 text-nowrap open-roles-modal">
                    Add New Role
                  </button>
                  @endif
                  <p class="mb-0">
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--/ Role cards -->

      <!-- Add Role Modal -->
      <div class="modal fade" id="manageRoleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-dialog-centered modal-add-new-role">
          <div class="modal-content">
            <div class="modal-body">
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              <div class="text-center mb-6">
                <h4 class="role-title">Add New Role</h4>
                <p class="text-body-secondary">Set role permissions</p>
              </div>
              <!-- Add role form -->
              <form id="manageRoleForm" class="row g-3" onsubmit="return false">
                <div class="col-12 form-control-validation mb-3">
                  <label class="form-label" for="modalRoleName">Role Name</label>
                  <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    placeholder="Enter a role name"
                    tabindex="-1" />
                </div>
                <div class="col-12">
                  <h5 class="mb-6">Role Permissions</h5>
                  @if($all_permissions->isNotEmpty())
                  
                  <!-- Select All Section -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1">Administrator Access</h6>
                          <small class="text-muted">Allows full access to the system</small>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="selectAll" />
                          <label class="form-check-label fw-medium" for="selectAll">Select All</label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Permission Groups -->
                  @php 
                    $groupedPermissions = $all_permissions->groupBy('group_name');
                    $groupLabels = [
                      'dashboard' => 'Dashboard',
                      'users' => 'User Management',
                      'roles' => 'Role & Permissions',
                      'profile' => 'Profile Management',
                      'settings' => 'System Settings',
                      'activities' => 'Activity Logs',
                      'cms' => 'CMS Pages',
                      'custom_fields' => 'Custom Fields',
                      'menus' => 'Menu Management',
                      'banners' => 'Banner Management',
                      'blog_categories' => 'Blog Categories',
                      'blogs' => 'Blog Management',
                      'news' => 'News Management',
                      'events' => 'Events Management',
                      'enquiries' => 'Enquiries',
                      'volunteer_applications' => 'Volunteer Applications',
                      'faq_categories' => 'FAQ Categories',
                      'faqs' => 'FAQ Management',
                      'newsletter' => 'Newsletter',
                      'students' => 'Student Management',
                      'student_applications' => 'Student Applications',
                      'media' => 'Media Management',
                      'testimonials' => 'Testimonials',
                      'locations' => 'Location Management'
                    ];
                  @endphp

                  @foreach($groupedPermissions as $groupName => $groupPermissions)
                  @if($groupName === 'locations')
                  @continue
                  @endif
                  <div class="card mb-3">
                    <div class="card-header">
                      <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $groupLabels[$groupName] ?? ucfirst(str_replace('_', ' ', $groupName)) }}</h6>
                        <div class="form-check">
                          <input class="form-check-input group-select-all" type="checkbox" 
                                 id="selectAll{{ $groupName }}" />
                          <label class="form-check-label small" for="selectAll{{ $groupName }}">
                            Select All
                          </label>
                        </div>
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        @php
                          $permissionTypes = [
                            'view' => 'View',
                            'create' => 'Create',
                            'edit' => 'Edit',
                            'delete' => 'Delete',
                            'approve' => 'Approve',
                            'reject' => 'Reject',
                            'export' => 'Export',
                            'publish' => 'Publish',
                            'manage' => 'Manage',
                            'activate' => 'Activate',
                            'deactivate' => 'Deactivate',
                            'comment' => 'Comment',
                            'upload' => 'Upload',
                            'organize' => 'Organize',
                            'send' => 'Send',
                            'finance_manage' => 'Finance Management',
                            'documents_manage' => 'Documents Management',
                            'media_manage' => 'Media Management',
                            'details' => 'Details',
                            'change_password' => 'Change Password',
                            'stats' => 'Statistics'
                          ];
                        @endphp
                        
                        @foreach($groupPermissions as $permission)
                        @php
                          $permissionParts = explode('.', $permission->name);
                          $action = $permissionParts[1] ?? $permissionParts[0];
                          $actionLabel = $permissionTypes[$action] ?? ucfirst(str_replace('_', ' ', $action));
                        @endphp
                        <div class="col-md-3 col-sm-4 col-6 mb-2">
                          <div class="form-check">
                            <input class="form-check-input permission-checkbox" type="checkbox" 
                                   name="permissions[{{ $permission->id }}]" 
                                   id="checkPermission{{ $permission->id }}" 
                                   value="{{ $permission->name }}" />
                            <label class="form-check-label" for="checkPermission{{ $permission->id }}">
                              {{ $actionLabel }}
                            </label>
                          </div>
                        </div>
                        @endforeach
                      </div>
                    </div>
                  </div>
                  @endforeach
                  
                  @endif
                </div>
                <div class="col-12 text-center">
                  <button type="submit" class="btn btn-primary me-sm-4 me-1">Submit</button>
                  <button
                    type="reset"
                    class="btn btn-label-secondary"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                    Cancel
                  </button>
                </div>
              </form>
              <!--/ Add role form -->
            </div>
          </div>
        </div>
      </div>
      <!--/ Add Role Modal -->
    </div>
    <!-- / Content -->

    @slot('footerBlock')
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // Copy role name
            $(document).on('click', '.copy-role-name', function () {
                const roleName = $(this).data('role-name');
                navigator.clipboard.writeText(roleName).then(function () {
                    toastr.success("Role name copied to clipboard!");
                }).catch(function () {
                    toastr.error("Failed to copy role name.");
                });
            });

            //Select All Permissions
            $(document).on('change', '#selectAll', function () {
                $('.permission-checkbox').prop('checked', this.checked);
            });

            // Group-level select all functionality
            $(document).on('change', '.group-select-all', function () {
                const groupCard = $(this).closest('.card');
                const isChecked = $(this).is(':checked');
                groupCard.find('.permission-checkbox').prop('checked', isChecked);
            });

            // Update group select all states based on individual checkboxes
            function updateGroupSelectAllStates() {
                $('.card').each(function() {
                    const groupCard = $(this);
                    const groupCheckboxes = groupCard.find('.permission-checkbox');
                    const groupSelectAll = groupCard.find('.group-select-all');
                    
                    if (groupCheckboxes.length > 0) {
                        const checkedCount = groupCheckboxes.filter(':checked').length;
                        const totalCount = groupCheckboxes.length;
                        
                        if (checkedCount === 0) {
                            groupSelectAll.prop('indeterminate', false).prop('checked', false);
                        } else if (checkedCount === totalCount) {
                            groupSelectAll.prop('indeterminate', false).prop('checked', true);
                        } else {
                            groupSelectAll.prop('indeterminate', true).prop('checked', false);
                        }
                    }
                });
            }

            // Update group states when individual checkboxes change
            $(document).on('change', '.permission-checkbox', function() {
                updateGroupSelectAllStates();
            });

            // Open Add/Edit Role Modal
            $(document).on('click', '.open-roles-modal', function () {
                const mode = $(this).data('mode');
                const title = $(this).data('title');
                const roleId = $(this).data('id') || null;
                const $form = $('#manageRoleForm');

                $form[0].reset();
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();
                $('.permission-checkbox').prop('checked', false);
                $('.group-select-all').prop('checked', false).prop('indeterminate', false);
                $('#selectAll').prop('checked', false);
                $('#manageRoleModal .role-title').text(title);

                // Reset form action
                if (mode === 'add') {
                    $form.removeAttr('action');
                }

                if (mode === 'edit' && roleId) {
                    $.ajax({
                        url: '{{ route($routeName . ".roles.getRole") }}',
                        type: 'POST',
                        data: { id: roleId },
                        success: function (res) {
                            if (res.status) {
                                $('#name').val(res.data.name);

                                // Set form action dynamically
                                const actionUrl = '{{ route($routeName . ".roles.edit", ":id") }}'.replace(':id', roleId);
                                $form.attr('action', actionUrl);

                                // Set permissions
                                if (res.data.permissions && typeof res.data.permissions === 'object') {
                                    Object.keys(res.data.permissions).forEach(function (permId) {
                                        $(`input#checkPermission${permId}`).prop('checked', true);
                                    });
                                    
                                    // Update group select all checkboxes
                                    updateGroupSelectAllStates();
                                }
                            } else {
                                toastr.error(res.message || 'Role data not found.');
                            }
                        },
                        error: function () {
                            toastr.error('Failed to fetch role details.');
                        }
                    });
                }
            });

            // Submit Form (Add/Edit)
            $(document).on('submit', '#manageRoleForm', function (e) {
                e.preventDefault();

                const $form = $(this);
                const formData = $form.serialize();
                const actionUrl = $form.attr('action') || '{{ route($routeName . ".roles.add") }}';

                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    success: function (res) {
                        if (res.status) {
                            toastr.success(res.message || 'Role saved successfully!');
                            $('#manageRoleModal').modal('hide');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            toastr.error(res.message || 'Something went wrong.');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const key in errors) {
                                const input = $(`#${key}`);
                                input.addClass('is-invalid');
                                const parent = input.closest('.form-group, .form-control-validation') || input.parent();
                                parent.append(`<div class="invalid-feedback d-block">${errors[key][0]}</div>`);
                            }
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'An error occurred.');
                        }
                    }
                });
            });

            $(document).on('click', '.delete-role', function () {
                const encryptedId = $(this).data('id');
                if (!encryptedId) return;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route($routeName . ".roles.delete", ":id") }}'.replace(':id', encryptedId),
                            type: 'POST',
                            data: { id: encryptedId },
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            success: function (res) {
                                if (res.status) {
                                    toastr.success(res.message || 'Role deleted successfully!');
                                    setTimeout(() => window.location.reload(), 1500);
                                } else {
                                    toastr.error(res.message || 'Deletion failed.');
                                }
                            },
                            error: function () {
                                toastr.error('Something went wrong while deleting the role.');
                            }
                        });
                    }
                });
            });
        });
    </script>

    @endslot
@endcomponent
