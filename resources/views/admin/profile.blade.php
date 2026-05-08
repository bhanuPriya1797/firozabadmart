@component('admin.layouts.main')
    @slot('title')
        My Profile - {{ config('app.name') }}
    @endslot

    @php
        $routeName = CustomHelper::getAdminRouteName();
    @endphp

    @slot('headerBlock')
    <link rel="stylesheet" href="{{ asset('admin/assets/vendor/css/pages/page-profile.css') }}" />
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
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
          <!-- Header -->
          <div class="row">
            <div class="col-12">
              <div class="card mb-6">
                <div class="user-profile-header-banner">
                  <img src="{{ asset('admin/assets/img/pages/profile-banner.png') }}" alt="{{ $user->name }} Banner image" class="rounded-top" />
                </div>
                <div class="user-profile-header d-flex flex-column flex-lg-row text-sm-start text-center mb-5">
                  <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                    <img
                      src="{{ $user->image['path'] }}"
                      alt="{{ $user->name }}"
                      class="d-block h-auto ms-0 ms-sm-6 rounded user-profile-img" />
                  </div>
                  <div class="flex-grow-1 mt-3 mt-lg-5">
                    <div
                      class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-4">
                      <div class="user-profile-info">
                        <h4 class="mb-2 mt-lg-6">{{ $user->name }}</h4>
                        <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4 my-2">
                            <li class="list-inline-item d-flex gap-2 align-items-center">
                                <i class="icon-base ti tabler-user-cog icon-lg"></i>
                                <span class="fw-medium">{{ $user->role }}</span>
                            </li>
                          @if(!empty($user->address))
                          <li class="list-inline-item d-flex gap-2 align-items-center">
                            <i class="icon-base ti tabler-map-pin icon-lg"></i
                            ><span class="fw-medium">{{ $user->address }}</span>
                          </li>
                          @endif
                          <li class="list-inline-item d-flex gap-2 align-items-center">
                            <i class="icon-base ti tabler-calendar icon-lg"></i
                            ><span class="fw-medium">Joined {{ \Carbon\Carbon::parse($user->created_at)->format('d M, Y') }}</span>
                          </li>
                        </ul>
                      </div>
                        <button 
                            class="btn update-profile btn-primary open-profile-modal"
                            type="button"
                            data-id="{{ $user->encrypt_id }}"
                            data-mode="edit"
                            data-title="Edit Profile"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#update-profile"
                        >
                            <span class="d-flex align-items-center gap-2">
                                <i class="icon-base ti tabler-plus icon-sm"></i>
                                <span class="d-none d-sm-inline-block">Edit Profile</span>
                            </span>
                        </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--/ Header -->

          <!-- Navbar pills -->
          <!-- <div class="row">
            <div class="col-md-12">
              <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-sm-row mb-6 gap-sm-0 gap-2">
                  <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"
                      ><i class="icon-base ti tabler-user-check icon-sm me-1_5"></i> Profile</a
                    >
                  </li>
                </ul>
              </div>
            </div>
          </div> -->
          <!--/ Navbar pills -->

          <!-- User Profile Content -->
          <div class="row">
            <div class="col-xl-12 col-lg-5 col-md-5">
              <!-- About User -->
              <div class="card mb-6">
                <div class="card-body">
                  <p class="card-text text-uppercase text-body-secondary small mb-0">About</p>
                  <ul class="list-unstyled my-3 py-1">
                    <li class="d-flex align-items-center mb-4">
                      <i class="icon-base ti tabler-user icon-lg"></i
                      ><span class="fw-medium mx-2">Full Name:</span> <span>{{ $user->name }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-4">
                      <i class="icon-base ti tabler-check icon-lg"></i><span class="fw-medium mx-2">Status:</span>
                      <span>{{ ($user->status == 1) ? "Active" : "Inactive" }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-4">
                      <i class="icon-base ti tabler-crown icon-lg"></i><span class="fw-medium mx-2">Role:</span>
                      <span>{{ $user->role }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-4">
                      <i class="icon-base ti tabler-flag icon-lg"></i><span class="fw-medium mx-2">Address:</span>
                      <span>{{ $user->address ?? "N/A" }}</span>
                    </li>
                  </ul>
                  <p class="card-text text-uppercase text-body-secondary small mb-0">Contacts</p>
                  <ul class="list-unstyled my-3 py-1">
                    <li class="d-flex align-items-center mb-4">
                      <i class="icon-base ti tabler-phone-call icon-lg"></i
                      ><span class="fw-medium mx-2">Contact:</span>
                      <span>{{ $user->phone }}</span>
                    </li>
                    <li class="d-flex align-items-center mb-4">
                      <i class="icon-base ti tabler-mail icon-lg"></i><span class="fw-medium mx-2">Email:</span>
                      <span>{{ $user->email }}</span>
                    </li>
                  </ul>
                </div>
              </div>
              <!--/ About User -->
            </div>
          </div>
          <!--/ User Profile Content -->

        <!-- Modal to update for profile -->
        <div class="offcanvas offcanvas-end" id="update-profile">
            <div class="offcanvas-header border-bottom">
              <h5 class="offcanvas-title" id="exampleModalLabel">Edit Profile</h5>
              <button
                type="button"
                class="btn-close text-reset"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
            </div>
            <div class="offcanvas-body flex-grow-1">
              <form class="update-profile pt-0 row g-2" id="form-update-profile" action="{{ route($routeName . '.profile.update') }}" autocomplete="off" onsubmit="return false">

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
        <!-- / Content -->

    @slot('footerBlock')
    <!-- <script src="{{ asset('admin/assets/js/app-user-view-account.js') }}"></script> -->
    <script src="https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js"></script>
    <script type="text/javaScript">

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            $('#update-profile').on('hidden.bs.offcanvas', function () {
                $('#previewImage').attr('src', '').addClass('d-none');
                $('#removeImageBtn').addClass('d-none');
                $('#removeImage').val(0);
            });

            // Get User Details
            $(document).on('click', '.open-profile-modal', function () {
                const mode = $(this).data('mode');
                const title = $(this).data('title');
                const userId = $(this).data('id') || null;
                //alert(mode);

                /*const offcanvas = new bootstrap.Offcanvas(document.getElementById('update-profile'));
                    offcanvas.show();*/

                // Reset form and remove validation
                const $form = $('#form-update-profile');
                $form[0].reset();
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('.invalid-feedback').remove();

                // Set modal title
                $('#update-profile .offcanvas-title').text(title);

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

                                //$form.attr('action', '{{ route($routeName . ".users.edit", ":id") }}'.replace(':id', userId));
                                //$form.attr('action', '{{ route($routeName . ".profile.update") }}';
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
            $(document).on('submit', '#form-update-profile', function (e) {
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
                            toastr.success(res.message || 'Profile updated successfully');

                            const offcanvasElement = document.getElementById('update-profile');
                            const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
                            if (offcanvasInstance) {
                                offcanvasInstance.hide();
                            }

                            document.body.classList.remove('offcanvas-backdrop');
                            $('.offcanvas-backdrop').remove();

                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
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

        });
    </script>
    @endslot
@endcomponent