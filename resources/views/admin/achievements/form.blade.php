@component('admin.layouts.main')

@slot('title')
{{ $page_heading ?? 'Achievement' }} - {{ config('app.name') }}
@endslot

@php
    $routeName = \App\Helpers\CustomHelper::getAdminRouteName();
    $id = $achievement->id ?? null;
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">{{ $page_heading ?? 'Achievement' }}</h4>
        <a href="{{ route($routeName.'.achievements.index') }}" class="btn btn-outline-secondary">
            <i class="icon-base ti tabler-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ $id ? route($routeName.'.achievements.update', \App\Helpers\CustomHelper::encrypt($id)) : route($routeName.'.achievements.store') }}" method="POST">
                @csrf
                @if($id)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Winner Name <span class="text-danger">*</span></label>
                        <input type="text" name="winner_name" class="form-control" value="{{ old('winner_name', $achievement->winner_name ?? '') }}">
                        @error('winner_name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Medal</label>
                        <select name="medal" class="form-select">
                            <option value="">Select</option>
                            @php $medal = old('medal', $achievement->medal ?? ''); @endphp
                            <option value="gold" {{ $medal==='gold'?'selected':'' }}>Gold</option>
                            <option value="silver" {{ $medal==='silver'?'selected':'' }}>Silver</option>
                            <option value="bronze" {{ $medal==='bronze'?'selected':'' }}>Bronze</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category', $achievement->category ?? '') }}" placeholder="e.g., Recurve / Compound / Para">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Event Name</label>
                        <input type="text" name="event_name" class="form-control" value="{{ old('event_name', $achievement->event_name ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location', $achievement->location ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $achievement->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        @php $status = old('status', $achievement->status ?? 1); @endphp
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ $status ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">Active</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route($routeName.'.achievements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endcomponent
