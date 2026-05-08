@component('admin.layouts.main')

    @slot('title')
        {{ isset($story) ? 'Edit Testimonial' : 'Create Testimonial' }} - {{ config('app.name') }}
    @endslot

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">{{ isset($story) ? 'Edit' : 'Create' }} Testimonial</h4>
            <a href="{{ route(CustomHelper::getAdminRouteName() . '.success-stories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Testimonial Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ isset($story) ? route(CustomHelper::getAdminRouteName() . '.success-stories.update', $story->encrypted_id) : route(CustomHelper::getAdminRouteName() . '.success-stories.store') }}">
                            @csrf
                            @if(isset($story))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label">Client Name *</label>
                                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                               value="{{ old('title', $story->title ?? '') }}" required>
                                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Designation *</label>
                                        <textarea name="brief" class="form-control @error('brief') is-invalid @enderror" 
                                                  rows="2" maxlength="500" required>{{ old('brief', $story->brief ?? '') }}</textarea>
                                        <div class="form-text">Maximum 500 characters</div>
                                        @error('brief') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Testimonial *</label>
                                        <textarea name="description" id="description" class="form-control ckeditor @error('description') is-invalid @enderror" rows="10" required>{{ old('description', isset($story) && $story->description ? html_entity_decode($story->description, ENT_QUOTES | ENT_HTML5, 'UTF-8') : '') }}</textarea>
                                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Image</label>
                                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                               accept="image/*">
                                        @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        
                                        @if(isset($story) && $story->image)
                                            <div class="mt-2">
                                                <img src="{{ $story->image_url }}" alt="{{ $story->title }}" 
                                                     class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                                <p class="text-muted small mt-1">Current image</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Sort Order</label>
                                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                               value="{{ old('sort_order', $story->sort_order ?? 0) }}" min="0">
                                        <div class="form-text">Lower numbers appear first</div>
                                        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="featured" id="featured" 
                                                   {{ old('featured', $story->featured ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featured">
                                                Featured Story
                                            </label>
                                        </div>
                                        <div class="form-text">Featured stories appear prominently on the homepage</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="status" id="status" 
                                                   {{ old('status', $story->status ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                Active
                                            </label>
                                        </div>
                                        <div class="form-text">Only active stories are displayed on the website</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ isset($story) ? 'Update' : 'Create' }} Story
                                    </button>
                                    <a href="{{ route(CustomHelper::getAdminRouteName() . '.success-stories.index') }}" class="btn btn-secondary">
                                        Cancel
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
    <script>
        $(document).ready(function() {
            // Initialize CKEditor
            CKEDITOR.replace('description', {
                filebrowserImageUploadUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_upload", ["_token" => csrf_token()]) }}',
                filebrowserUploadMethod: 'form',
                filebrowserBrowseUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_browse") }}',
                filebrowserImageBrowseUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_browse") }}',
                extraPlugins: 'filebrowser',
                height: 300,
                toolbar: [
                    { name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                    { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                    { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
                    { name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                    '/',
                    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
                    { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
                    { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                    { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak', 'Iframe' ] },
                    '/',
                    { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                    { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                    { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
                ]
            });

            var uploadJsonUrl = @json(route(CustomHelper::getAdminRouteName() . '.ck_upload_json'));
            var overlayHtml = '<div id="imageLibraryOverlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:none;"><div style="max-width:900px;margin:40px auto;background:#fff;border-radius:6px;overflow:hidden"><div style="padding:10px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center"><strong>Image Library</strong><button type="button" id="closeImageLibrary" class="btn btn-sm btn-outline-secondary">Close</button></div><iframe id="imageLibraryFrame" src="" style="width:100%;height:520px;border:0"></iframe></div></div>';
            if(!document.getElementById('imageLibraryOverlay')){ document.body.insertAdjacentHTML('beforeend', overlayHtml); }
            window.insertImageFromLibrary = function(url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.description){ CKEDITOR.instances.description.insertHtml('<img src="'+url+'" />'); } var ov=document.getElementById('imageLibraryOverlay'); if(ov) ov.style.display='none'; };
            var toolsHtml = '<div class="mt-2"><div class="d-flex gap-2 align-items-center"><input type="file" id="descriptionQuickUpload" class="form-control form-control-sm" accept="image/*" style="max-width:260px"><button type="button" class="btn btn-sm btn-outline-primary" id="descriptionOpenLibrary">Choose From Library</button></div></div>';
            $('#description').after(toolsHtml);
            $(document).on('change','#descriptionQuickUpload',function(){ var f=this.files && this.files[0]; if(!f) return; var fd=new FormData(); fd.append('upload', f); fd.append('_token','{{ csrf_token() }}'); fetch(uploadJsonUrl,{method:'POST', body:fd}).then(function(r){return r.json();}).then(function(j){ if(j && j.success && j.url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.description){ CKEDITOR.instances.description.insertHtml('<img src="'+j.url+'" />'); } } }); });
            $(document).on('click','#descriptionOpenLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); var fr=document.getElementById('imageLibraryFrame'); if(ov && fr){ fr.src = '{{ route(CustomHelper::getAdminRouteName() . ".ck_browse") }}'; ov.style.display='block'; } });
            $(document).on('click','#closeImageLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); if(ov){ ov.style.display='none'; } });
            $(document).on('click','#imageLibraryOverlay',function(e){ if(e.target && e.target.id==='imageLibraryOverlay'){ this.style.display='none'; } });

            // Character counter for brief
            $('textarea[name="brief"]').on('input', function() {
                const maxLength = 500;
                const currentLength = $(this).val().length;
                const remaining = maxLength - currentLength;
                
                if (!$(this).next('.char-counter').length) {
                    $(this).after('<div class="char-counter text-muted small"></div>');
                }
                
                $(this).next('.char-counter').text(`${currentLength}/${maxLength} characters`);
                
                if (remaining < 50) {
                    $(this).next('.char-counter').addClass('text-warning');
                } else {
                    $(this).next('.char-counter').removeClass('text-warning');
                }
            }).trigger('input');
        });
    </script>
    @endslot

@endcomponent
