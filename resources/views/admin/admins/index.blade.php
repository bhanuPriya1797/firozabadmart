@component('admin.layouts.main')

@slot('title')
    Admin - Manage Users - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

@slot('headerBlock')
<link href="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.css" rel="stylesheet"/>
<style>
  .modal-xl {
    max-width: 900px;
  }
  #tempCropImage {
    max-width: 100%;
    height: auto;
  }
</style>
@endslot
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Table Title -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Users
                @if(isset($currentRole) && !empty($currentRole))
                    <span class="badge bg-primary ms-2">{{ $currentRole }}</span>
                @endif
            </h4>
            @if(isset($currentRole) && !empty($currentRole))
                <small class="text-muted">
                    Showing users with role: {{ $currentRole }}
                    <a href="{{ route($routeName . '.users.index') }}" class="text-decoration-none ms-2">
                        <i class="ti tabler-x"></i> Clear filter
                    </a>
                </small>
            @endif
        </div>
        @hasPermission('users.create')
        <button 
            class="btn create-new btn-primary open-user-modal"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#add-new-record"
            data-mode="add"
            data-title="Add New User"
        >
            <span class="d-flex align-items-center gap-2">
                <i class="icon-base ti tabler-plus icon-sm"></i>
                <span class="d-none d-sm-inline-block">Add New User</span>
            </span>
        </button>
        @endHasPermission        
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">            
          <table class="datatables-basic table" id="admin-table">
            <thead>
                <tr>
                    <th>Details</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login At</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
          </table>
        </div>
    </div>
    <!-- Modal to add new record -->
    <div class="offcanvas offcanvas-end" id="add-new-record">
        <div class="offcanvas-header border-bottom">
          <h5 class="offcanvas-title" id="exampleModalLabel">New User</h5>
          <button
            type="button"
            class="btn-close text-reset"
            data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
        </div>
        <div class="offcanvas-body flex-grow-1">
          <form class="add-new-record pt-0 row g-2" id="form-add-new-record" autocomplete="off" onsubmit="return false">

            <!-- Name -->
            <div class="col-sm-12 form-control-validation">
                <label class="form-label" for="name">Full Name</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-user"></i></span>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Full Name" autocomplete="off" />
                </div>
            </div>

            <!-- Email -->
            <div class="col-sm-12 form-control-validation">
                <label class="form-label" for="email">Email</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-mail"></i></span>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email" autocomplete="off" />
                </div>
            </div>

            <!-- User Image Upload -->
            <div class="col-sm-12 form-control-validation" style="text-align: center;">
                <label class="form-label" for="image">Image</label>
                <input type="file" id="userImage" name="image" class="form-control" accept="image/*">

                <div class="mt-2 position-relative">
                    <img id="previewImage" src="" class="img-thumbnail d-none" style="max-width: 150px; max-height: 150px;" />  
                </div>
                <button type="button" id="removeImageBtn" class="btn btn-sm btn-danger d-none mt-1" style="top: 5px; right: 5px;">Remove</button>
                <input type="hidden" name="cropped_image" id="croppedImageInput">
                <input type="hidden" name="remove_image" id="removeImage" value="0">
            </div>

            <!-- Phone -->
            <div class="col-sm-12 form-control-validation">
                <label class="form-label" for="phone">Phone</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-phone"></i></span>
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter Phone" autocomplete="off" />
                </div>
            </div>

            <!-- Address -->
            <div class="col-sm-12 form-control-validation">
                <label class="form-label" for="address">Address</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><!-- <i class="icon-base ti tabler-location"></i> --></span>
                    <textarea id="address" name="address" rows='5' class="form-control" placeholder="Enter Address" autocomplete="off" /></textarea>
                </div>
            </div>

            <!-- Status -->
            <div class="col-sm-12 form-control-validation">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select input-group" >
                    <option value="">-- Select Status --</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <!-- Role -->
            @if($roles->isNotEmpty())
            <div class="col-sm-12 form-control-validation">
                <label class="form-label" for="role">Role</label>
                <select id="role_id" name="role_id" class="form-select input-group">
                    <option value="">-- Select Role --</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Password -->
            <div class="col-sm-12 form-control-validation">
                <label class="form-label" for="password">Password</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-lock"></i></span>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password" autocomplete="new-password" />
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="col-sm-12 form-control-validation">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="icon-base ti tabler-lock-check"></i></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm Password" autocomplete="new-password" />
                </div>
            </div>
            <div class="col-sm-12">
              <button type="submit" class="btn btn-primary data-submit me-sm-4 me-1">Submit</button>
              <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </div>
          </form>
        </div>
    </div>
</div>

@slot('footerBlock')
<script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>
<script type="text/javaScript">

    $( function() {
        // Initialize DataTable
        $('#admin-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            ordering: true,
            ajax: {
                url: @json(route($routeName . '.users.index')),
                data: function(d) {
                    @if(isset($currentRole) && !empty($currentRole))
                        d.role = @json($currentRole);
                    @endif
                }
            },
            columns: [
                { data: 'name', name: 'name', visible: false, searchable: true, orderable: true },
                { data: 'details', name: 'details', title: 'Details', orderable: true, searchable: false },
                { data: 'role', name: 'roles.name', title: 'Role', orderable: true, searchable: true },
                { data: 'status', name: 'status', title: 'Status' },
                { data: 'last_login_at', name: 'last_login_at', title: 'Last Login At', searchable: false },
                { data: 'created_at', name: 'created_at', title: 'Created At', searchable: false },
                { data: 'action', name: 'action', title: 'Actions', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[1, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search Users...",
                processing: "Loading...",
                emptyTable: "No User found.",
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $('#add-new-record').on('hidden.bs.offcanvas', function () {
            $('#previewImage').attr('src', '').addClass('d-none');
            $('#removeImageBtn').addClass('d-none');
            $('#removeImage').val(0);
        });

        // Get User Details
        $(document).on('click', '.open-user-modal', function () {
            const mode = $(this).data('mode');
            const title = $(this).data('title');
            const userId = $(this).data('id') || null;

            // Reset form and remove validation
            const $form = $('#form-add-new-record');
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();

            // Set modal title
            $('#add-new-record .offcanvas-title').text(title);

            if (mode === 'add') {
                // Set form action for adding
                $form.attr('action', '{{ route($routeName . ".users.add") }}');

                // Hide image-related elements
                $('#previewImage').attr('src', '').addClass('d-none');
                $('#removeImageBtn').addClass('d-none');
                $('#removeImage').val(0);    
            }

            if (mode === 'edit' && userId) {
                $.ajax({
                    url: '{{ route($routeName . ".users.getUser") }}',
                    method: 'POST',
                    data: {
                        id: userId
                    },
                    success: function (res) {
                        if (res.status) {
                            $('#name').val(res.data.name);
                            $('#email').val(res.data.email);
                            $('#phone').val(res.data.phone || '');
                            $('#address').val(res.data.address || '');
                            $('#status').val(res.data.status || '');
                            $('#role_id').val(res.data.role_id || '');
                            if (res.data.image && res.data.image.name) {
                                $('#previewImage')
                                    .attr('src', res.data.image.path)
                                    .removeClass('d-none');

                                $('#removeImageBtn').removeClass('d-none');
                                $('#removeImage').val(0);
                            } else {
                                $('#previewImage').attr('src', '').addClass('d-none');
                                $('#removeImageBtn').addClass('d-none');
                                $('#removeImage').val(0);
                            }

                            $form.attr('action', '{{ route($routeName . ".users.edit", ":id") }}'.replace(':id', userId));
                        } else {
                            toastr.error(res.message || 'User data not found.');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (const key in errors) {
                                const input = $(`#${key}`);
                                input.addClass('is-invalid');
                                input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                            }
                        } else {
                            const message = xhr.responseJSON?.message || 'An unexpected error occurred.';
                            toastr.error(message);
                        }
                    }
                });
            }
        });

        // Form submit with AJAX
        $(document).on('submit', '#form-add-new-record', function (e) {
            e.preventDefault();

            const $form = $(this);
            const formData = $form.serialize();
            const actionUrl = $form.attr('action');

            // Clear previous errors
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: formData,
                success: function (res) {
                    if (res.status) {
                        toastr.success(res.message || 'User saved successfully');

                        // Hide modal
                        $('#add-new-record').offcanvas('hide');

                        // Refresh table
                        $('#admin-table').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(res.message || 'Something went wrong.');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;

                        for (const key in errors) {
                            const input = $(`#${key}`);

                            // Mark input invalid
                            input.addClass('is-invalid');

                            // Find the input group container
                            const inputGroup = input.closest('.input-group');

                            // Remove any existing feedback within the same wrapper
                            inputGroup.next('.invalid-feedback').remove();

                            // Append error after input-group
                            inputGroup.after(`<div class="invalid-feedback d-block">${errors[key][0]}</div>`);
                        }
                    } else {
                        const message = xhr.responseJSON?.message || 'An unexpected error occurred.';
                        toastr.error(message);
                    }
                }
            });
        });

        let cropper;
        let croppedCanvas;

        $('#userImage').on('change', function (e) {
            const file = e.target.files[0];

            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (event) {
                const image = document.createElement('img');
                image.id = 'tempCropImage';
                image.src = event.target.result;
                image.style.maxWidth = '100%';

                const modalHtml = `
                <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Crop Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div style="max-width: 100%; overflow-x: auto;">
                          <img id="tempCropImage" src="${event.target.result}" style="max-width: 100%; height: auto; display: block; margin: auto;">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="cropImageBtn">Crop & Save</button>
                      </div>
                    </div>
                  </div>
                </div>
                `;

                $('body').append(modalHtml);
                const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
                cropperModal.show();

                $('#cropperModal').on('shown.bs.modal', function () {
                    cropper = new Cropper(document.getElementById('tempCropImage'), {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                        scalable: false,
                        zoomable: true,                        
                    });
                });

                $('#cropperModal').on('hidden.bs.modal', function () {
                    cropper.destroy();
                    $('#cropperModal').remove();
                });

                $(document).off('click', '#cropImageBtn').on('click', '#cropImageBtn', function () {
                    croppedCanvas = cropper.getCroppedCanvas({
                        width: 300,
                        height: 300,
                    });

                    $('#previewImage').attr('src', croppedCanvas.toDataURL()).removeClass('d-none');
                    $('#croppedImageInput').val(croppedCanvas.toDataURL());
                    cropperModal.hide();
                });
            };

            reader.readAsDataURL(file);
        });

        $(document).on('click', '#removeImageBtn', function () {
            $('#previewImage').attr('src', '').addClass('d-none');
            $('#removeImageBtn').addClass('d-none');
            $('#removeImage').val(1); // tells backend to delete
        });

        $(document).on('click', '.btn-delete-user', function (e) {
            e.preventDefault();

            const deleteUrl = $(this).data('url');

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
                        url: deleteUrl,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            if (res.status) {
                                Swal.fire('Deleted!', res.message || 'User has been deleted.', 'success');
                                $('#admin-table').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Error!', res.message || 'Failed to delete user.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        });

    });
</script>

@endslot

@endcomponent
