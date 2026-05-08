@component('admin.layouts.main')

@slot('title')
    {{ ($item->id ? 'Edit' : 'Create') . ' Destination Type - ' . config('app.name') }}
@endslot

@php
    $routeName = App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $item->id ? 'Edit Type' : 'Add Type' }}</h4>
        <a href="{{ route($routeName . '.destinations.types.index') }}" class="btn btn-secondary"><i class="ti tabler-arrow-left me-1"></i> Back</a>
    </div>
    <div class="row g-6">
        <div class="col-12">
            <div class="card">    
                <div class="card-body">
                    <form action="{{ $item->id ? route($routeName . '.destinations.types.update', $item->id) : route($routeName . '.destinations.types.store') }}" method="POST">
                        @csrf
                        @if($item->id)
                            @method('PUT')
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="1" {{ old('status', $item->status) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $item->status) ? '' : 'selected' }}>Inactive</option>
                                </select>
                                @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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

@endcomponent

