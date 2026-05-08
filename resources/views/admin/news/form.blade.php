@component('admin.layouts.main')

@slot('title')
    {{ $page_heading . ' - ' . config('app.name') }}
@endslot

@slot('headerBlock')

@endslot

@php
    use Illuminate\Support\Facades\Storage;
    use App\Helpers\CustomHelper;

    $routeName = CustomHelper::getAdminRouteName();
    $id = $news->id ?? '';
    $image = $news->image ?? '';
    $storage = Storage::disk('public');
    $imgSrc = (!empty($image) && $storage->exists($image)) ? asset('storage/'.$image) : '';

    $routeName = CustomHelper::getAdminRouteName();  
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $page_heading }}</h4>
    </div>
    <div class="row g-6">
        <div class="col-12">
            <div class="card">    
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $news->title ?? '') }}">
                                @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="blog_date" id="blog_date" class="form-control" value="{{ old('blog_date', $news->blog_date) }}">
                                @error('blog_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Brief</label>
                                <textarea name="brief" class="form-control">{{ old('brief', $news->brief ?? '') }}</textarea>
                                @error('brief')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="content" id="content" class="form-control ckeditor">{{ old('content', $news->content ?? '') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Image</label>
                                <div class="input-group mb-2">
                                    <input type="file" name="image" class="form-control" style="display:none">
                                    <input type="text" name="image_display" class="form-control" placeholder="No file selected" readonly>
                                    <button type="button" class="btn btn-outline-primary" onclick="openMediaManager('image_media_path')">Choose from Library</button>
                                </div>
                                <input type="hidden" name="image_media_path" id="image_media_path">
                                
                                <div id="image_media_preview" style="display:none" class="mt-2">
                                    <img id="image_media_path_preview" src="" style="max-height: 100px; width: auto;" class="rounded border">
                                    <button type="button" id="image_media_path_remove" class="btn btn-sm btn-link text-danger" onclick="clearMediaPreview('image_media_path')">Remove</button>
                                </div>

                                @if($imgSrc)
                                    <div class="mt-2" id="existing_image_preview">
                                        <img src="{{ $imgSrc }}" width="100">
                                        <a href="javascript:void(0)" class="text-danger delImg" data-id="{{ $id }}">Delete</a>
                                    </div>
                                @endif
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sort Order</label>
                                <input type="text" name="sort_order" class="form-control" value="{{ old('sort_order', $news->sort_order ?? 0) }}">
                                @error('sort_order')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">SEO / Meta Tags</h5>
                                    </div>
                                    <div class="card-body mt-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Meta Title</label>
                                                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $news->meta_title ?? '') }}">
                                                @error('meta_title')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Meta Keyword</label>
                                                <input type="text" name="meta_keyword" class="form-control" value="{{ old('meta_keyword', $news->meta_keyword ?? '') }}">
                                                @error('meta_keyword')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Meta Description</label>
                                                <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $news->meta_description ?? '') }}</textarea>
                                                @error('meta_description')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" value="1" {{ old('status', $news->status ?? 1) == 1 ? 'checked' : '' }}> Active
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" value="0" {{ old('status', $news->status ?? 1) == 0 ? 'checked' : '' }}> Inactive
                                    </div>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror                                
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label">Featured</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="featured" value="1" {{ old('featured', $news->featured ?? 0) == 1 ? 'checked' : '' }}>
                                </div>
                                @error('featured')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror                                
                            </div>
                            <div class="col-12">
                                <input type="hidden" name="id" value="{{ old('id', $id) }}">
                                <button type="submit" class="btn btn-primary">Submit</button>
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
$(function() {
    CKEDITOR.replace('content', {
        filebrowserImageUploadUrl: "{{ route($routeName . '.ck_upload', ['_token' => csrf_token()]) }}",
        filebrowserUploadMethod: 'form',
        filebrowserBrowseUrl: "{{ route($routeName . '.ck_browse') }}",
        filebrowserImageBrowseUrl: "{{ route($routeName . '.ck_browse') }}",
        extraPlugins: 'filebrowser'
    });
    var uploadJsonUrl = "{{ route($routeName . '.ck_upload_json') }}";
    var overlayHtml = '<div id="imageLibraryOverlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:none;"><div style="max-width:900px;margin:40px auto;background:#fff;border-radius:6px;overflow:hidden"><div style="padding:10px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center"><strong>Image Library</strong><button type="button" id="closeImageLibrary" class="btn btn-sm btn-outline-secondary">Close</button></div><iframe id="imageLibraryFrame" src="" style="width:100%;height:520px;border:0"></iframe></div></div>';
    if(!document.getElementById('imageLibraryOverlay')){ document.body.insertAdjacentHTML('beforeend', overlayHtml); }
    window.insertImageFromLibrary = function(url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.content){ CKEDITOR.instances.content.insertHtml('<img src="'+url+'" />'); } var ov=document.getElementById('imageLibraryOverlay'); if(ov) ov.style.display='none'; };
    var toolsHtml = '<div class="mt-2"><div class="d-flex gap-2 align-items-center"><input type="file" id="contentQuickUpload" class="form-control form-control-sm" accept="image/*" style="max-width:260px"><button type="button" class="btn btn-sm btn-outline-primary" id="contentOpenLibrary">Choose From Library</button></div></div>';
    $('#content').after(toolsHtml);
    $(document).on('change','#contentQuickUpload',function(){ var f=this.files && this.files[0]; if(!f) return; var fd=new FormData(); fd.append('upload', f); fd.append('_token','{{ csrf_token() }}'); fetch(uploadJsonUrl,{method:'POST', body:fd}).then(function(r){return r.json();}).then(function(j){ if(j && j.success && j.url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.content){ CKEDITOR.instances.content.insertHtml('<img src="'+j.url+'" />'); } } }); });
    $(document).on('click','#contentOpenLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); var fr=document.getElementById('imageLibraryFrame'); if(ov && fr){ fr.src = '{{ route($routeName . ".ck_browse") }}'; ov.style.display='block'; } });
    $(document).on('click','#closeImageLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); if(ov){ ov.style.display='none'; } });
    $(document).on('click','#imageLibraryOverlay',function(e){ if(e.target && e.target.id==='imageLibraryOverlay'){ this.style.display='none'; } });
});
$(document).on('click', '.delImg', function () {
    let newsId = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the image!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-danger mx-2',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route($routeName.'.news.ajax_delete_image', ['type' => request('type')]) }}",
                method: 'POST',
                data: {
                    id: newsId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res.success) {
                        toastr.success(res.msg);
                        location.reload();
                    } else {
                        toastr.error(res.msg);
                    }
                },
                error: function () {
                    toastr.error('Something went wrong!');
                }
            });
        }
    });
});
</script>
@endslot
@endcomponent
