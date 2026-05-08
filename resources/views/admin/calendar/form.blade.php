@component('admin.layouts.main')
  @slot('title') {{ $item->id ? 'Edit' : 'Add' }} Calendar Event - {{ config('app.name') }} @endslot
  @php $ADMIN = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">{{ $item->id ? 'Edit' : 'Add' }} Calendar Event</h4>
      <a href="{{ route($ADMIN.'.calendar.index') }}" class="btn btn-label-secondary">Back</a>
    </div>
    <div class="card">
      <form action="{{ $item->id ? route($ADMIN.'.calendar.update', $item->id) : route($ADMIN.'.calendar.store') }}" method="POST">
        @csrf
        @if($item->id) @method('PUT') @endif
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $item->title) }}" required>
            @error('title')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($item->start_date)->format('Y-m-d')) }}">
            @error('start_date')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($item->end_date)->format('Y-m-d')) }}">
            @error('end_date')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="{{ old('location', $item->location) }}">
            @error('location')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="{{ old('category', $item->category) }}" placeholder="e.g., Recurve, Compound, Indian Round">
            @error('category')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">External URL</label>
            <input type="url" name="external_url" class="form-control" value="{{ old('external_url', $item->external_url) }}" placeholder="Optional link">
            @error('external_url')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description', $item->description) }}</textarea>
            @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
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
          <div class="col-md-3">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug) }}" placeholder="leave blank to auto-generate">
            @error('slug')<div class="text-danger small">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer">
          <button class="btn btn-success" type="submit">{{ $item->id ? 'Update' : 'Create' }}</button>
        </div>
      </form>
    </div>
  </div>
@endcomponent
