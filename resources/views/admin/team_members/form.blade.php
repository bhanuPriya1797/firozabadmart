@component('admin.layouts.main')
  @slot('title') {{ $item->id ? 'Edit' : 'Add' }} Team Member - {{ config('app.name') }} @endslot
  @php $ADMIN = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">{{ $item->id ? 'Edit' : 'Add' }} Team Member</h4>
      <a href="{{ route($ADMIN.'.team-members.index') }}" class="btn btn-label-secondary">Back</a>
    </div>
    <div class="card">
      <form action="{{ $item->id ? route($ADMIN.'.team-members.update', $item->id) : route($ADMIN.'.team-members.store') }}" method="POST">
        @csrf
        @if($item->id) @method('PUT') @endif
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Designation</label>
            <input type="text" name="designation" class="form-control" value="{{ old('designation', $item->designation) }}">
            @error('designation')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-control" rows="5">{{ old('bio', $item->bio) }}</textarea>
            @error('bio')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Image</label>
            @php $img = old('image', $item->image); $url = $img ? (str_starts_with($img,'media/') ? asset('storage/'.$img) : asset('storage/uploads/team/'.$img)) : ''; @endphp
            <div class="d-flex align-items-start gap-3">
              <div class="border rounded p-2" style="width:120px;height:120px;display:flex;align-items:center;justify-content:center;background:#f8f9fa;">
                <img src="{{ $url }}" id="image_preview" class="img-fluid {{ $url ? '' : 'd-none' }}" style="max-height:100%;max-width:100%;">
              </div>
              <div>
                <button type="button" class="btn btn-primary mb-2" onclick="openMediaManager('image')">
                  <i class="ti tabler-photo me-1"></i> Choose From Library
                </button>
                <button type="button" id="image_remove" class="btn btn-label-danger mb-2 {{ $url ? '' : 'd-none' }}" onclick="clearMediaPreview('image')">
                  <i class="ti tabler-trash me-1"></i> Remove
                </button>
                <input type="hidden" name="image" id="image" value="{{ old('image', $item->image) }}">
              </div>
            </div>
            @error('image')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order ?? 0) }}" min="0">
            @error('sort_order')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label d-block">Featured</label>
            <label class="switch">
              <input type="checkbox" name="featured" value="1" {{ old('featured', $item->featured) ? 'checked' : '' }}>
              <span class="slider"></span>
            </label>
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="1" {{ old('status', $item->status) == 1 ? 'selected' : '' }}>Active</option>
              <option value="0" {{ old('status', $item->status) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-success" type="submit">{{ $item->id ? 'Update' : 'Create' }}</button>
        </div>
      </form>
    </div>
  </div>
  @slot('footerBlock')
    <script src="{{ asset('admin/assets/js/media-selector.js') }}"></script>
  @endslot
@endcomponent
