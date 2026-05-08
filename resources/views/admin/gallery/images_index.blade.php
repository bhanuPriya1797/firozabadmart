@component('admin.layouts.main')

@slot('title')
    Gallery Images - {{ config('app.name') }}
@endslot

@php
    $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Gallery Images</h4>
    <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.index') }}" class="btn btn-outline-secondary">Back to Folders</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card mb-4">
    <div class="card-body">
      <div>
        <h6 class="mb-2">Upload Images</h6>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Select Files</label>
            <input type="file" id="chunkFiles" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
          </div>
          <div class="col-md-4">
            <label class="form-label">Optional Title</label>
            <input type="text" id="chunkTitle" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Assign Folder</label>
            <select id="chunkFolderId" class="form-select">
              <option value="">None (Direct Gallery)</option>
              @foreach($folders as $f)
                <option value="{{ $f->id }}">{{ $f->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <button type="button" id="startChunkUpload" class="btn btn-outline-primary">Start Upload</button>
          </div>
        </div>
        <div class="mt-3" id="uploadProgressList"></div>
      </div>
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

(function() {
  const filesInput = document.getElementById('chunkFiles');
  const titleInput = document.getElementById('chunkTitle');
  const folderSelect = document.getElementById('chunkFolderId');
  const startBtn = document.getElementById('startChunkUpload');
  const progressList = document.getElementById('uploadProgressList');
  if (!filesInput || !startBtn) return;

  function createProgressItem(name) {
    const wrapper = document.createElement('div');
    wrapper.className = 'mb-2';
    wrapper.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="small text-muted">${name}</span>
        <span class="small"><span class="percent">0</span>%</span>
      </div>
      <div class="progress" style="height:8px;">
        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
    `;
    progressList.appendChild(wrapper);
    return {
      set: (p) => {
        const bar = wrapper.querySelector('.progress-bar');
        const percent = wrapper.querySelector('.percent');
        bar.style.width = p + '%';
        bar.setAttribute('aria-valuenow', p);
        percent.textContent = p;
      },
      done: (previewUrl, fullUrl) => {
        const thumb = document.createElement('img');
        thumb.src = previewUrl || fullUrl;
        thumb.className = 'rounded mt-2';
        thumb.style.maxHeight = '80px';
        wrapper.appendChild(thumb);
      }
    }
  }

  async function uploadFileChunked(file, title, folderId) {
    const chunkSize = 2 * 1024 * 1024; // 2MB
    const totalChunks = Math.ceil(file.size / chunkSize);
    const uploadKey = 'gal_' + Date.now() + '_' + Math.random().toString(36).slice(2);
    const progress = createProgressItem(file.name);
    for (let i = 0; i < totalChunks; i++) {
      const start = i * chunkSize;
      const end = Math.min(start + chunkSize, file.size);
      const blob = file.slice(start, end);
      const fd = new FormData();
      fd.append('_token', '{{ csrf_token() }}');
      fd.append('chunk', blob, file.name + '.part');
      fd.append('chunk_index', i.toString());
      fd.append('total_chunks', totalChunks.toString());
      fd.append('upload_key', uploadKey);
      fd.append('file_name', file.name);
      if (folderId) fd.append('folder_id', folderId);
      if (title) fd.append('title', title);
      const res = await fetch('{{ route($ADMIN_ROUTE_NAME.".gallery.images.upload-chunk") }}', {
        method: 'POST',
        body: fd
      });
      const json = await res.json();
      const percent = Math.round(((i + 1) / totalChunks) * 100);
      progress.set(percent);
      if (i === totalChunks - 1 && json && json.success) {
        progress.done(json.preview_url, json.full_url);
      }
    }
  }

  startBtn.addEventListener('click', async function() {
    const files = filesInput.files;
    const title = titleInput.value || '';
    const folderId = folderSelect.value || '';
    if (!files || files.length === 0) return;
    for (const file of files) {
      await uploadFileChunked(file, title, folderId);
    }
  });
})();
</script>
