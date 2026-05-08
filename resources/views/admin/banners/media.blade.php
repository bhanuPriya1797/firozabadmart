@component('admin.layouts.main')

@slot('title')
    Manage Banner Media - {{ $banner->title }} - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="icon-base ti tabler-photo me-2"></i>
                        Manage Media for: <strong>{{ $banner->title }}</strong>
                    </h4>
                    <div>
                        <a href="{{ route($routeName . '.banners.edit', $banner->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="icon-base ti tabler-edit me-1"></i>
                            Edit Banner
                        </a>
                        <a href="{{ route($routeName . '.banners.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="icon-base ti tabler-arrow-left me-1"></i>
                            Back to Banners
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Banner Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    @if($banner->type == 1)
                                        <span class="badge bg-primary me-2">Image Banner</span>
                                    @else
                                        <span class="badge bg-success me-2">Video Banner</span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $banner->title }}</h6>
                                    <small class="text-muted">Created: {{ $banner->created_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="badge bg-{{ $banner->status == 1 ? 'success' : 'danger' }}">
                                {{ $banner->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Image Requirements Alert -->
                    @if($banner->type == 1)
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">
                            <i class="icon-base ti tabler-info-circle me-2"></i>
                            Image Requirements for Better Frontend Display
                        </h6>
                        <ul class="mb-0">
                            <li><strong>Recommended Resolution:</strong> 1920x1080 pixels (16:9 aspect ratio)</li>
                            <li><strong>Minimum Resolution:</strong> 1200x675 pixels</li>
                            <li><strong>File Formats:</strong> JPG, PNG, GIF</li>
                            <li><strong>Maximum File Size:</strong> 5MB per image</li>
                            <li><strong>Color Mode:</strong> RGB (for web display)</li>
                            <li><strong>Quality:</strong> High quality images for crisp display on all devices</li>
                        </ul>
                    </div>
                    @endif

                    <!-- Media Upload Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="icon-base ti tabler-upload me-2"></i>
                                        Upload New Media
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($banner->type == 1)
                                        <div class="text-center p-4 border border-dashed rounded mb-3">
                                            <div class="mb-3">
                                                <i class="icon-base ti tabler-photo-plus icon-2xl text-primary"></i>
                                            </div>
                                            <h5>Select Image from Media Library</h5>
                                            <p class="text-muted">Choose an existing image from your media library folders or upload a new one.</p>
                                            
                                            <button class="btn btn-primary" type="button" onclick="openMediaManager('media-library-selection')">
                                                <i class="icon-base ti tabler-photo me-1"></i> Open Media Manager
                                            </button>
                                            
                                            <!-- Hidden input to store selected file path -->
                                            <input type="hidden" id="media-library-selection" name="media_library_selection">
                                            
                                            <div id="library-upload-status" class="mt-3"></div>
                                        </div>
                                    @else
                                        <!-- Video Upload -->
                                        <ul class="nav nav-tabs" id="videoUploadTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="video-library-tab" data-bs-toggle="tab" data-bs-target="#video-library" type="button" role="tab" aria-controls="video-library" aria-selected="true">From Library</button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="video-upload-tab" data-bs-toggle="tab" data-bs-target="#video-upload" type="button" role="tab" aria-controls="video-upload" aria-selected="false">Upload New</button>
                                            </li>
                                        </ul>
                                        <div class="tab-content pt-3" id="videoUploadTabsContent">
                                            <!-- Library Tab -->
                                            <div class="tab-pane fade show active" id="video-library" role="tabpanel" aria-labelledby="video-library-tab">
                                                <div class="text-center p-4 border border-dashed rounded mb-3">
                                                    <div class="mb-3">
                                                        <i class="icon-base ti tabler-video icon-2xl text-primary"></i>
                                                    </div>
                                                    <h5>Select Video from Media Library</h5>
                                                    <p class="text-muted">Choose an existing video from your media library folders.</p>
                                                    
                                                    <button class="btn btn-primary" type="button" onclick="openMediaManager('video-library-selection')">
                                                        <i class="icon-base ti tabler-photo me-1"></i> Open Media Manager
                                                    </button>
                                                    
                                                    <!-- Hidden input to store selected file path -->
                                                    <input type="hidden" id="video-library-selection" name="video_library_selection">
                                                    
                                                    <div id="video-library-upload-status" class="mt-3"></div>
                                                </div>
                                            </div>
                                            
                                            <!-- Upload Tab -->
                                            <div class="tab-pane fade" id="video-upload" role="tabpanel" aria-labelledby="video-upload-tab">
                                                <div class="mb-3">
                                                    <label class="form-label">Upload Video</label>
                                                    <div class="dropzone" id="video-dropzone">
                                                        <div class="dz-message">
                                                            <i class="icon-base ti tabler-video icon-lg"></i>
                                                            <h5>Drop video here or click to upload</h5>
                                                            <span class="text-muted">Supports: MP4, AVI, MOV (Max: 100MB)</span>
                                                        </div>
                                                    </div>
                                                    <div id="video-upload-progress" class="mt-3" style="display: none;">
                                                        <div class="progress">
                                                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                                        </div>
                                                        <small class="text-muted">Uploading...</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Media List -->
                    <div class="row">
                        <div class="col-12">
                            @if($banner->type == 1)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="icon-base ti tabler-list me-2"></i>
                                        Manage Image Items ({{ $banner->images->count() }} items)
                                    </h5>
                                    <div>
                                        <button type="button" class="btn btn-success btn-sm" id="save-all-media">
                                            <i class="icon-base ti tabler-device-floppy me-1"></i>
                                            Save All Changes
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm" id="update-order">
                                            <i class="icon-base ti tabler-arrows-sort me-1"></i>
                                            Update Order
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="icon-base ti tabler-video me-2"></i>
                                        Manage Video Content
                                    </h5>
                                </div>
                            @endif
                            
                            @if($banner->type == 2)
                                <!-- Video Information Section -->
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="card-title mb-0">
                                            <i class="icon-base ti tabler-video me-2"></i>
                                            Current Video Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($banner->video)
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="text-center">
                                                        @if($banner->video_type == 1)
                                                            @php
                                                                $videoSrc = $banner->video && str_starts_with($banner->video, 'media/')
                                                                    ? asset('storage/' . $banner->video)
                                                                    : asset('storage/banners/' . $banner->video);
                                                            @endphp
                                                            <video width="100%" height="200" controls class="rounded">
                                                                <source src="{{ $videoSrc }}" type="video/mp4">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                        @else
                                                            <!-- Embedded Video Preview -->
                                                            <div class="ratio ratio-16x9">
                                                                {!! $banner->video_embed !!}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Video Type:</label>
                                                            <p class="text-muted">
                                                                @if($banner->video_type == 1)
                                                                    <span class="badge bg-primary">Uploaded File</span>
                                                                @else
                                                                    <span class="badge bg-info">Embedded URL</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Video Source:</label>
                                                            <p class="text-muted">
                                                                @if($banner->video_type == 1)
                                                                    {{ $banner->video }}
                                                                @else
                                                                    {{ Str::limit($banner->video_embed, 50) }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <label class="form-label fw-bold">Video Title:</label>
                                                            <input type="text" class="form-control" id="video-title" value="{{ $banner->title }}" placeholder="Video Title">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Button 1 Text:</label>
                                                            <input type="text" class="form-control" id="video-btn1-text" value="{{ $banner->link_text_1 ?? '' }}" placeholder="Button 1 Text">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Button 1 Link:</label>
                                                            <input type="url" class="form-control" id="video-btn1-link" value="{{ $banner->link_1 ?? '' }}" placeholder="Button 1 URL">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Button 2 Text:</label>
                                                            <input type="text" class="form-control" id="video-btn2-text" value="{{ $banner->link_text_2 ?? '' }}" placeholder="Button 2 Text">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold">Button 2 Link:</label>
                                                            <input type="url" class="form-control" id="video-btn2-link" value="{{ $banner->link_2 ?? '' }}" placeholder="Button 2 URL">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <button type="button" class="btn btn-success" id="save-video-info">
                                                                <i class="icon-base ti tabler-device-floppy me-1"></i>
                                                                Save Video Information
                                                            </button>
                                                            @if($banner->video_type == 1)
                                                                <button type="button" class="btn btn-danger" id="delete-video">
                                                                    <i class="icon-base ti tabler-trash me-1"></i>
                                                                    Delete Video
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="icon-base ti tabler-video-off icon-lg text-muted mb-3"></i>
                                                <h6 class="text-muted">No video uploaded yet</h6>
                                                <p class="text-muted">Upload a video using the form above to get started.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($banner->images->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="media-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="80">Order</th>
                                                <th width="120">Preview</th>
                                                <th>Title & Description</th>
                                                <th>Button 1</th>
                                                <th>Button 2</th>
                                                <th width="100">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sortable-media">
                                            @foreach($banner->images->sortBy('sort_order') as $image)
                                            <tr data-id="{{ $image->id }}" class="media-item">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="icon-base ti tabler-grip-vertical me-2 text-muted drag-handle"></i>
                                                        <span class="sort-order">{{ $image->sort_order }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <img src="{{ $image->image_url }}" 
                                                         class="img-thumbnail" 
                                                         style="width: 80px; height: 60px; object-fit: cover;"
                                                         alt="{{ $image->title }}">
                                                </td>
                                                <td>
                                                    <div class="mb-2">
                                                        <input type="text" 
                                                               class="form-control form-control-sm" 
                                                               name="title[{{ $image->id }}]" 
                                                               value="{{ $image->title }}" 
                                                               placeholder="Title">
                                                    </div>
                                                    <div>
                                                        <textarea class="form-control form-control-sm" 
                                                                  name="sub_title[{{ $image->id }}]" 
                                                                  rows="2" 
                                                                  placeholder="Description">{{ $image->sub_title }}</textarea>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-2">
                                                        <input type="text" 
                                                               class="form-control form-control-sm" 
                                                               name="link_text_1[{{ $image->id }}]" 
                                                               value="{{ $image->link_text_1 }}" 
                                                               placeholder="Button Text">
                                                    </div>
                                                    <div>
                                                        <input type="url" 
                                                               class="form-control form-control-sm" 
                                                               name="link_1[{{ $image->id }}]" 
                                                               value="{{ $image->link_1 }}" 
                                                               placeholder="URL">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="mb-2">
                                                        <input type="text" 
                                                               class="form-control form-control-sm" 
                                                               name="link_text_2[{{ $image->id }}]" 
                                                               value="{{ $image->link_text_2 }}" 
                                                               placeholder="Button Text">
                                                    </div>
                                                    <div>
                                                        <input type="url" 
                                                               class="form-control form-control-sm" 
                                                               name="link_2[{{ $image->id }}]" 
                                                               value="{{ $image->link_2 }}" 
                                                               placeholder="URL">
                                                    </div>
                                                </td>
                                                                                                 <td>
                                                     <div class="btn-group-vertical btn-group-sm">
                                                         <button type="button" 
                                                                 class="btn btn-outline-danger btn-delete-media" 
                                                                 data-id="{{ $image->id }}">
                                                             <i class="icon-base ti tabler-trash"></i>
                                                         </button>
                                                     </div>
                                                 </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="icon-base ti tabler-photo-off icon-lg text-muted mb-3"></i>
                                    <h5 class="text-muted">No media items found</h5>
                                    <p class="text-muted">Upload some images or videos to get started.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@slot('footerBlock')
<script src="{{ asset('admin/assets/js/media-selector.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Media Library Selection
            const mediaInput = document.getElementById('media-library-selection');
            if(mediaInput) {
                mediaInput.addEventListener('change', function() {
                    const filePath = this.value;
                    if(!filePath) return;

                    // Show loading state
                    const statusDiv = document.getElementById('library-upload-status');
                    statusDiv.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Importing image...';

                    const formData = new FormData();
                    formData.append('banner_id', '{{ $banner->id }}');
                    formData.append('file_path', filePath);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route($routeName . ".banners.addMediaFromLibrary") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            statusDiv.innerHTML = '<div class="text-success"><i class="ti tabler-check"></i> Image added successfully! Reloading...</div>';
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            statusDiv.innerHTML = '<div class="text-danger"><i class="ti tabler-alert-circle"></i> ' + (data.message || 'Error adding image') + '</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        statusDiv.innerHTML = '<div class="text-danger"><i class="ti tabler-alert-circle"></i> An error occurred.</div>';
                    });
                });
            }

            // Existing Dropzone Logic (if any) or other scripts
        });
    </script>
    <!-- Include jQuery UI for drag and drop -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<!-- Include Dropzone for file uploads -->
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
$(document).ready(function() {
    
    // Initialize drag and drop sorting
    $("#sortable-media").sortable({
        handle: ".drag-handle",
        axis: "y",
        cursor: "move",
        opacity: 0.8,
        update: function(event, ui) {
            // Update sort order numbers
            $('.media-item').each(function(index) {
                $(this).find('.sort-order').text(index + 1);
            });
        }
    });
    
    // Button event handlers
    $('#save-all-media').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        saveAllMedia();
    });
    
    $('#update-order').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        updateMediaOrder();
    });
    
    // Function to save all media
    function saveAllMedia() {
        const mediaData = [];
        
        $('.media-item').each(function() {
            const mediaId = $(this).data('id');
            const row = $(this);
            
            mediaData.push({
                media_id: mediaId,
                title: row.find('input[name="title[' + mediaId + ']"]').val(),
                sub_title: row.find('textarea[name="sub_title[' + mediaId + ']"]').val(),
                link_text_1: row.find('input[name="link_text_1[' + mediaId + ']"]').val(),
                link_1: row.find('input[name="link_1[' + mediaId + ']"]').val(),
                link_text_2: row.find('input[name="link_text_2[' + mediaId + ']"]').val(),
                link_2: row.find('input[name="link_2[' + mediaId + ']"]').val()
            });
        });

        $.ajax({
            url: "{{ route($routeName . '.banners.updateAllMedia') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                banner_id: '{{ $banner->id }}',
                media_data: mediaData
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('All media updated successfully!');
                } else {
                    toastr.error(response.message || 'Update failed!');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Something went wrong! Error: ' + error);
            }
        });
    }
    
    // Function to update media order
    function updateMediaOrder() {
        const orderData = [];
        
        $('.media-item').each(function(index) {
            orderData.push({
                media_id: $(this).data('id'),
                sort_order: index + 1
            });
        });

        $.ajax({
            url: "{{ route($routeName . '.banners.updateMediaOrder') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                banner_id: '{{ $banner->id }}',
                order_data: orderData
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Order updated successfully!');
                } else {
                    toastr.error(response.message || 'Order update failed!');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Something went wrong! Error: ' + error);
            }
        });
    }

         // Initialize Dropzone uploads
     Dropzone.autoDiscover = false;
     
    // Image upload with Dropzone
    @if($banner->type == 1)
    // Destroy existing dropzone if it exists
    if (Dropzone.instances.length > 0) {
        Dropzone.instances.forEach(function(instance) {
            if (instance.element.id === 'image-dropzone') {
                instance.destroy();
            }
        });
    }
    @endif



     // Video upload with Dropzone
    @if($banner->type == 2)
    
    // Video Library Selection Handler
    if(document.getElementById('video-library-selection')) {
        document.getElementById('video-library-selection').addEventListener('change', function() {
            const filePath = this.value;
            if (!filePath) return;

            const statusEl = document.getElementById('video-library-upload-status');
            statusEl.innerHTML = '<span class="text-info"><i class="icon-base ti tabler-loader animate-spin"></i> Adding video to banner...</span>';

            const formData = new FormData();
            formData.append('banner_id', '{{ $banner->id }}');
            formData.append('file_path', filePath);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route($routeName . ".banners.addVideoFromLibrary") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusEl.innerHTML = '<span class="text-success"><i class="icon-base ti tabler-check"></i> ' + data.message + '</span>';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    statusEl.innerHTML = '<span class="text-danger"><i class="icon-base ti tabler-alert-circle"></i> ' + data.message + '</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusEl.innerHTML = '<span class="text-danger"><i class="icon-base ti tabler-alert-circle"></i> An error occurred.</span>';
            });
            
            this.value = '';
        });
    }

    // Destroy existing dropzone if it exists
     if (Dropzone.instances.length > 0) {
         Dropzone.instances.forEach(function(instance) {
             if (instance.element.id === 'video-dropzone') {
                 instance.destroy();
             }
         });
     }
     
     var videoDropzone = new Dropzone("#video-dropzone", {
         url: "{{ route($routeName . '.banners.uploadVideo') }}",
         paramName: "file",
         maxFilesize: 100, // MB
         acceptedFiles: "video/*",
         addRemoveLinks: true,
         clickable: true,
         headers: {
             'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         init: function() {
             this.on("sending", function(file, xhr, formData) {
                 formData.append('banner_id', '{{ $banner->id }}');
                 $('#video-upload-progress').show();
             });
             
             this.on("success", function(file, response) {
                 if (response.success) {
                     toastr.success('Video uploaded successfully!');
                     setTimeout(function() {
                         location.reload();
                     }, 1000);
                 } else {
                     toastr.error(response.message || 'Upload failed!');
                 }
                 $('#video-upload-progress').hide();
             });
             
             this.on("error", function(file, errorMessage) {
                 toastr.error('Upload failed: ' + errorMessage);
                 $('#video-upload-progress').hide();
             });
             
             this.on("addedfile", function(file) {
                 console.log('File added to dropzone:', file.name);
             });
         }
     });
     @endif

    // Delete media item
    $(document).on('click', '.btn-delete-media', function() {
        const mediaId = $(this).data('id');
        
        Swal.fire({
            title: "Are you sure?",
            text: "This will permanently delete this media item!",
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
                    url: "{{ route($routeName . '.banners.deleteImage') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        image_id: mediaId
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Media deleted successfully!');
                            location.reload();
                        } else {
                            toastr.error(response.message || 'Delete failed!');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Something went wrong! Error: ' + error);
                    }
                });
            }
        });
    });

    // Video Information Management
    @if($banner->type == 2)
    $('#save-video-info').on('click', function() {
        const videoData = {
            _token: '{{ csrf_token() }}',
            banner_id: '{{ $banner->id }}',
            title: $('#video-title').val(),
            link_text_1: $('#video-btn1-text').val(),
            link_1: $('#video-btn1-link').val(),
            link_text_2: $('#video-btn2-text').val(),
            link_2: $('#video-btn2-link').val()
        };

        $.ajax({
            url: "{{ route($routeName . '.banners.updateVideoInfo') }}",
            type: 'POST',
            data: videoData,
            success: function(response) {
                if (response.success) {
                    toastr.success('Video information updated successfully!');
                } else {
                    toastr.error(response.message || 'Update failed!');
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Something went wrong! Error: ' + error);
            }
        });
    });

    $('#delete-video').on('click', function() {
        Swal.fire({
            title: "Are you sure?",
            text: "This will permanently delete the video file!",
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
                    url: "{{ route($routeName . '.banners.deleteVideo') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        banner_id: '{{ $banner->id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Video deleted successfully!');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message || 'Delete failed!');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Something went wrong! Error: ' + error);
                    }
                });
            }
        });
    });
    @endif
});
</script>

<style>
.dropzone {
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    background: #f9fafb;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.dropzone:hover {
    border-color: #8e44ad;
    background: #eff6ff;
}

.dropzone .dz-message {
    text-align: center;
    color: #6b7280;
}

.dropzone .dz-message i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #9ca3af;
}

.dropzone .dz-message h5 {
    margin: 0.5rem 0;
    color: #374151;
}

.progress {
    height: 0.5rem;
    border-radius: 0.25rem;
}

.progress-bar {
    background-color: #8e44ad;
}

.media-item {
    cursor: move;
}

.media-item:hover {
    background-color: #f8f9fa;
}

.drag-handle {
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
}

.ui-sortable-helper {
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.ui-sortable-placeholder {
    background: #f3f4f6;
    border: 2px dashed #d1d5db;
    height: 60px;
}
</style>
@endslot

@endcomponent
