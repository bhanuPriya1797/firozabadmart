@component('admin.layouts.main')

@slot('title')
    {{ $page_heading ?? (isset($circular) ? 'Edit Circular' : 'Add Circular') }} - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
    $idEnc = isset($circular) ? CustomHelper::encrypt($circular->id) : '';
    $title = old('title', $circular->title ?? '');
    $slug = old('slug', $circular->slug ?? '');
    $description = old('description', $circular->description ?? '');
    $status = old('status', $circular->status ?? 1);
    $sortOrder = old('sort_order', $circular->sort_order ?? 0);
    $image = old('image', $circular->image ?? '');
    $banner = old('banner_image', $circular->banner_image ?? '');
    $doc = old('document_path', $circular->document_path ?? '');
    $seo = $circular->seo ?? [];
    $metaTitle = old('meta_title', $seo['meta_title'] ?? '');
    $metaKeywords = old('meta_keywords', $seo['meta_keywords'] ?? '');
    $metaDescription = old('meta_description', $seo['meta_description'] ?? '');
    $featured = old('featured', $circular->featured ?? 0);
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $page_heading ?? (isset($circular) ? 'Edit Circular' : 'Add Circular') }}</h4>
        <a href="{{ route($routeName . '.circulars.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left icon-sm me-2"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route($routeName . '.circulars.save', $idEnc) }}">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <h6 class="text-primary">Basic</h6>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $title }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ $slug }}" placeholder="Auto-generated if blank">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="circular_description" class="form-control" rows="6">{{ $description }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Image</label>
                        <div class="d-flex align-items-start gap-3">
                            <div class="border rounded p-2" style="width:140px;height:140px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;">
                                @if(!empty($image))
                                    <img src="{{ asset('storage/'.$image) }}" id="image_preview" class="img-fluid" style="max-width:100%;max-height:100%;">
                                @else
                                    <img src="" id="image_preview" class="img-fluid" style="display:none;max-width:100%;max-height:100%;">
                                @endif
                            </div>
                            <div>
                                <button class="btn btn-primary mb-2" type="button" onclick="openMediaManager('image')">
                                    <i class="ti tabler-photo me-1"></i> Choose From Library
                                </button>
                                <button class="btn btn-label-danger mb-2" id="image_remove" type="button" style="{{ $image ? '' : 'display:none;' }}" onclick="clearMediaPreview('image')">
                                    <i class="ti tabler-trash me-1"></i> Remove
                                </button>
                                <input type="hidden" id="image" name="image" value="{{ $image }}">
                                <div class="small text-muted">Recommended: 600x400 or similar</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Banner Image</label>
                        <div class="d-flex align-items-start gap-3">
                            <div class="border rounded p-2" style="width:140px;height:140px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;">
                                @if(!empty($banner))
                                    <img src="{{ asset('storage/'.$banner) }}" id="banner_image_preview" class="img-fluid" style="max-width:100%;max-height:100%;">
                                @else
                                    <img src="" id="banner_image_preview" class="img-fluid" style="display:none;max-width:100%;max-height:100%;">
                                @endif
                            </div>
                            <div>
                                <button class="btn btn-primary mb-2" type="button" onclick="openMediaManager('banner_image')">
                                    <i class="ti tabler-photo me-1"></i> Choose From Library
                                </button>
                                <button class="btn btn-label-danger mb-2" id="banner_image_remove" type="button" style="{{ $banner ? '' : 'display:none;' }}" onclick="clearMediaPreview('banner_image')">
                                    <i class="ti tabler-trash me-1"></i> Remove
                                </button>
                                <input type="hidden" id="banner_image" name="banner_image" value="{{ $banner }}">
                                <div class="small text-muted">Recommended: 1600x600 or similar</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">PDF Document</label>
                        <div class="d-flex align-items-start gap-3">
                            <div class="border rounded p-2" style="width:140px;height:140px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;">
                                @if(!empty($doc))
                                    <div id="document_path_preview" class="text-center">
                                        <i class="ti tabler-file-description icon-2xl d-block mb-1"></i>
                                        <small class="d-block text-break">{{ basename($doc) }}</small>
                                    </div>
                                @else
                                    <div id="document_path_preview" style="display:none;" class="text-center">
                                        <i class="ti tabler-file-description icon-2xl d-block mb-1"></i>
                                        <small class="d-block text-break"></small>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <button class="btn btn-primary mb-2" type="button" onclick="openMediaManager('document_path')">
                                    <i class="ti tabler-photo me-1"></i> Select From Library
                                </button>
                                <button class="btn btn-label-danger mb-2" id="document_path_remove" type="button" style="{{ $doc ? '' : 'display:none;' }}" onclick="clearMediaPreview('document_path')">
                                    <i class="ti tabler-trash me-1"></i> Remove
                                </button>
                                <input type="hidden" id="document_path" name="document_path" value="{{ $doc }}">
                                <div class="small text-muted">Allowed: PDF or any document stored in Media Manager</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="{{ $sortOrder }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ $status ? 'checked' : '' }}>
                            <label for="status" class="form-check-label">Active</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Featured</label>
                        <div class="form-check form-switch mt-2">
                            <input type="hidden" name="featured" value="0">
                            <input class="form-check-input" type="checkbox" name="featured" id="featured" value="1" {{ $featured ? 'checked' : '' }}>
                            <label for="featured" class="form-check-label">Show in Homepage Circular</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <hr>
                        <h6 class="text-primary">SEO</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ $metaTitle }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="{{ $metaKeywords }}" placeholder="comma, separated, keywords">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3">{{ $metaDescription }}</textarea>
                    </div>

                    <div class="col-12 mt-3">
                        <button class="btn btn-primary" type="submit">Save Circular</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@slot('footerBlock')
<script src="{{ asset('admin/assets/js/media-selector.js') }}"></script>
<script src="{{ asset('admin/assets/ckeditor/ckeditor.js') }}"></script>
<script>
(function(){
    if (window.CKEDITOR) {
        try { CKEDITOR.replace('circular_description'); } catch(e){}
    }
    // Enhance default onMediaFileSelected to show filename for document
    const baseHandler = window.onMediaFileSelected;
    window.onMediaFileSelected = function(fieldId, file){
        if (baseHandler) baseHandler(fieldId, file);
        if (fieldId === 'document_path') {
            const prev = document.getElementById('document_path_preview');
            if (prev) {
                prev.style.display = 'block';
                const small = prev.querySelector('small');
                if (small) small.textContent = (file.name || file.path || '').split('/').pop();
            }
            const rm = document.getElementById('document_path_remove');
            if (rm) rm.style.display = 'inline-block';
        }
        if (fieldId === 'image') {
            const rm = document.getElementById('image_remove');
            if (rm) rm.style.display = 'inline-block';
        }
        if (fieldId === 'banner_image') {
            const rm = document.getElementById('banner_image_remove');
            if (rm) rm.style.display = 'inline-block';
        }
    };
})();
</script>
<script>
// Basic SEO fields toggling/showing - no extra JS required, but keeping place for consistency
</script>
@endslot

@endcomponent
*** End Patch***} -->
