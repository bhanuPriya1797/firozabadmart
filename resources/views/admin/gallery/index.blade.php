@component('admin.layouts.main')

@slot('title')
    Gallery - {{ config('app.name') }}
@endslot

@php
    $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Gallery Folders</h4>
    <div class="d-flex gap-2">
      <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.images.index') }}" class="btn btn-outline-secondary">Manage Images</a>
      <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.folders.create') }}" class="btn btn-primary">Create Folder</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="row g-4">
        @forelse($folders as $folder)
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100">
              <img src="{{ $folder->cover_url }}" class="card-img-top" alt="{{ $folder->name }}">
              <div class="card-body">
                <h5 class="card-title mb-1">{{ $folder->name }}</h5>
                <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit($folder->description, 100) }}</p>
                <div class="d-flex gap-2">
                  <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.folders.edit', $folder->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="{{ route($ADMIN_ROUTE_NAME.'.gallery.folders.images', $folder->id) }}" class="btn btn-sm btn-outline-secondary">Images</a>
                  <form action="{{ route($ADMIN_ROUTE_NAME.'.gallery.folders.destroy', $folder->id) }}" method="POST" onsubmit="return confirm('Delete this folder?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-muted">No folders found.</div>
        @endforelse
      </div>
      <div class="mt-3">
        {{ $folders->links() }}
      </div>
    </div>
  </div>
</div>

@endcomponent

