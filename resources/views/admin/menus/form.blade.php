@component('admin.layouts.main')

@slot('title')
{{ $page_heading }} - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();

    $id = !empty($menu->id) ? CustomHelper::encrypt($menu->id) : "";
    $title = $menu->title ?? '';
    $position = $menu->position ?? '';
    $status = $menu->status ?? 1;

    $positionArr = ['top' => 'Top', 'bottom' => 'Bottom'];
    $backUrl = request('back_url') ?? '';
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

		                    <div class="row g-3">
		                        <div class="col-md-6">
		                            <label class="form-label required" for="title">Title</label>
		                            <input type="text" name="title" id="title" value="{{ old('title', $title) }}" class="form-control @error('title') is-invalid @enderror" />
		                        </div>
								@error('title')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror

		                        <div class="col-md-6">
		                            <label class="form-label required" for="position">Position</label>
		                            <select name="position" class="form-select @error('position') is-invalid @enderror">
		                                @foreach($positionArr as $key => $val)
		                                    <option value="{{ $key }}" {{ $key == $position ? 'selected' : '' }}>{{ $val }}</option>
		                                @endforeach
		                            </select>
		                        </div>
								@error('position')
								<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
		                    </div>

		                    <div class="row mt-3">
		                        <div class="col-md-12">
		                            <label class="form-label">Status</label>
		                            <div class="form-check form-check-inline">
		                                <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ $status == 1 ? 'checked' : '' }}>
		                                <label class="form-check-label" for="status_active">Active</label>
		                            </div>
		                            <div class="form-check form-check-inline">
		                                <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ $status === 0 ? 'checked' : '' }}>
		                                <label class="form-check-label" for="status_inactive">Inactive</label>
		                            </div>
		                        </div>
								@error('status')
								<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
		                    </div>

		                    <input type="hidden" name="id" value="{{ old('id', $id) }}" />

		                    <div class="mt-4">
		                        <button type="submit" class="btn btn-primary">
		                            <i class="fa fa-save me-1"></i> Submit
		                        </button>
		                    </div>
		                </form>
		            </div>
		        </div>
		    </div>
		</div>
    </div>
    <!-- Content -->

@slot('footerBlock')
<!-- Additional scripts if needed -->
@endslot

@endcomponent
