@component('admin.layouts.main')

    @slot('title')
    {{ isset($cms) ? 'Edit' : 'Create' }} CMS Page - {{ config('app.name') }}
    @endslot

@slot('headerBlock')

    @endslot
    <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
            <div class="col-12">
                <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $page_title }}</h5>
                    <a href="{{ route(CustomHelper::getAdminRouteName() . '.cms.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                    <div class="card-body">
                    @php
                        $routeName = CustomHelper::getAdminRouteName();
                        $formAction = isset($cms) ? route($routeName . '.cms.update', $cms->id) : route($routeName . '.cms.store');
                        \Log::info('CMS form route:', ['routeName' => $routeName, 'formAction' => $formAction, 'isEdit' => isset($cms)]);
                        
                        // Debug validation errors
                        if ($errors->any()) {
                            \Log::error('Validation errors in CMS form:', $errors->toArray());
                        }
                    @endphp
                    <form action="{{ $formAction }}" 
                          method="POST" enctype="multipart/form-data">
                            @csrf
                        @if(isset($cms))
                            @method('PUT')
                        @endif
                        
                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="ti tabler-info-circle me-2"></i>Basic Information
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Page Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title', $cms->title ?? '') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Slug</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                       name="slug" value="{{ old('slug', $cms->slug ?? '') }}" 
                                       placeholder="Leave empty to auto-generate from title">
                                <small class="form-text text-muted">Leave empty to automatically generate from the page title</small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Template <span class="text-danger">*</span></label>
                                <select class="form-select @error('template') is-invalid @enderror" name="template" required>
                                    <option value="">Select Template</option>
                                    @foreach($templates as $key => $name)
                                        <option value="{{ $key }}" {{ old('template', $cms->template ?? '') == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('template')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Brief Description</label>
                                <input type="text" class="form-control @error('brief') is-invalid @enderror" 
                                       name="brief" value="{{ old('brief', $cms->brief ?? '') }}">
                                @error('brief')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Page Heading</label>
                                <input type="text" class="form-control @error('heading') is-invalid @enderror" 
                                       name="heading" value="{{ old('heading', $cms->heading ?? '') }}">
                                @error('heading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Page Description</label>
                                <textarea class="form-control ckeditor @error('description') is-invalid @enderror" 
                                          name="description" id="description" rows="4">{{ old('description', isset($cms) && $cms->description ? html_entity_decode($cms->description, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Banner Image -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Banner Image</label>
                                <div class="image-upload-container">
                                    <div class="input-group">
                                        <input type="file" class="form-control @error('banner') is-invalid @enderror" 
                                               name="banner" id="banner" accept="image/*" data-preview="banner-local-preview">
                                        <button type="button" class="btn btn-outline-secondary" onclick="openMediaManager('banner_media_path')">
                                            <i class="ti tabler-photo me-1"></i> Library
                                        </button>
                                    </div>
                                    <input type="hidden" name="banner_media_path" id="banner_media_path">
                                    @error('banner')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Previews -->
                                    <div class="mt-2">
                                        <!-- Local Upload Preview -->
                                        <div id="banner-local-preview" style="display: none;"></div>

                                        <!-- Database Image -->
                                        @if(isset($cms) && $cms->banner)
                                            <div class="image-preview-wrapper mb-2 position-relative d-inline-block" id="banner-current-preview">
                                                <img src="{{ asset('storage/' . $cms->banner) }}" class="img-thumbnail" style="max-height: 150px;">
                                                <button type="button" class="btn btn-sm btn-icon btn-danger position-absolute top-0 end-0 m-1 rounded-pill shadow-sm" onclick="deleteImage('banner', '{{ $cms->id }}')">
                                                    <i class="ti tabler-x"></i>
                                                </button>
                                            </div>
                                        @endif
                                        <!-- Selected from Library Preview -->
                                        <div class="position-relative d-inline-block" id="banner_media_path_preview_wrapper">
                                            <img id="banner_media_path_preview" src="" class="img-thumbnail" style="max-height: 150px; display: none;">
                                            <button type="button" class="btn btn-sm btn-icon btn-danger position-absolute top-0 end-0 m-1 rounded-pill shadow-sm" 
                                                    id="banner_media_path_remove" style="display: none;" onclick="clearMediaPreview('banner_media_path')">
                                                <i class="ti tabler-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Page Image -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Page Image</label>
                                <div class="image-upload-container">
                                    <div class="input-group">
                                        <input type="file" class="form-control @error('page_image') is-invalid @enderror" 
                                               name="page_image" id="page_image" accept="image/*" data-preview="page-image-local-preview">
                                        <button type="button" class="btn btn-outline-secondary" onclick="openMediaManager('page_image_media_path')">
                                            <i class="ti tabler-photo me-1"></i> Library
                                        </button>
                                    </div>
                                    <input type="hidden" name="page_image_media_path" id="page_image_media_path">
                                    @error('page_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Previews -->
                                    <div class="mt-2">
                                        <!-- Local Upload Preview -->
                                        <div id="page-image-local-preview" style="display: none;"></div>

                                        <!-- Database Image -->
                                        @if(isset($cms) && $cms->page_image)
                                            <div class="image-preview-wrapper mb-2 position-relative d-inline-block" id="page-image-current-preview">
                                                <img src="{{ asset('storage/' . $cms->page_image) }}" class="img-thumbnail" style="max-height: 150px;">
                                                <button type="button" class="btn btn-sm btn-icon btn-danger position-absolute top-0 end-0 m-1 rounded-pill shadow-sm" onclick="deleteImage('page_image', '{{ $cms->id }}')">
                                                    <i class="ti tabler-x"></i>
                                                </button>
                                            </div>
                                        @endif
                                        <!-- Selected from Library Preview -->
                                        <div class="position-relative d-inline-block" id="page_image_media_path_preview_wrapper">
                                            <img id="page_image_media_path_preview" src="" class="img-thumbnail" style="max-height: 150px; display: none;">
                                            <button type="button" class="btn btn-sm btn-icon btn-danger position-absolute top-0 end-0 m-1 rounded-pill shadow-sm" 
                                                    id="page_image_media_path_remove" style="display: none;" onclick="clearMediaPreview('page_image_media_path')">
                                                <i class="ti tabler-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" 
                                           {{ old('status', $cms->status ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">Active Status</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="featured" value="1" 
                                           {{ old('featured', $cms->featured ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">Featured Page</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       name="sort_order" value="{{ old('sort_order', $cms->sort_order ?? 0) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                </div>
                            </div>

                        <!-- SEO Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="ti tabler-search me-2"></i>SEO Information
                                </h6>
                    </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Meta Title</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                       name="meta_title" value="{{ old('meta_title', $cms->seo['meta_title'] ?? '') }}" 
                                       placeholder="Enter meta title for SEO">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Meta Keywords</label>
                                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                       name="meta_keywords" value="{{ old('meta_keywords', $cms->seo['meta_keywords'] ?? '') }}" 
                                       placeholder="Enter meta keywords (comma separated)">
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Meta Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                          name="meta_description" rows="3" 
                                          placeholder="Enter meta description for SEO">{{ old('meta_description', $cms->seo['meta_description'] ?? '') }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
        </div>
    </div>

                        <!-- Custom Fields by Group -->
                        @if(isset($customFieldsGrouped) && $customFieldsGrouped && $customFieldsGrouped->count() > 0)
                            @foreach($customFieldsGrouped as $groupName => $fields)
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">
                                                    <i class="ti tabler-settings me-2"></i>{{ $groupName ?? 'Additional Fields' }}
                                                </h6>
                </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @php
                                                        $textFields = $fields->whereIn('type', ['text', 'email', 'number', 'url', 'date', 'time', 'datetime']);
                                                        $textareaFields = $fields->whereIn('type', ['textarea', 'editor']);
                                                        $textareaFields = $textareaFields->filter(function($f){ return !in_array($f->key, ['features','counters','team_members']); });
                                                        $selectFields = $fields->whereIn('type', ['select', 'checkbox', 'radio']);
                                                        $fileFields = $fields->whereIn('type', ['file', 'image']);
                                                    @endphp
                                                    
                                                    <!-- Text Fields (col-md-4) -->
                                                    @if($textFields->count() > 0)
                                                        @foreach($textFields as $field)
                                                            @php
                                                                $value = (isset($customFieldValues) && is_array($customFieldValues) && isset($customFieldValues[$field->key])) ? $customFieldValues[$field->key] : ($field->default_value ?? '');
                                                                $required = isset($field->validation['required']) && $field->validation['required'] ? 'required' : '';
                                                                $placeholder = $field->placeholder ?? '';
                                                                $helpText = $field->help_text ?? '';
                                                                $errorClass = $errors->has($field->key) ? 'is-invalid' : '';
                                                            @endphp
                                                            
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-semibold">
                                                                    {{ $field->label }}
                                                                    @if($required)
                                                                        <span class="text-danger">*</span>
                                                                    @endif
                                                                </label>

                                                                @switch($field->type)
                                                                    @case('text')
                                                                        <input type="text" 
                                                                               class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                               name="{{ $field->key }}" 
                                                                               value="{{ old($field->key, $value) }}" 
                                                                               placeholder="{{ $placeholder }}" 
                                                                               {{ $required }}>
                                                                        @break

                                                                    @case('email')
                                                                        <input type="email" 
                                                                               class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                               name="{{ $field->key }}" 
                                                                               value="{{ old($field->key, $value) }}" 
                                                                               placeholder="{{ $placeholder }}" 
                                                                               {{ $required }}>
                                                                        @break

                                                                    @case('number')
                                                                        @php
                                                                            $min = $field->validation['min_value'] ?? '';
                                                                            $max = $field->validation['max_value'] ?? '';
                                                                        @endphp
                                                                        <input type="number" 
                                                                               class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                               name="{{ $field->key }}" 
                                                                               value="{{ old($field->key, $value) }}" 
                                                                               placeholder="{{ $placeholder }}" 
                                                                               @if($min !== '') min="{{ $min }}" @endif
                                                                               @if($max !== '') max="{{ $max }}" @endif
                                                                               {{ $required }}>
                                                                        @break

                                                                    @case('url')
                                                                        <input type="url" 
                                                                               class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                               name="{{ $field->key }}" 
                                                                               value="{{ old($field->key, $value) }}" 
                                                                               placeholder="{{ $placeholder }}" 
                                                                               {{ $required }}>
                                                                        @break

                                                                    @case('date')
                                                                        <input type="date" 
                                                                               class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                               name="{{ $field->key }}" 
                                                                               value="{{ old($field->key, $value) }}" 
                                                                               {{ $required }}>
                                                                        @break

                                                                    @case('time')
                                                                        <input type="time" 
                                                                               class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                               name="{{ $field->key }}" 
                                                                               value="{{ old($field->key, $value) }}" 
                                                                               {{ $required }}>
                                                                        @break

                                                                    @case('datetime')
                                                                        <input type="datetime-local" 
                                                                               class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                               name="{{ $field->key }}" 
                                                                               value="{{ old($field->key, $value) }}" 
                                                                               {{ $required }}>
                                                                        @break

                                                                    @default
                                                                        <input type="text" 
                                                                               class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                               name="{{ $field->key }}" 
                                                                               value="{{ old($field->key, $value) }}" 
                                                                               placeholder="{{ $placeholder }}" 
                                                                               {{ $required }}>
                                                                        @break
                                                                @endswitch

                                                                @if($helpText)
                                                                    <div class="form-text text-muted small">{{ $helpText }}</div>
                                                                @endif

                                                                @error($field->key)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                        </div>
                                                        @endforeach
                                                    @endif
                                                    
                                                    <!-- Select Fields (col-md-6) -->
                                                    @if($selectFields->count() > 0)
                                                        @foreach($selectFields as $field)
                                                            @php
                                                                $value = (isset($customFieldValues) && is_array($customFieldValues) && isset($customFieldValues[$field->key])) ? $customFieldValues[$field->key] : ($field->default_value ?? '');
                                                                $required = isset($field->validation['required']) && $field->validation['required'] ? 'required' : '';
                                                                $placeholder = $field->placeholder ?? '';
                                                                $helpText = $field->help_text ?? '';
                                                                $errorClass = $errors->has($field->key) ? 'is-invalid' : '';
                                                            @endphp
                                                            
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label fw-semibold">
                                                                    {{ $field->label }}
                                                                    @if($required)
                                                                        <span class="text-danger">*</span>
                                                                    @endif
                                                                </label>

                                                                @switch($field->type)
                                                                    @case('select')
                                                                        <select class="form-select {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                                name="{{ $field->key }}{{ isset($field->is_multiple) && $field->is_multiple ? '[]' : '' }}" 
                                                                                {{ isset($field->is_multiple) && $field->is_multiple ? 'multiple' : '' }} 
                                                                                {{ $required }}>
                                                                            <option value="">Select {{ $field->label }}</option>
                                                                             @if(isset($field->options) && is_array($field->options))
                                                                               @foreach($field->options as $optionData)
                                                                                   @php
                                                                                       // Handle both old format (string) and new format (object)
                                                                                       if(is_string($optionData)) {
                                                                                           $optionValue = $optionData;
                                                                                           $optionLabel = $optionData;
                                                                                       } else {
                                                                                           $optionValue = $optionData['value'] ?? '';
                                                                                           $optionLabel = $optionData['label'] ?? $optionValue;
                                                                                       }
                                                                                       
                                                                                       $selected = '';
                                                                                       
                                                                                       if(isset($field->is_multiple) && $field->is_multiple && is_array($value)) {
                                                                                           $selected = in_array($optionValue, $value) ? 'selected' : '';
                                                                                       } else {
                                                                                           $selected = ($value == $optionValue) ? 'selected' : '';
                                                                                       }
                                                                                   @endphp
                                                                                   <option value="{{ $optionValue }}" {{ $selected }}>
                                                                                       {{ $optionLabel }}
                                                                                   </option>
                                                                               @endforeach
                                                                           @endif
                            </select>
                                                                        @break

                                                                       @case('checkbox')
                                                                         <div class="rounded p-3">
                                                                               @if(isset($field->options) && is_array($field->options))
                                                                                 @foreach($field->options as $index => $optionData)
                                                                                     @php
                                                                                         // Handle both old format (string) and new format (object)
                                                                                         if(is_string($optionData)) {
                                                                                             $optionValue = $optionData;
                                                                                             $optionLabel = $optionData;
                                                                                         } else {
                                                                                             $optionValue = $optionData['value'] ?? '';
                                                                                             $optionLabel = $optionData['label'] ?? $optionValue;
                                                                                         }
                                                                                         
                                                                                         $checked = '';
                                                                                         
                                                                                         if(is_array($value)) {
                                                                                             $checked = in_array($optionValue, $value) ? 'checked' : '';
                                                                                         } else {
                                                                                             $checked = ($value == $optionValue) ? 'checked' : '';
                                                                                         }
                                                                                     @endphp
                                                                                     <div class="form-check">
                                                                                         <input type="checkbox" 
                                                                                                class="form-check-input {{ $errorClass }}" 
                                                                                                name="{{ $field->key }}[]" 
                                                                                                value="{{ $optionValue }}" 
                                                                                                {{ $checked }}>
                                                                                         <label class="form-check-label">
                                                                                             {{ $optionLabel }}
                                                                                         </label>
                                                                                     </div>
                                                                                 @endforeach
                                                                             @endif
                        </div>
                                                                        @break

                                                                       @case('radio')
                                                                         <div class="rounded p-3">
                                                                               @if(isset($field->options) && is_array($field->options))
                                                                                 @foreach($field->options as $index => $optionData)
                                                                                     @php
                                                                                         // Handle both old format (string) and new format (object)
                                                                                         if(is_string($optionData)) {
                                                                                             $optionValue = $optionData;
                                                                                             $optionLabel = $optionData;
                                                                                         } else {
                                                                                             $optionValue = $optionData['value'] ?? '';
                                                                                             $optionLabel = $optionData['label'] ?? $optionValue;
                                                                                         }
                                                                                         
                                                                                         $checked = ($value == $optionValue) ? 'checked' : '';
                                                                                     @endphp
                                                                                     <div class="form-check">
                                                                                         <input type="radio" 
                                                                                                class="form-check-input {{ $errorClass }}" 
                                                                                                name="{{ $field->key }}" 
                                                                                                value="{{ $optionValue }}" 
                                                                                                {{ $checked }} 
                                                                                                {{ $required }}>
                                                                                         <label class="form-check-label">
                                                                                             {{ $optionLabel }}
                                                                                         </label>
                                                                                     </div>
                                                                                 @endforeach
                                                                             @endif
                        </div>
                                                                        @break
                                                                @endswitch

                                                                @if($helpText)
                                                                    <div class="form-text text-muted small">{{ $helpText }}</div>
                                                                @endif

                                                                @error($field->key)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                        </div>
                                                        @endforeach
                                                    @endif
                                                    
                                                    <!-- File Fields (col-md-6) -->
                                                    @if($fileFields->count() > 0)
                                                        @foreach($fileFields as $field)
                                                            @php
                                                                $value = (isset($customFieldValues) && is_array($customFieldValues) && isset($customFieldValues[$field->key])) ? $customFieldValues[$field->key] : ($field->default_value ?? '');
                                                                $required = isset($field->validation['required']) && $field->validation['required'] ? 'required' : '';
                                                                $placeholder = $field->placeholder ?? '';
                                                                $helpText = $field->help_text ?? '';
                                                                $errorClass = $errors->has($field->key) ? 'is-invalid' : '';
                                                            @endphp
                                                            
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label fw-semibold">
                                                                    {{ $field->label }}
                                                                    @if($required)
                                                                        <span class="text-danger">*</span>
                                                                    @endif
                                                                </label>

                                                                @php
                                                                    $accept = '';
                                                                    if(isset($field->validation['allowed_types']) && is_array($field->validation['allowed_types'])) {
                                                                        $accept = '.' . implode(',.', $field->validation['allowed_types']);
                                                                    }
                                                                @endphp
                                                                <input type="file" 
                                                                       class="form-control {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                       name="{{ $field->key }}" 
                                                                       accept="{{ $accept }}" 
                                                                       {{ $required }}>
                                                                @if($value)
                                                                    <small class="text-muted">Current file: {{ basename($value) }}</small>
                                                                @endif

                                                                @if($helpText)
                                                                    <div class="form-text text-muted small">{{ $helpText }}</div>
                                                                @endif

                                                                @error($field->key)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                @php $tpl = old('template', $cms->template ?? ''); @endphp
                                                @if(strtolower($groupName) === 'features' && ($tpl === 'about' || $tpl === ''))
                                                    @php
                                                        $featuresRaw = (isset($customFieldValues) && is_array($customFieldValues) && isset($customFieldValues['features'])) ? $customFieldValues['features'] : null;
                                                        $features = [];
                                                        if (is_string($featuresRaw) && strlen($featuresRaw)) {
                                                            $decoded = json_decode($featuresRaw, true);
                                                            $features = is_array($decoded) ? $decoded : [];
                                                        }
                                                        if (!count($features)) {
                                                            $features = [
                                                                ['icon' => 'frontend/img/featureIcons/1/1.svg','title' => 'Best Price Guarantee','desc' => ''],
                                                                ['icon' => 'frontend/img/featureIcons/1/2.svg','title' => 'Easy & Quick Booking','desc' => ''],
                                                                ['icon' => 'frontend/img/featureIcons/1/3.svg','title' => 'Customer Care 24/7','desc' => ''],
                                                            ];
                                                        }
                                                    @endphp
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="fw-semibold">Why Choose Us Items</div>
                                                                <button type="button" class="btn btn-sm btn-primary" onclick="addFeatureItem()">Add Item</button>
                                                            </div>
                                                        </div>
                                                        <div id="featuresRepeater" class="col-12">
                                                            @foreach($features as $i => $f)
                                                                <div class="border rounded p-3 mb-3 feature-item">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-4">
                                                                            <label class="form-label fw-semibold">Image</label>
                                                                            <input type="hidden" name="features_existing_icons[]" value="{{ $f['icon'] ?? '' }}">
                                                                            <input type="file" name="features_files[]" class="form-control" accept="image/*">
                                                                            @if(isset($cms) && !empty($f['icon']))
                                                                                <div class="current-image-preview mt-2">
                                                                                    <div class="image-preview-wrapper">
                                                                                        <img src="{{ asset($f['icon']) }}" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                                                                                        <div class="image-actions">
                                                                                            <button type="button" class="btn btn-sm btn-outline-primary" data-url="{{ asset($f['icon']) }}" onclick="previewImage(this.dataset.url)">
                                                                                                <i class="ti tabler-eye"></i> Preview
                                                                                            </button>
                                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCustomFieldImage('features', '{{ $cms->id }}', '{{ $i }}')">
                                                                                                <i class="ti tabler-trash"></i> Delete
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label fw-semibold">Title</label>
                                                                            <input type="text" name="features_titles[]" class="form-control" value="{{ $f['title'] ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label fw-semibold">Description</label>
                                                                            <input type="text" name="features_descs[]" class="form-control" value="{{ $f['desc'] ?? '' }}">
                                                                        </div>
                                                                        <div class="col-12 mt-2">
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRepeaterItem(this)">Remove</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(strtolower($groupName) === 'stats' && ($tpl === 'about' || $tpl === ''))
                                                    @php
                                                        $countersRaw = (isset($customFieldValues) && is_array($customFieldValues) && isset($customFieldValues['counters'])) ? $customFieldValues['counters'] : null;
                                                        $counters = [];
                                                        if (is_string($countersRaw) && strlen($countersRaw)) {
                                                            $decodedC = json_decode($countersRaw, true);
                                                            $counters = is_array($decodedC) ? $decodedC : [];
                                                        }
                                                        if (!count($counters)) {
                                                            $counters = [
                                                                ['value' => '4,958', 'label' => 'Destinations'],
                                                                ['value' => '2,869', 'label' => 'Total Properties'],
                                                                ['value' => '2M', 'label' => 'Happy customers'],
                                                                ['value' => '574,974', 'label' => 'Our Volunteers'],
                                                            ];
                                                        }
                                                    @endphp
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="fw-semibold">Counters</div>
                                                                <button type="button" class="btn btn-sm btn-primary" onclick="addCounterItem()">Add Counter</button>
                                                            </div>
                                                        </div>
                                                        <div id="countersRepeater" class="col-12">
                                                            @foreach($counters as $i => $c)
                                                                <div class="border rounded p-3 mb-3 counter-item">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold">Counter Value</label>
                                                                            <input type="text" name="counters_values[]" class="form-control" value="{{ $c['value'] ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="form-label fw-semibold">Title</label>
                                                                            <input type="text" name="counters_labels[]" class="form-control" value="{{ $c['label'] ?? '' }}">
                                                                        </div>
                                                                        <div class="col-12 mt-2">
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRepeaterItem(this)">Remove</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(strtolower($groupName) === 'team' && ($tpl === 'about' || $tpl === ''))
                                                    @php
                                                        $teamRaw = (isset($customFieldValues) && is_array($customFieldValues) && isset($customFieldValues['team_members'])) ? $customFieldValues['team_members'] : null;
                                                        $teamMembers = [];
                                                        if (is_string($teamRaw) && strlen($teamRaw)) {
                                                            $decodedT = json_decode($teamRaw, true);
                                                            $teamMembers = is_array($decodedT) ? $decodedT : [];
                                                        }
                                                        if (!count($teamMembers)) {
                                                            $teamMembers = [
                                                                ['image' => 'frontend/img/team/1.png', 'name' => 'Cody Fisher', 'role' => 'Medical Assistant'],
                                                                ['image' => 'frontend/img/team/2.png', 'name' => 'Dianne Russell', 'role' => 'Web Designer'],
                                                                ['image' => 'frontend/img/team/3.png', 'name' => 'Jerome Bell', 'role' => 'Marketing Coordinator'],
                                                                ['image' => 'frontend/img/team/4.png', 'name' => 'Theresa Webb', 'role' => 'Nursing Assistant'],
                                                                ['image' => 'frontend/img/team/5.png', 'name' => 'Cameron Williamson', 'role' => 'Dog Trainer'],
                                                            ];
                                                        }
                                                    @endphp
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="fw-semibold">Team Members</div>
                                                                <button type="button" class="btn btn-sm btn-primary" onclick="addTeamItem()">Add Member</button>
                                                            </div>
                                                        </div>
                                                        <div id="teamRepeater" class="col-12">
                                                            @foreach($teamMembers as $i => $m)
                                                                <div class="border rounded p-3 mb-3 team-item">
                                                                    <div class="row g-3">
                                                                        <div class="col-md-3">
                                                                            <label class="form-label fw-semibold">Image</label>
                                                                            <input type="hidden" name="team_members_existing_images[]" value="{{ $m['image'] ?? '' }}">
                                                                            <input type="file" name="team_members_files[]" class="form-control" accept="image/*">
                                                                            @if(isset($cms) && !empty($m['image']))
                                                                                <div class="current-image-preview mt-2">
                                                                                    <div class="image-preview-wrapper">
                                                                                        <img src="{{ asset($m['image']) }}" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                                                                                        <div class="image-actions">
                                                                                            <button type="button" class="btn btn-sm btn-outline-primary" data-url="{{ asset($m['image']) }}" onclick="previewImage(this.dataset.url)">
                                                                                                <i class="ti tabler-eye"></i> Preview
                                                                                            </button>
                                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCustomFieldImage('team_members', '{{ $cms->id }}', '{{ $i }}')">
                                                                                                <i class="ti tabler-trash"></i> Delete
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <label class="form-label fw-semibold">Name</label>
                                                                            <input type="text" name="team_members_names[]" class="form-control" value="{{ $m['name'] ?? '' }}">
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label fw-semibold">Occupation</label>
                                                                            <input type="text" name="team_members_roles[]" class="form-control" value="{{ $m['role'] ?? '' }}">
                                                                        </div>
                                                                        <div class="col-12 mt-2">
                                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRepeaterItem(this)">Remove</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Textarea Fields (Full Width) -->
                                                @if($textareaFields->count() > 0)
                                                    @foreach($textareaFields as $field)
                                                        @php
                                                            $value = (isset($customFieldValues) && is_array($customFieldValues) && isset($customFieldValues[$field->key])) ? $customFieldValues[$field->key] : ($field->default_value ?? '');
                                                            // Decode HTML entities for editor fields
                                                            if ($field->type === 'editor' && !empty($value)) {
                                                                $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                                            }
                                                            $required = isset($field->validation['required']) && $field->validation['required'] ? 'required' : '';
                                                            $placeholder = $field->placeholder ?? '';
                                                            $helpText = $field->help_text ?? '';
                                                            $errorClass = $errors->has($field->key) ? 'is-invalid' : '';
                                                        @endphp
                                                        
                                                        <div class="row">
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label fw-semibold">
                                                                    {{ $field->label }}
                                                                    @if($required)
                                                                        <span class="text-danger">*</span>
                                                                    @endif
                                    </label>

                                                                                                <textarea class="form-control ckeditor {{ $field->class ?? '' }} {{ $errorClass }}" 
                                                                         name="{{ $field->key }}" 
                                                                         id="{{ $field->key }}_editor"
                                                                         placeholder="{{ $placeholder }}" 
                                                                         rows="4" 
                                                                         {{ $required }}>{{ old($field->key, $value) }}</textarea>

                                                                @if($helpText)
                                                                    <div class="form-text text-muted small">{{ $helpText }}</div>
                                                                @endif

                                                                @error($field->key)
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                </div>
                            </div>
                                                    @endforeach
                        @endif
                                            </div>
                                        </div>
                                    </div>
                    </div>
                            @endforeach
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti tabler-device-floppy me-1"></i> {{ isset($cms) ? 'Update' : 'Create' }} Page
                                </button>
                                <a href="{{ route(CustomHelper::getAdminRouteName() . '.cms.index') }}" class="btn btn-secondary">
                                    <i class="ti tabler-x me-1"></i> Cancel
                                </a>
                            </div>
                    </div>
                </form>
                </div>
            </div>
            </div>
        </div>
    </div>

    @slot('footerBlock')
<style>
/* Image Upload Styles */
.image-upload-container {
    position: relative;
}

.current-image-preview {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 15px;
    background: #f9fafb;
}

.image-preview-wrapper {
    position: relative;
    display: inline-block;
}

.image-preview-wrapper img {
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.image-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    gap: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-preview-wrapper:hover .image-actions {
    opacity: 1;
}

.image-actions .btn {
    padding: 4px 8px;
    font-size: 0.75rem;
    border-radius: 4px;
}

/* Image Preview Modal */
.image-preview-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
}

.image-preview-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 90%;
    max-height: 90%;
}

.image-preview-content img {
    max-width: 100%;
    max-height: 100%;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.image-preview-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: #fff;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    background: none;
    border: none;
}

.image-preview-close:hover {
    color: #ddd;
}

/* File Preview Styles */
.file-preview {
    display: flex;
    align-items: center;
    padding: 10px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #6c757d;
}

.file-preview i {
    font-size: 1.2rem;
    margin-right: 8px;
    color: var(--theme-color);
}
</style>

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="image-preview-modal">
    <div class="image-preview-content">
        <button class="image-preview-close" onclick="closeImagePreview()">&times;</button>
        <img id="previewImage" src="" alt="Image Preview">
    </div>
</div>

<script>
$(function() {
    // Initialize CKEditor for page description
    CKEDITOR.replace('description', {
        filebrowserImageUploadUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_upload", ["_token" => csrf_token()]) }}',
        filebrowserUploadMethod: 'form',
        filebrowserBrowseUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_browse") }}',
        filebrowserImageBrowseUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_browse") }}',
        extraPlugins: 'filebrowser'
    });
    var uploadJsonUrl = "{{ route(CustomHelper::getAdminRouteName() . '.ck_upload_json') }}";
    var overlayHtml = '<div id="imageLibraryOverlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:none;"><div style="max-width:900px;margin:40px auto;background:#fff;border-radius:6px;overflow:hidden"><div style="padding:10px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center"><strong>Image Library</strong><button type="button" id="closeImageLibrary" class="btn btn-sm btn-outline-secondary">Close</button></div><iframe id="imageLibraryFrame" src="" style="width:100%;height:520px;border:0"></iframe></div></div>';
    if(!document.getElementById('imageLibraryOverlay')){ document.body.insertAdjacentHTML('beforeend', overlayHtml); }
    window.insertImageFromLibrary = function(url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.description){ CKEDITOR.instances.description.insertHtml('<img src="'+url+'" />'); } var ov=document.getElementById('imageLibraryOverlay'); if(ov) ov.style.display='none'; };
    var toolsHtml = '<div class="mt-2"><div class="d-flex gap-2 align-items-center"><input type="file" id="descriptionQuickUpload" class="form-control form-control-sm" accept="image/*" style="max-width:260px"><button type="button" class="btn btn-sm btn-outline-primary" id="descriptionOpenLibrary">Choose From Library</button></div></div>';
    $('#description').after(toolsHtml);
    $(document).on('change','#descriptionQuickUpload',function(){ var f=this.files && this.files[0]; if(!f) return; var fd=new FormData(); fd.append('upload', f); fd.append('_token','{{ csrf_token() }}'); fetch(uploadJsonUrl,{method:'POST', body:fd}).then(function(r){return r.json();}).then(function(j){ if(j && j.success && j.url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.description){ CKEDITOR.instances.description.insertHtml('<img src="'+j.url+'" />'); } } }); });
    $(document).on('click','#descriptionOpenLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); var fr=document.getElementById('imageLibraryFrame'); if(ov && fr){ fr.src = '{{ route(CustomHelper::getAdminRouteName() . ".media.index", ["popup" => 1]) }}'; ov.style.display='block'; } });
    $(document).on('click','#closeImageLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); if(ov){ ov.style.display='none'; } });
    $(document).on('click','#imageLibraryOverlay',function(e){ if(e.target && e.target.id==='imageLibraryOverlay'){ this.style.display='none'; } });

    // Initialize CKEditor for custom fields with editor type
    document.querySelectorAll('.ckeditor').forEach(function(el) {
        if (el.id !== 'description') { // Skip the main description field as it's already initialized
                CKEDITOR.replace(el, {
                extraPlugins: 'filebrowser'
                });
            }
        });
});

// Image Upload Preview
document.addEventListener('DOMContentLoaded', function() {
    // Handle file input changes
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewId = this.getAttribute('data-preview');
            const previewContainer = document.getElementById(previewId);
            
            if (file && previewContainer) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <div class="image-preview-wrapper">
                            <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                            <div class="image-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewImage('${e.target.result}')">
                                    <i class="ti tabler-eye"></i> Preview
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFilePreview('${previewId}', '${input.id}')">
                                    <i class="ti tabler-x"></i> Remove
                                </button>
                            </div>
                        </div>
                    `;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

// Preview image in modal
function previewImage(imageSrc) {
    const modal = document.getElementById('imagePreviewModal');
    const img = document.getElementById('previewImage');
    img.src = imageSrc;
    modal.style.display = 'block';
}

// Close image preview modal
function closeImagePreview() {
    const modal = document.getElementById('imagePreviewModal');
    modal.style.display = 'none';
}

// Remove file preview
function removeFilePreview(previewId, inputId) {
    const previewContainer = document.getElementById(previewId);
    const fileInput = document.getElementById(inputId);
    
    if (previewContainer) {
        previewContainer.innerHTML = '';
        previewContainer.style.display = 'none';
    }
    
    if (fileInput) {
        fileInput.value = '';
    }
}

// Delete image from server
function deleteImage(fieldName, cmsId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This image will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
            confirmButton: 'btn btn-danger mx-1',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Instant hide for better UX
            const previewId = fieldName === 'banner' ? 'banner-current-preview' : 'page-image-current-preview';
            const previewContainer = document.getElementById(previewId);
            if (previewContainer) {
                previewContainer.style.display = 'none';
            }

            const token = document.querySelector('meta[name="_token"]').getAttribute('content');
            const adminRouteName = '{{ CustomHelper::getAdminRouteName() }}';
            const deleteUrl = `/${adminRouteName}/cms/${cmsId}/delete-image`;
            fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ field_name: fieldName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    if (previewContainer) {
                        previewContainer.innerHTML = '';
                    }
                    toastr.success(data.message);
                } else {
                    if (previewContainer) previewContainer.style.display = 'block'; // Revert if failed
                    toastr.error(data.message || 'Error deleting image');
                }
            })
            .catch(() => {
                if (previewContainer) previewContainer.style.display = 'block'; // Revert if failed
                toastr.error('Error deleting image');
            });
        }
    });
}

// Clear media preview (for newly selected images)
window.clearMediaPreview = function(fieldId) {
    document.getElementById(fieldId).value = ''; // Clear hidden input
    // Also clear file input if it exists
    const fileInput = document.getElementById(fieldId.replace('_media_path', ''));
    if (fileInput) fileInput.value = '';
    
    const preview = document.getElementById(fieldId + '_preview');
    const removeBtn = document.getElementById(fieldId + '_remove');
    
    if (preview) {
        preview.src = '';
        preview.style.display = 'none';
    }
    if (removeBtn) {
        removeBtn.style.display = 'none';
    }
};

// Observer to show remove button when image becomes visible
function setupPreviewObserver(fieldId) {
    const preview = document.getElementById(fieldId + '_preview');
    const removeBtn = document.getElementById(fieldId + '_remove');
    if (!preview || !removeBtn) return;

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && (mutation.attributeName === 'style' || mutation.attributeName === 'src')) {
                if (preview.style.display !== 'none' && preview.getAttribute('src')) {
                    removeBtn.style.display = 'block';
                } else {
                    removeBtn.style.display = 'none';
                }
            }
        });
    });

    observer.observe(preview, { attributes: true });
}

document.addEventListener('DOMContentLoaded', function() {
    setupPreviewObserver('banner_media_path');
    setupPreviewObserver('page_image_media_path');
});


// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('imagePreviewModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

    // Delete custom field file
    function deleteCustomFieldFile(fieldKey, module, moduleId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This file will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-danger mx-1',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const token = document.querySelector('meta[name="_token"]').getAttribute('content');
                const adminRouteName = '{{ CustomHelper::getAdminRouteName() }}';
                const deleteUrl = `/${adminRouteName}/custom-fields/delete-file`;
                fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ field_key: fieldKey, module: module, module_id: moduleId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        const previewContainer = document.getElementById(fieldKey + '-preview');
                        if (previewContainer) {
                            previewContainer.innerHTML = '';
                            previewContainer.style.display = 'none';
                        }
                        toastr.success(data.message);
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, buttonsStyling: false, customClass: { confirmButton: 'btn btn-success' } });
                    } else {
                        toastr.error(data.message || 'Error deleting file');
                    }
                })
                .catch(() => {
                    toastr.error('Error deleting file');
                });
            }
        });
    }

    function deleteCustomFieldImage(fieldKey, cmsId, index) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This image will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-danger mx-1',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const token = document.querySelector('meta[name="_token"]').getAttribute('content');
                const adminRouteName = '{{ CustomHelper::getAdminRouteName() }}';
                const deleteUrl = `/${adminRouteName}/cms/${cmsId}/delete-custom-field-image`;
                fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ field_key: fieldKey, index: Number(index) })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        if (fieldKey === 'features') {
                            const items = document.querySelectorAll('#featuresRepeater .feature-item');
                            if (items[index]) {
                                const preview = items[index].querySelector('.current-image-preview');
                                if (preview) preview.remove();
                                const hidden = items[index].querySelector('input[name="features_existing_icons[]"]');
                                if (hidden) hidden.value = '';
                            }
                        } else if (fieldKey === 'team_members') {
                            const items = document.querySelectorAll('#teamRepeater .team-item');
                            if (items[index]) {
                                const preview = items[index].querySelector('.current-image-preview');
                                if (preview) preview.remove();
                                const hidden = items[index].querySelector('input[name="team_members_existing_images[]"]');
                                if (hidden) hidden.value = '';
                            }
                        }
                        toastr.success(data.message);
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, buttonsStyling: false, customClass: { confirmButton: 'btn btn-success' } });
                    } else {
                        toastr.error(data.message || 'Error deleting image');
                    }
                })
                .catch(() => {
                    toastr.error('Error deleting image');
                });
            }
        });
    }

// Auto-generate slug from title
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.querySelector('input[name="slug"]');
    
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            // Only auto-generate if slug field is empty or user hasn't manually edited it
            if (slugInput.value === '' || slugInput.dataset.autoGenerated === 'true') {
                const slug = generateSlug(this.value);
                slugInput.value = slug;
                slugInput.dataset.autoGenerated = 'true';
            }
        });
        
        // Mark as manually edited when user types in slug field
        slugInput.addEventListener('input', function() {
            this.dataset.autoGenerated = 'false';
        });
    }
    
    // Add form submission debugging
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted');
            console.log('Form action:', this.action);
            console.log('Form method:', this.method);
            
            // Check if required fields are filled
            const title = document.querySelector('input[name="title"]').value;
            const template = document.querySelector('select[name="template"]').value;
            
            if (!title || !template) {
                e.preventDefault();
                alert('Please fill in all required fields (Title and Template)');
                return false;
            }
        });
    }
});

function generateSlug(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '') // Remove special characters except spaces and hyphens
        .replace(/[\s-]+/g, '-') // Replace multiple spaces/hyphens with single hyphen
        .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
}
function removeRepeaterItem(btn){ var item = btn.closest('.feature-item, .counter-item, .team-item'); if(item){ item.remove(); } }
function addFeatureItem(){ var cont = document.getElementById('featuresRepeater'); if(!cont) return; var items = cont.querySelectorAll('.feature-item'); if(items.length >= 3) return; var html = '<div class="border rounded p-3 mb-3 feature-item"><div class="row g-3"><div class="col-md-4"><label class="form-label fw-semibold">Image</label><input type="hidden" name="features_existing_icons[]" value=""><input type="file" name="features_files[]" class="form-control" accept="image/*"></div><div class="col-md-4"><label class="form-label fw-semibold">Title</label><input type="text" name="features_titles[]" class="form-control" value=""></div><div class="col-md-4"><label class="form-label fw-semibold">Description</label><input type="text" name="features_descs[]" class="form-control" value=""></div><div class="col-12 mt-2"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRepeaterItem(this)">Remove</button></div></div></div>'; cont.insertAdjacentHTML('beforeend', html); }
function addCounterItem(){ var cont = document.getElementById('countersRepeater'); if(!cont) return; var items = cont.querySelectorAll('.counter-item'); if(items.length >= 4) return; var html = '<div class="border rounded p-3 mb-3 counter-item"><div class="row g-3"><div class="col-md-6"><label class="form-label fw-semibold">Counter Value</label><input type="text" name="counters_values[]" class="form-control" value=""></div><div class="col-md-6"><label class="form-label fw-semibold">Title</label><input type="text" name="counters_labels[]" class="form-control" value=""></div><div class="col-12 mt-2"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRepeaterItem(this)">Remove</button></div></div></div>'; cont.insertAdjacentHTML('beforeend', html); }
function addTeamItem(){ var cont = document.getElementById('teamRepeater'); if(!cont) return; var html = '<div class="border rounded p-3 mb-3 team-item"><div class="row g-3"><div class="col-md-3"><label class="form-label fw-semibold">Image</label><input type="hidden" name="team_members_existing_images[]" value=""><input type="file" name="team_members_files[]" class="form-control" accept="image/*"></div><div class="col-md-5"><label class="form-label fw-semibold">Name</label><input type="text" name="team_members_names[]" class="form-control" value=""></div><div class="col-md-4"><label class="form-label fw-semibold">Occupation</label><input type="text" name="team_members_roles[]" class="form-control" value=""></div><div class="col-12 mt-2"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRepeaterItem(this)">Remove</button></div></div></div>'; cont.insertAdjacentHTML('beforeend', html); }
    </script>
    @endslot
@endcomponent
