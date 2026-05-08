@component('admin.layouts.main')

@slot('title')
    {{ ($item->id ? 'Edit' : 'Create') . ' Service - ' . config('app.name') }}
@endslot

@slot('headerBlock')
@endslot

@php
    use Illuminate\Support\Facades\Storage;
    $routeName = App\Helpers\CustomHelper::getAdminRouteName();
    $imgSrc = ($item->image && Storage::disk('public')->exists($item->image)) ? asset('storage/'.$item->image) : '';
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $item->id ? 'Edit Service' : 'Add Service' }}</h4>
        <a href="{{ route($routeName . '.services.index') }}" class="btn btn-secondary"><i class="ti tabler-arrow-left me-1"></i> Back</a>
    </div>
    <div class="row g-6">
        <div class="col-12">
            <div class="card">    
                <div class="card-body">
                    <form action="{{ $item->id ? route($routeName . '.services.update', $item->id) : route($routeName . '.services.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($item->id)
                            @method('PUT')
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $item->title) }}" required>
                                @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug) }}" placeholder="Auto from title if blank">
                                @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Brief</label>
                                <input type="text" name="brief" class="form-control" value="{{ old('brief', $item->brief) }}">
                                @error('brief')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control ckeditor">{{ old('description', $item->description) }}</textarea>
                                @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control">
                                @if($imgSrc)
                                    <div class="mt-2">
                                        <img src="{{ $imgSrc }}" width="120">
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2 delImg" data-id="{{ $item->id }}">Delete</button>
                                    </div>
                                @endif
                                @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Featured</label>
                                <select name="featured" class="form-select">
                                    <option value="0" {{ old('featured', $item->featured) ? '' : 'selected' }}>No</option>
                                    <option value="1" {{ old('featured', $item->featured) ? 'selected' : '' }}>Yes</option>
                                </select>
                                @error('featured')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="1" {{ old('status', $item->status) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $item->status) ? '' : 'selected' }}>Inactive</option>
                                </select>
                                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order) }}">
                                @error('sort_order')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
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
var svcCkUploadUrl = @json(route($routeName . '.ck_upload') . '?_token=' . csrf_token());
var svcCkBrowseUrl = @json(route($routeName . '.ck_browse'));
var svcUploadJsonUrl = @json(route($routeName . '.ck_upload_json'));
$(function() {
    CKEDITOR.replace('description', {
        filebrowserImageUploadUrl: svcCkUploadUrl,
        filebrowserUploadMethod: 'form',
        filebrowserBrowseUrl: svcCkBrowseUrl,
        filebrowserImageBrowseUrl: svcCkBrowseUrl,
        extraPlugins: 'filebrowser'
    });
    var overlayHtml = '<div id="imageLibraryOverlay" style="position:fixed;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:none;"><div style="max-width:900px;margin:40px auto;background:#fff;border-radius:6px;overflow:hidden"><div style="padding:10px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center"><strong>Image Library</strong><button type="button" id="closeImageLibrary" class="btn btn-sm btn-outline-secondary">Close</button></div><iframe id="imageLibraryFrame" src="" style="width:100%;height:520px;border:0"></iframe></div></div>';
    if(!document.getElementById('imageLibraryOverlay')){ document.body.insertAdjacentHTML('beforeend', overlayHtml); }
    window.insertImageFromLibrary = function(url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.description){ CKEDITOR.instances.description.insertHtml('<img src="'+url+'" />'); } var ov=document.getElementById('imageLibraryOverlay'); if(ov) ov.style.display='none'; };
    var toolsHtml = '<div class="mt-2"><div class="d-flex gap-2 align-items-center"><input type="file" id="descriptionQuickUpload" class="form-control form-control-sm" accept="image/*" style="max-width:260px"><button type="button" class="btn btn-sm btn-outline-primary" id="descriptionOpenLibrary">Choose From Library</button></div></div>';
    $('#description').after(toolsHtml);
    $(document).on('change','#descriptionQuickUpload',function(){ var f=this.files && this.files[0]; if(!f) return; var fd=new FormData(); fd.append('upload', f); fd.append('_token','{{ csrf_token() }}'); fetch(svcUploadJsonUrl,{method:'POST', body:fd}).then(function(r){return r.json();}).then(function(j){ if(j && j.success && j.url){ if(window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances.description){ CKEDITOR.instances.description.insertHtml('<img src="'+j.url+'" />'); } } }); });
    $(document).on('click','#descriptionOpenLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); var fr=document.getElementById('imageLibraryFrame'); if(ov && fr){ fr.src = svcCkBrowseUrl; ov.style.display='block'; } });
    $(document).on('click','#closeImageLibrary',function(){ var ov=document.getElementById('imageLibraryOverlay'); if(ov){ ov.style.display='none'; } });
    $(document).on('click','#imageLibraryOverlay',function(e){ if(e.target && e.target.id==='imageLibraryOverlay'){ this.style.display='none'; } });
});
$(document).on('click', '.delImg', function () {
    let id = $(this).data('id');
    $.post('{{ route($routeName . ".services.delete-image") }}', {id: id, _token: '{{ csrf_token() }}'}, function(res){
        if(res.success){ location.reload(); }
    });
});
</script>
@endslot
@endcomponent
