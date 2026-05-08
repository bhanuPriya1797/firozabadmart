@component('admin.layouts.main')

@slot('title')
    Custom Fields - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Custom Fields</h4>
        @if(auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('custom_fields.create')))
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addCustomFieldModal">
            <i class="icon-base ti tabler-plus icon-sm"></i>
            <span class="d-none d-sm-inline-block">Add Custom Field</span>
        </button>
        @endif
    </div>

    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="custom-fields-table">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Key</th>
                        <th>Type</th>
                        <th>Module</th>
                        <th>Group</th>
                        <th>Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Custom Field Modal -->
<div class="modal fade" id="addCustomFieldModal" tabindex="-1" aria-labelledby="addCustomFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomFieldModalLabel">Add Custom Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customFieldForm">
                <div class="modal-body">
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="ti tabler-info-circle me-2"></i>Basic Information
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Field Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="label" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Field Key</label>
                            <input type="text" class="form-control" name="key" placeholder="Auto-generated if empty">
                            <small class="text-muted">Only letters, numbers, and underscores. Must start with a letter.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Field Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="">Select Field Type</option>
                                <optgroup label="Text Fields">
                                    <option value="text">Single Line Text</option>
                                    <option value="textarea">Multi-line Text</option>
                                    <option value="email">Email Address</option>
                                    <option value="phone">Phone Number</option>
                                    <option value="number">Number</option>
                                    <option value="url">URL/Link</option>
                                </optgroup>
                                <optgroup label="Rich Content">
                                    <option value="editor">Rich Text Editor</option>
                                    <option value="file">File Upload</option>
                                    <option value="image">Image Upload</option>
                                </optgroup>
                                <optgroup label="Selection Fields">
                                    <option value="select">Dropdown Select</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="radio">Radio Buttons</option>
                                </optgroup>
                                <optgroup label="Date & Time">
                                    <option value="date">Date</option>
                                    <option value="time">Time</option>
                                    <option value="datetime">Date & Time</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Module <span class="text-danger">*</span></label>
                            <select class="form-select" name="module" required>
                                <option value="">Select Module</option>
                                <option value="settings">Settings</option>
                                <option value="cms">CMS Pages</option>
                                <option value="blog">Blog Posts</option>
                                <option value="news">News</option>
                                <option value="events">Events</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Group Name</label>
                            <input type="text" class="form-control" name="group_name" placeholder="e.g., Basic Info, Advanced Settings">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CSS Class</label>
                            <input type="text" class="form-control" name="class" placeholder="e.g., form-control-lg">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference ID</label>
                            <input type="number" class="form-control" name="module_ref_id" placeholder="Specific page/module ID">
                        </div>
                    </div>

                    <!-- Options Configuration -->
                    <div class="row mb-4 options-section" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="ti tabler-list me-2"></i>Options Configuration
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Multiple Selection</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_multiple" id="isMultiple">
                                <label class="form-check-label" for="isMultiple">
                                    Allow multiple selections
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Maximum Selections</label>
                            <input type="number" class="form-control" name="max_selections" min="1" placeholder="Leave empty for unlimited">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Options <span class="text-danger">*</span></label>
                            <div id="optionsContainer">
                                <div class="option-row mb-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="options[]" placeholder="Option value" required>
                                        <input type="text" class="form-control" name="option_labels[]" placeholder="Display label" required>
                                        <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">
                                            <i class="ti tabler-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addOption">
                                <i class="ti tabler-plus me-1"></i>Add Option
                            </button>
                        </div>
                    </div>

                    <!-- Validation Rules -->
                    <div class="row mb-4 validation-section" style="display: none;">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="ti tabler-shield-check me-2"></i>Validation Rules
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="validation_required" id="validationRequired">
                                <label class="form-check-label" for="validationRequired">
                                    Required Field
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="validation_unique" id="validationUnique">
                                <label class="form-check-label" for="validationUnique">
                                    Unique Value
                                </label>
                            </div>
                        </div>
                        
                        <!-- Text/Email/Number Validation -->
                        <div class="text-validation" style="display: none;">
                            <div class="col-md-3">
                                <label class="form-label">Minimum Length</label>
                                <input type="number" class="form-control" name="min_length" min="0" placeholder="Min">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Maximum Length</label>
                                <input type="number" class="form-control" name="max_length" min="0" placeholder="Max">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Regex Pattern</label>
                                <input type="text" class="form-control" name="regex_pattern" placeholder="e.g., ^[A-Za-z]+$">
                                <small class="text-muted">Custom validation pattern</small>
                            </div>
                        </div>

                        <!-- Number Validation -->
                        <div class="number-validation" style="display: none;">
                            <div class="col-md-3">
                                <label class="form-label">Minimum Value</label>
                                <input type="number" class="form-control" name="min_value" placeholder="Min">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Maximum Value</label>
                                <input type="number" class="form-control" name="max_value" placeholder="Max">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Decimal Places</label>
                                <input type="number" class="form-control" name="decimal_places" min="0" max="10" placeholder="0">
                            </div>
                        </div>

                        <!-- File Validation -->
                        <div class="file-validation" style="display: none;">
                            <div class="col-md-4">
                                <label class="form-label">Maximum File Size (MB)</label>
                                <input type="number" class="form-control" name="max_file_size" min="1" placeholder="2">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Allowed File Types</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_types[]" value="jpg" id="typeJpg">
                                            <label class="form-check-label" for="typeJpg">JPG/JPEG</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_types[]" value="png" id="typePng">
                                            <label class="form-check-label" for="typePng">PNG</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_types[]" value="gif" id="typeGif">
                                            <label class="form-check-label" for="typeGif">GIF</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_types[]" value="pdf" id="typePdf">
                                            <label class="form-check-label" for="typePdf">PDF</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_types[]" value="doc" id="typeDoc">
                                            <label class="form-check-label" for="typeDoc">DOC</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_types[]" value="docx" id="typeDocx">
                                            <label class="form-check-label" for="typeDocx">DOCX</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_types[]" value="xls" id="typeXls">
                                            <label class="form-check-label" for="typeXls">XLS</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="allowed_types[]" value="xlsx" id="typeXlsx">
                                            <label class="form-check-label" for="typeXlsx">XLSX</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date/Time Validation -->
                        <div class="date-validation" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">Minimum Date</label>
                                <input type="date" class="form-control" name="min_date">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Maximum Date</label>
                                <input type="date" class="form-control" name="max_date">
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Settings -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="ti tabler-settings me-2"></i>Advanced Settings
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Value</label>
                            <input type="text" class="form-control" name="default_value" placeholder="Default value for this field">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Help Text</label>
                            <input type="text" class="form-control" name="help_text" placeholder="Help text to display below field">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Placeholder Text</label>
                            <input type="text" class="form-control" name="placeholder" placeholder="Placeholder text">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_searchable" id="isSearchable">
                                <label class="form-check-label" for="isSearchable">
                                    Include in search
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Custom Field</button>
                </div>
            </form>
        </div>
    </div>
</div>

@slot('footerBlock')

<script>
    $(function () {
        $('#custom-fields-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: @json(route($routeName . '.custom_fields.index')),
            columns: [
                { data: 'label', name: 'label' },
                { data: 'key', name: 'key' },
                { data: 'type_badge', name: 'type', orderable: false, searchable: false },
                { data: 'module_info', name: 'module' },
                { data: 'group_name', name: 'group_name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            order: [[5, 'desc']],
            language: {
                searchPlaceholder: "Search Custom Fields...",
                emptyTable: "No custom fields found.",
                processing: "Loading..."
            }
        });
    });

    // Show/hide sections based on type selection
    $('select[name="type"]').change(function() {
        var type = $(this).val();
        
        // Hide all sections first
        $('.options-section, .validation-section, .text-validation, .number-validation, .file-validation, .date-validation').hide();
        
        // Show validation section for all types
        $('.validation-section').show();
        
        // Show specific sections based on type
        if (['select', 'checkbox', 'radio'].includes(type)) {
            $('.options-section').show();
        }
        
        if (['text', 'textarea', 'email', 'phone', 'url'].includes(type)) {
            $('.text-validation').show();
        }
        
        if (type === 'number') {
            $('.number-validation').show();
        }
        
        if (['file', 'image'].includes(type)) {
            $('.file-validation').show();
        }
        
        if (['date', 'time', 'datetime'].includes(type)) {
            $('.date-validation').show();
        }
    });

    // Auto-generate key from label
    $('input[name="label"]').on('input', function() {
        if (!$('input[name="key"]').val()) {
            var key = $(this).val()
                .toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '_');
            $('input[name="key"]').val(key);
        }
    });

    // Add option functionality
    $('#addOption').click(function() {
        var optionRow = `
            <div class="option-row mb-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="options[]" placeholder="Option value" required>
                    <input type="text" class="form-control" name="option_labels[]" placeholder="Display label" required>
                    <button type="button" class="btn btn-outline-danger remove-option">
                        <i class="ti tabler-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#optionsContainer').append(optionRow);
        updateRemoveButtons();
    });

    // Remove option functionality
    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-row').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        var optionRows = $('.option-row');
        if (optionRows.length === 1) {
            optionRows.find('.remove-option').hide();
        } else {
            optionRows.find('.remove-option').show();
        }
    }

    // Handle form submission
    $('#customFieldForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var url = '{{ route($routeName . ".custom_fields.store") }}';
        var method = 'POST';
        
        if ($('#addCustomFieldModal').data('mode') === 'edit') {
            url = '{{ route($routeName . ".custom_fields.update", ":id") }}'.replace(':id', $('#addCustomFieldModal').data('field-id'));
            method = 'POST';
        }

        // Build validation object
        var validation = {};
        
        // Basic validation
        if ($('input[name="validation_required"]').is(':checked')) {
            validation.required = true;
        }
        if ($('input[name="validation_unique"]').is(':checked')) {
            validation.unique = true;
        }
        
        // Text validation
        if ($('.text-validation').is(':visible')) {
            if ($('input[name="min_length"]').val()) {
                validation.min_length = parseInt($('input[name="min_length"]').val());
            }
            if ($('input[name="max_length"]').val()) {
                validation.max_length = parseInt($('input[name="max_length"]').val());
            }
            if ($('input[name="regex_pattern"]').val()) {
                validation.regex = $('input[name="regex_pattern"]').val();
            }
        }
        
        // Number validation
        if ($('.number-validation').is(':visible')) {
            if ($('input[name="min_value"]').val()) {
                validation.min_value = parseFloat($('input[name="min_value"]').val());
            }
            if ($('input[name="max_value"]').val()) {
                validation.max_value = parseFloat($('input[name="max_value"]').val());
            }
            if ($('input[name="decimal_places"]').val()) {
                validation.decimal_places = parseInt($('input[name="decimal_places"]').val());
            }
        }
        
        // File validation
        if ($('.file-validation').is(':visible')) {
            var fileConstraints = {};
            if ($('input[name="max_file_size"]').val()) {
                fileConstraints.max_size = parseInt($('input[name="max_file_size"]').val()) * 1024; // Convert to KB
            }
            var allowedTypes = [];
            $('input[name="allowed_types[]"]:checked').each(function() {
                allowedTypes.push($(this).val());
            });
            if (allowedTypes.length > 0) {
                fileConstraints.allowed_types = allowedTypes;
            }
            if (Object.keys(fileConstraints).length > 0) {
                validation.file_constraints = fileConstraints;
            }
        }
        
        // Date validation
        if ($('.date-validation').is(':visible')) {
            if ($('input[name="min_date"]').val()) {
                validation.min_date = $('input[name="min_date"]').val();
            }
            if ($('input[name="max_date"]').val()) {
                validation.max_date = $('input[name="max_date"]').val();
            }
        }
        
        // Add validation to form data
        if (Object.keys(validation).length > 0) {
            formData.append('validation', JSON.stringify(validation));
        }
        
        // Build options array
        var options = [];
        var optionLabels = [];
        $('input[name="options[]"]').each(function(index) {
            if ($(this).val().trim()) {
                options.push($(this).val().trim());
                var label = $('input[name="option_labels[]"]').eq(index).val().trim();
                optionLabels.push(label || $(this).val().trim());
            }
        });
        
        if (options.length > 0) {
            formData.append('options', JSON.stringify(options));
            formData.append('option_labels', JSON.stringify(optionLabels));
        }
        
        // Add multiple selection info
        if ($('input[name="is_multiple"]').is(':checked')) {
            formData.append('is_multiple', '1');
        }
        if ($('input[name="max_selections"]').val()) {
            formData.append('max_selections', $('input[name="max_selections"]').val());
        }
        
        // Add searchable flag
        if ($('input[name="is_searchable"]').is(':checked')) {
            formData.append('is_searchable', '1');
        }

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.message);
                    $('#addCustomFieldModal').modal('hide');
                    $('#custom-fields-table').DataTable().ajax.reload(null, false);
                    $('#customFieldForm')[0].reset();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        toastr.error(errors[key][0]);
                    });
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            }
        });
    });

    // Reset modal on close
    $('#addCustomFieldModal').on('hidden.bs.modal', function() {
        $('#customFieldForm')[0].reset();
        $('.options-section, .validation-section, .text-validation, .number-validation, .file-validation, .date-validation').hide();
        $('#optionsContainer').html(`
            <div class="option-row mb-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="options[]" placeholder="Option value" required>
                    <input type="text" class="form-control" name="option_labels[]" placeholder="Display label" required>
                    <button type="button" class="btn btn-outline-danger remove-option" style="display: none;">
                        <i class="ti tabler-trash"></i>
                    </button>
                </div>
            </div>
        `);
        $(this).data('mode', 'add');
        $(this).find('.modal-title').text('Add Custom Field');
    });

    // Edit custom field
    $(document).on('click', '.btn-edit-field', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        $.ajax({
            url: '{{ route($routeName . ".custom_fields.edit", ":id") }}'.replace(':id', id),
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status) {
                    var field = response.data;
                    
                    // Set modal mode and field ID
                    $('#addCustomFieldModal').data('mode', 'edit').data('field-id', id);
                    $('#addCustomFieldModal').find('.modal-title').text('Edit Custom Field');
                    
                    // Populate basic fields
                    $('input[name="label"]').val(field.label || '');
                    $('input[name="key"]').val(field.key || '');
                    $('select[name="type"]').val(field.type || '').trigger('change');
                    $('select[name="module"]').val(field.module || '');
                    $('input[name="group_name"]').val(field.group_name || '');
                    $('input[name="class"]').val(field.class || '');
                    $('input[name="module_ref_id"]').val(field.ref_id || '');
                    $('input[name="default_value"]').val(field.default_value || '');
                    $('input[name="help_text"]').val(field.help_text || '');
                    $('input[name="placeholder"]').val(field.placeholder || '');
                    
                                         // Handle options
                     if (field.options && Array.isArray(field.options)) {
                         $('#optionsContainer').empty();
                         field.options.forEach(function(optionData, index) {
                             // Handle both old format (string) and new format (object)
                             var optionValue, optionLabel;
                             if (typeof optionData === 'string') {
                                 optionValue = optionData;
                                 optionLabel = field.option_labels && field.option_labels[index] ? field.option_labels[index] : optionData;
                             } else if (optionData && typeof optionData === 'object') {
                                 optionValue = optionData.value || '';
                                 optionLabel = optionData.label || optionValue;
                             } else {
                                 optionValue = '';
                                 optionLabel = '';
                             }
                             
                             var optionRow = `
                                 <div class="option-row mb-2">
                                     <div class="input-group">
                                         <input type="text" class="form-control" name="options[]" value="${optionValue}" required>
                                         <input type="text" class="form-control" name="option_labels[]" value="${optionLabel}" required>
                                         <button type="button" class="btn btn-outline-danger remove-option">
                                             <i class="ti tabler-trash"></i>
                                         </button>
                                     </div>
                                 </div>
                             `;
                             $('#optionsContainer').append(optionRow);
                         });
                         updateRemoveButtons();
                     }
                    
                    // Handle validation
                    if (field.validation) {
                        var validation = typeof field.validation === 'string' ? JSON.parse(field.validation) : field.validation;
                        
                        // Basic validation
                        if (validation.required) {
                            $('input[name="validation_required"]').prop('checked', true);
                        }
                        if (validation.unique) {
                            $('input[name="validation_unique"]').prop('checked', true);
                        }
                        
                        // Text validation
                        if (validation.min_length) {
                            $('input[name="min_length"]').val(validation.min_length);
                        }
                        if (validation.max_length) {
                            $('input[name="max_length"]').val(validation.max_length);
                        }
                        if (validation.regex) {
                            $('input[name="regex_pattern"]').val(validation.regex);
                        }
                        
                        // Number validation
                        if (validation.min_value) {
                            $('input[name="min_value"]').val(validation.min_value);
                        }
                        if (validation.max_value) {
                            $('input[name="max_value"]').val(validation.max_value);
                        }
                        if (validation.decimal_places) {
                            $('input[name="decimal_places"]').val(validation.decimal_places);
                        }
                        
                        // File validation
                        if (validation.file_constraints) {
                            if (validation.file_constraints.max_size) {
                                $('input[name="max_file_size"]').val(Math.round(validation.file_constraints.max_size / 1024));
                            }
                            if (validation.file_constraints.allowed_types) {
                                validation.file_constraints.allowed_types.forEach(function(type) {
                                    $(`input[name="allowed_types[]"][value="${type}"]`).prop('checked', true);
                                });
                            }
                        }
                        
                        // Date validation
                        if (validation.min_date) {
                            $('input[name="min_date"]').val(validation.min_date);
                        }
                        if (validation.max_date) {
                            $('input[name="max_date"]').val(validation.max_date);
                        }
                    }
                    
                    // Handle multiple selection
                    if (field.is_multiple) {
                        $('input[name="is_multiple"]').prop('checked', true);
                    }
                    if (field.max_selections) {
                        $('input[name="max_selections"]').val(field.max_selections);
                    }
                    
                    // Handle searchable
                    if (field.is_searchable) {
                        $('input[name="is_searchable"]').prop('checked', true);
                    }
                    
                    // Show the modal
                    $('#addCustomFieldModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to load custom field data');
                }
            },
            error: function(xhr) {
                toastr.error('Error loading custom field data. Please try again.');
            }
        });
    });

    // Delete custom field
    $(document).on('click', '.btn-delete-field', function (e) {
        e.preventDefault();
        const url = $(this).data('url');

        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res.status) {
                            toastr.success(res.message || 'Custom field deleted successfully.');
                            $('#custom-fields-table').DataTable().ajax.reload(null, false);
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
</script>
@endslot

@endcomponent
