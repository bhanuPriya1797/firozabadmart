@component('admin.layouts.main')

@slot('title')
    {{ $folder->id ? 'Edit Folder' : 'Create Folder' }} - {{ config('app.name') }}
@endslot

@php
    $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $folder->id ? 'Edit Folder' : 'Create Folder' }}</h4>
    <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.index') }}" class="btn btn-outline-secondary">Back</a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data" action="{{ $folder->id ? route($ADMIN_ROUTE_NAME.'.gallery.folders.update', $folder->id) : route($ADMIN_ROUTE_NAME.'.gallery.folders.store') }}">
        @csrf
        @if($folder->id)
          @method('PUT')
        @endif
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $folder->name) }}" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $folder->sort_order ?? 0) }}" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="4" class="form-control">{{ old('description', $folder->description) }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="1" {{ old('status', $folder->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
              <option value="0" {{ old('status', $folder->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Cover Image</label>
            <div class="input-group">
                <input type="file" name="cover_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <button type="button" class="btn btn-outline-secondary" onclick="openMediaManager('cover_image_media_path')">
                    <i class="ti tabler-photo me-1"></i> Library
                </button>
            </div>
            <input type="hidden" name="cover_image_media_path" id="cover_image_media_path">
            <div id="cover_image_media_path_container" class="mt-2" style="display:none">
                <img id="cover_image_media_path_preview" src="" class="rounded" style="max-height:100px">
            </div>

            @if($folder->cover_image)
              <div class="mt-2">
                <img src="{{ $folder->cover_url }}" alt="" class="rounded" style="max-height:100px">
              </div>
            @endif
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@endcomponent

<script src="{{ asset('admin/assets/js/media-selector.js') }}"></script>
<script>
    // Listen for changes on hidden input to show preview container
    document.getElementById('cover_image_media_path').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('cover_image_media_path_container').style.display = 'block';
        }
    });
</script>

