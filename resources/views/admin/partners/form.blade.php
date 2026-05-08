@component('admin.layouts.main')

@slot('title')
    {{ isset($partner) ? 'Edit Partner' : 'Add Partner' }} - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ isset($partner) ? 'Edit Partner' : 'Add Partner' }}</h4>
        <a href="{{ route($routeName . '.partners.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left icon-sm me-2"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route($routeName . '.partners.save', isset($partner) ? $partner->id : '') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $partner->title ?? '') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="sort_order">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $partner->sort_order ?? 0) }}">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <div class="form-check form-switch mb-2">
                            <!-- Hidden input for unchecked state -->
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $partner->status ?? 1) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Partner Logo</label>
                        <div class="d-flex align-items-start gap-3">
                            <div class="preview-box border rounded p-2" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                @if(isset($partner) && $partner->logo_path)
                                    <img src="{{ asset('storage/' . $partner->logo_path) }}" id="logo_preview" class="img-fluid" style="max-height: 100%; max-width: 100%;">
                                @else
                                    <img src="" id="logo_preview" class="img-fluid" style="max-height: 100%; max-width: 100%; display: none;">
                                @endif
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary mb-2" onclick="openMediaManager('logo_path')">
                                    <i class="ti tabler-photo me-1"></i> Select Image
                                </button>
                                <button type="button" class="btn btn-label-danger mb-2" onclick="removeImage()">
                                    <i class="ti tabler-trash me-1"></i> Remove
                                </button>
                                <div class="small text-muted">Allowed files: jpg, png, gif.</div>
                                <input type="hidden" id="logo_path" name="logo_path" value="{{ old('logo_path', $partner->logo_path ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Partner</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@slot('footerBlock')
<script src="{{ asset('admin/assets/js/media-selector.js') }}"></script>
<script>
    // This function is called by the media manager popup
    window.onMediaFileSelected = function(fieldId, file) {
        if (fieldId === 'logo_path') {
            document.getElementById('logo_path').value = file.path;
            const preview = document.getElementById('logo_preview');
            preview.src = file.url;
            preview.style.display = 'block';
        }
    }

    function removeImage() {
        document.getElementById('logo_path').value = '';
        const preview = document.getElementById('logo_preview');
        preview.src = '';
        preview.style.display = 'none';
    }
</script>
@endslot

@endcomponent
