@component('admin.layouts.main')

@slot('title')
    {{ isset($banner) ? 'Edit Banner' : 'Add Banner' }} - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ isset($banner) ? 'Edit Banner' : 'Add Banner' }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route($routeName . '.banners.save', $banner ? CustomHelper::encrypt($banner->id) : 0) }}" method="POST" id="banner-form">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Banner Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $banner ? $banner->title : '') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Banner Type <span class="text-danger">*</span></label>
                                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="1" {{ old('type', $banner ? $banner->type : '') == 1 ? 'selected' : '' }}>Image Banner</option>
                                                <option value="2" {{ old('type', $banner ? $banner->type : '') == 2 ? 'selected' : '' }}>Video Banner</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="1" {{ old('status', $banner ? $banner->status : 1) == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ old('status', $banner ? $banner->status : 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Sort Order</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $banner ? $banner->sort_order : 0) }}" min="0">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3" id="video-type-container" style="display: none;">
                                            <label for="video_type" class="form-label">Video Type</label>
                                            <select class="form-select @error('video_type') is-invalid @enderror" id="video_type" name="video_type">
                                                <option value="0">No Video</option>
                                                <option value="1" {{ old('video_type', $banner ? $banner->video_type : 0) == 1 ? 'selected' : '' }}>Upload Video</option>
                                                <option value="2" {{ old('video_type', $banner ? $banner->video_type : 0) == 2 ? 'selected' : '' }}>Embed Video</option>
                                            </select>
                                            @error('video_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Video Embed URL -->
                                <div class="mb-3" id="video-embed-container" style="display: none;">
                                    <label for="video_embed" class="form-label">Video Embed URL</label>
                                    <input type="url" class="form-control @error('video_embed') is-invalid @enderror" id="video_embed" name="video_embed" value="{{ old('video_embed', $banner ? $banner->video_embed : '') }}" placeholder="https://www.youtube.com/embed/...">
                                    @error('video_embed')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Enter the embed URL from YouTube, Vimeo, etc.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Info Card -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="icon-base ti tabler-info-circle me-2"></i>
                                            Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                                                                 <div class="alert alert-info">
                                             <h6 class="alert-heading">Media Management</h6>
                                             <p class="mb-0">After creating this banner, you can manage its media (images/videos) by clicking the "Manage Media" button in the banner listing.</p>
                                         </div>
                                         
                                         <div class="alert alert-warning mt-3">
                                             <h6 class="alert-heading">Important Notes</h6>
                                             <ul class="mb-0">
                                                 <li>For Image Banners: Upload images with recommended resolution 1920x1080 pixels</li>
                                                 <li>For Video Banners: Use embed URLs from YouTube, Vimeo, etc.</li>
                                                 <li>Media can be managed after banner creation</li>
                                             </ul>
                                         </div>
                                        
                                        @if($banner)
                                        <div class="mt-3">
                                            <h6>Current Media</h6>
                                            <p class="text-muted mb-2">
                                                <i class="icon-base ti tabler-photo me-1"></i>
                                                {{ $banner->images->count() }} images
                                            </p>
                                            <a href="{{ route($routeName . '.banners.media', CustomHelper::encrypt($banner->id)) }}" class="btn btn-primary btn-sm">
                                                <i class="icon-base ti tabler-photo me-1"></i>
                                                Manage Media
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route($routeName . '.banners.index') }}" class="btn btn-label-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-base ti tabler-device-floppy me-1"></i>
                                        {{ $banner ? 'Update Banner' : 'Create Banner' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@slot('footerBlock')
<script>
$(document).ready(function() {
    // Banner type change handler
    $('#type').change(function() {
        const type = $(this).val();
        if (type == '2') {
            $('#video-type-container').show();
        } else {
            $('#video-type-container').hide();
            $('#video-embed-container').hide();
        }
    });

    // Video type change handler
    $('#video_type').change(function() {
        const videoType = $(this).val();
        if (videoType == '2') {
            $('#video-embed-container').show();
        } else {
            $('#video-embed-container').hide();
        }
    });

    // Initialize on page load
    $('#type').trigger('change');
    $('#video_type').trigger('change');
});
</script>
@endslot

@endcomponent
