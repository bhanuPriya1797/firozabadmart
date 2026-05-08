@component('admin.layouts.main')
    @slot('title')
        Settings - {{ config('app.name') }}
    @endslot

    @php
        $routeName = CustomHelper::getAdminRouteName();
    @endphp

    @slot('headerBlock')
   
    @endslot

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Settings</h4>

            <!-- <button class="btn create-new btn-primary waves-effect waves-light" type="button" data-bs-toggle="modal" data-bs-target="#addSettingModal" data-mode="add" data-title="Add New Setting">
                <span class="d-flex align-items-center gap-2">
                    <i class="icon-base ti tabler-plus icon-sm"></i>
                    <span class="d-none d-sm-inline-block">Add New Setting</span>
                </span>
            </button> -->

        </div>

        @php
            $homepageSettings = collect();
            $otherGroups = $allSettings->filter(function($v, $k){ return $k !== 'Homepage'; });
        @endphp

        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="website-tab" data-bs-toggle="tab" data-bs-target="#website" type="button" role="tab" aria-controls="website" aria-selected="true">Website Settings</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="homepage-tab" data-bs-toggle="tab" data-bs-target="#homepage" type="button" role="tab" aria-controls="homepage" aria-selected="false">Homepage Settings</button>
            </li>
        </ul>

        <div class="tab-content mt-4" id="settingsTabsContent">
            <!-- Other Website Settings Tab -->
            <div class="tab-pane fade show active" id="website" role="tabpanel" aria-labelledby="website-tab">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route($routeName.'.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                @foreach($otherGroups as $groupName => $settings)
                                    <div class="col-12 mb-4">
                                        <h5 class="fw-bold border-bottom pb-1 mb-3">{{ $groupName ?? 'General' }}</h5>
                                        <div class="row g-4">
                                            @foreach($settings as $setting)
                                                @include('admin.settings._fields', ['setting' => $setting])
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="submit" class="btn btn-success mt-3">Update Website</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Homepage Settings Tab -->
            <div class="tab-pane fade" id="homepage" role="tabpanel" aria-labelledby="homepage-tab">
                <div class="card">
                    <div class="card-body">
                        <form id="homepageSettingsForm" action="{{ route($routeName.'.settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @include('admin.settings.homepage')
                            <button type="submit" class="btn btn-success mt-3">Update Homepage</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Setting Modal -->
    <div class="modal fade" id="addSettingModal" tabindex="-1" aria-labelledby="addSettingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSettingModalLabel">Add Custom Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route($routeName.'.settings.store') }}" method="POST">
                    @csrf
                    <div class="modal-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Label</label>
                            <input type="text" name="label" class="form-control" placeholder="Setting Label" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Key</label>
                            <input type="text" name="key" class="form-control" placeholder="setting_key" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Group Name</label>
                            <input type="text" name="group_name" class="form-control" placeholder="Group (e.g., SEO)">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Field Type</label>
                            <select name="type" class="form-select" id="fieldType" required>
                                <option value="text">Text</option>
                                <option value="email">Email</option>
                                <option value="phone">Phone</option>
                                <option value="textarea">Textarea</option>
                                <option value="editor">Editor</option>
                                <option value="file">File</option>
                                <option value="number">Number</option>
                                <option value="select">Select</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio</option>
                            </select>
                        </div>

                        <div class="col-md-12 d-none" id="fieldOptionsWrapper">
                            <label class="form-label">Options <small>(comma-separated)</small></label>
                            <input type="text" name="options" id="fieldOptions" class="form-control" placeholder="option1,option2,option3">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">CSS Class</label>
                            <input type="text" name="class" class="form-control" placeholder="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Validation Rules (JSON)</label>
                            <input type="text" name="validation" class="form-control" placeholder='{"max":"255", "required":true}'>
                        </div>

                        <div class="col-md-12 d-none" id="fileValidationWrapper">
                            <label class="form-label">File Constraints (JSON)</label>
                            <input type="text" name="file_constraints" class="form-control" placeholder='{"max_size":2048, "types":["jpg","png"]}'>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create Field</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Content -->

    @slot('footerBlock')
    <script>
        $(document).ready(function () {
            $('#fieldType').on('change', function () {
                const type = $(this).val();

                if (['select', 'checkbox', 'radio'].includes(type)) {
                    $('#fieldOptionsWrapper').removeClass('d-none');
                } else {
                    $('#fieldOptionsWrapper').addClass('d-none');
                }

                if (type === 'file') {
                    $('#fileValidationWrapper').removeClass('d-none');
                } else {
                    $('#fileValidationWrapper').addClass('d-none');
                }
            });

            // Don't manually force modal-open removal
            $('#addSettingModal').on('hidden.bs.modal', function () {
                $('.modal-backdrop').remove();
            });

            $('.delete-setting-file').on('click', function () {
                const key = $(this).data('key');

                // Mark for deletion
                $('#delete-file-' + key).val('1');

                // Hide preview
                $('#preview-' + key).remove();
            });

            // Homepage logic moved into homepage.blade.php to avoid double-binding
        });
    </script>
    @endslot

@endcomponent
