@component('admin.layouts.main')

@slot('title')
    Folder Images - {{ $folder->name }} - {{ config('app.name') }}
@endslot

@php
    $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Images: {{ $folder->name }}</h4>
    <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.index') }}" class="btn btn-outline-secondary">Back to Folders</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Add Images</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-end g-3">
            <div class="col-md-8">
                 <label class="form-label">Select Image from Media Manager</label>
                 <div class="input-group">
                    <input type="text" id="media-library-selection" class="form-control" placeholder="No file selected" readonly>
                    <button class="btn btn-primary" type="button" onclick="openMediaManager('media-library-selection')">
                        <i class="ti tabler-photo me-1"></i> Open Media Manager
                    </button>
                 </div>
            </div>
            <div class="col-md-4">
                <button type="button" id="btn-add-library-image" class="btn btn-success w-100">
                    <i class="ti tabler-plus me-1"></i> Add Selected to Gallery
                </button>
            </div>
        </div>
        <div id="library-status" class="mt-2"></div>
        <input type="hidden" id="chunkFolderId" value="{{ $folder->id }}">
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="row g-4">
        @forelse($images as $img)
          <div class="col-lg-2 col-md-3 col-sm-4">
            <div class="card h-100">
              <img src="{{ $img->url }}" class="card-img-top" alt="{{ $img->title ?? '' }}">
              <div class="card-body">
                <div class="small text-muted">{{ $img->title }}</div>
                <form action="{{ route($ADMIN_ROUTE_NAME.'.gallery.images.destroy', $img->id) }}" method="POST" onsubmit="return confirm('Delete this image?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-muted">No images found.</div>
        @endforelse
      </div>
      <div class="mt-3">
        {{ $images->links() }}
      </div>
    </div>
  </div>
</div>

@endcomponent

<script src="{{ asset('admin/assets/js/media-selector.js') }}"></script>
<script>
// Library Add Handler
document.addEventListener('DOMContentLoaded', function() {
    const btnAdd = document.getElementById('btn-add-library-image');
    if(btnAdd) {
        btnAdd.addEventListener('click', function() {
            const filePath = document.getElementById('media-library-selection').value;
            const folderId = document.getElementById('chunkFolderId').value;
            const statusEl = document.getElementById('library-status');

            if (!filePath) {
                statusEl.innerHTML = '<span class="text-warning">Please select a file first.</span>';
                return;
            }

            statusEl.innerHTML = '<span class="text-info"><i class="icon-base ti tabler-loader animate-spin"></i> Adding image...</span>';

            const formData = new FormData();
            formData.append('file_path', filePath);
            if(folderId) formData.append('folder_id', folderId);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route($ADMIN_ROUTE_NAME . ".gallery.images.addMediaFromLibrary") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusEl.innerHTML = '<span class="text-success"><i class="icon-base ti tabler-check"></i> ' + data.message + '</span>';
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    statusEl.innerHTML = '<span class="text-danger"><i class="icon-base ti tabler-alert-circle"></i> ' + data.message + '</span>';
                }
            })
            .catch(error => {
                console.error(error);
                statusEl.innerHTML = '<span class="text-danger">An error occurred.</span>';
            });
        });
    }
});
</script>
