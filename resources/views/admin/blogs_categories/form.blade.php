@component('admin.layouts.main')

@slot('title')
    {{ $page_heading }} - {{ config('app.name') }}
@endslot

@php
    $id = CustomHelper::encrypt($category->id) ?? 0;
    $parent_id = $category->parent_id ?? 0;
    $name = $category->name ?? '';
    $slug = $category->slug ?? '';
    $meta_title = $category->meta_title ?? '';
    $meta_keyword = $category->meta_keyword ?? '';
    $meta_description = $category->meta_description ?? '';
    $status = $category->status ?? 1;
    $sort_order = $category->sort_order ?? 0;
@endphp

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $page_heading }}</h4>
    </div>

    <div class="row g-6">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="id" value="{{ $id }}">
                        <input type="hidden" name="parent_id" value="{{ $parent_id }}">

                        <div class="row gy-4">
                            {{-- Name --}}
                            <div class="col-md-6">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $name) }}" placeholder="Enter category name">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Sort Order --}}
                            <div class="col-md-6">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $sort_order) }}" placeholder="Sort Order">
                                @error('sort_order')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Meta Title --}}
                            <div class="col-md-6">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $meta_title) }}" placeholder="Meta Title">
                                @error('meta_title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Meta Keyword --}}
                            <div class="col-md-6">
                                <label class="form-label">Meta Keyword</label>
                                <input type="text" name="meta_keyword" class="form-control @error('meta_keyword') is-invalid @enderror" value="{{ old('meta_keyword', $meta_keyword) }}" placeholder="Meta Keyword">
                                @error('meta_keyword')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Meta Description --}}
                            <div class="col-md-12">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="3" placeholder="Meta Description">{{ old('meta_description', $meta_description) }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-12">
                                <label class="form-label d-block">Status <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="statusActive" name="status" class="form-check-input" value="1" {{ old('status', $status) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusActive">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" id="statusInactive" name="status" class="form-check-input" value="0" {{ old('status', $status) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusInactive">Inactive</label>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                Submit
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endcomponent
