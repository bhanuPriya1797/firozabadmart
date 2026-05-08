@component('admin.layouts.main')

@slot('title')
    {{ $page_heading . ' - ' . config('app.name') }}
@endslot

@slot('headerBlock')
@endslot

@php
    use Illuminate\Support\Facades\Storage;
    use App\Helpers\CustomHelper;

    $routeName = CustomHelper::getAdminRouteName();
    $id = $event->id ?? '';
    $image = $event->image ?? '';
    $storage = Storage::disk('public');
    $imgSrc = (!empty($image) && $storage->exists($image)) ? asset('storage/'.$image) : '';
    $team1Src = method_exists($event, 'getTeamImage1UrlAttribute') ? ($event->team_image_1_url ?? '') : '';
    $team2Src = method_exists($event, 'getTeamImage2UrlAttribute') ? ($event->team_image_2_url ?? '') : '';
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">{{ $page_heading }}</h4>
    </div>
    <div class="row g-6">
        <div class="col-12">
            <div class="card">    
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $event->title ?? '') }}">
                                @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="blog_date" id="blog_date" class="form-control" value="{{ old('blog_date', !empty($event->blog_date) ? \Carbon\Carbon::parse($event->blog_date)->format('Y-m-d') : '') }}">
                                @error('blog_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', !empty($event->end_date) ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d') : '') }}">
                                @error('end_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Brief</label>
                                <textarea name="brief" class="form-control" rows="3">{{ old('brief', $event->brief ?? '') }}</textarea>
                                @error('brief')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="content" id="content" class="form-control ckeditor">{{ old('content', $event->content ?? '') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Image</label>
                                <div class="input-group mb-2">
                                    <input type="file" name="image" class="form-control" style="display:none">
                                    <input type="text" name="image_display" class="form-control" placeholder="No file selected" readonly>
                                    <button type="button" class="btn btn-outline-primary" onclick="openMediaManager('image_media_path')">Choose from Library</button>
                                </div>
                                <input type="hidden" name="image_media_path" id="image_media_path">
                                <div class="mt-2">
                                    <img id="image_media_path_preview" src="" style="display:none;max-height: 100px; width: auto;" class="rounded border">
                                    <button type="button" id="image_media_path_remove" style="display:none" class="btn btn-sm btn-link text-danger" onclick="clearMediaPreview('image_media_path')">Remove</button>
                                </div>
                                @if($imgSrc)
                                    <div class="mt-2" id="existing_image_preview">
                                        <img src="{{ $imgSrc }}" width="100">
                                        <a href="javascript:void(0)" class="text-danger delImg" data-id="{{ $id }}">Delete</a>
                                    </div>
                                @endif
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6"></div>

                            <div class="col-md-6">
                                <label class="form-label">Team Image 1</label>
                                <div class="input-group mb-2">
                                    <input type="file" name="team_image_1" class="form-control" style="display:none">
                                    <input type="text" name="team_image_1_display" class="form-control" placeholder="No file selected" readonly>
                                    <button type="button" class="btn btn-outline-primary" onclick="openMediaManager('team_image_1_media_path')">Choose from Library</button>
                                </div>
                                <input type="hidden" name="team_image_1_media_path" id="team_image_1_media_path" value="">
                                <div class="mt-2">
                                    <img id="team_image_1_media_path_preview" src="" style="display:none;max-height: 100px; width: auto;" class="rounded border">
                                    <button type="button" id="team_image_1_media_path_remove" style="display:none" class="btn btn-sm btn-link text-danger" onclick="clearMediaPreview('team_image_1_media_path')">Remove</button>
                                </div>
                                @if(!empty($team1Src))
                                    <div class="mt-2" id="existing_team1_preview">
                                        <img src="{{ $team1Src }}" width="100" class="rounded border">
                                    </div>
                                @endif
                                @error('team_image_1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Team Image 2</label>
                                <div class="input-group mb-2">
                                    <input type="file" name="team_image_2" class="form-control" style="display:none">
                                    <input type="text" name="team_image_2_display" class="form-control" placeholder="No file selected" readonly>
                                    <button type="button" class="btn btn-outline-primary" onclick="openMediaManager('team_image_2_media_path')">Choose from Library</button>
                                </div>
                                <input type="hidden" name="team_image_2_media_path" id="team_image_2_media_path" value="">
                                <div class="mt-2">
                                    <img id="team_image_2_media_path_preview" src="" style="display:none;max-height: 100px; width: auto;" class="rounded border">
                                    <button type="button" id="team_image_2_media_path_remove" style="display:none" class="btn btn-sm btn-link text-danger" onclick="clearMediaPreview('team_image_2_media_path')">Remove</button>
                                </div>
                                @if(!empty($team2Src))
                                    <div class="mt-2" id="existing_team2_preview">
                                        <img src="{{ $team2Src }}" width="100" class="rounded border">
                                    </div>
                                @endif
                                @error('team_image_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $event->sort_order ?? 0) }}">
                                @error('sort_order')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">SEO / Meta Tags</h5>
                                    </div>
                                    <div class="card-body mt-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Meta Title</label>
                                                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $event->meta_title ?? '') }}">
                                                @error('meta_title')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Meta Keyword</label>
                                                <input type="text" name="meta_keyword" class="form-control" value="{{ old('meta_keyword', $event->meta_keyword ?? '') }}">
                                                @error('meta_keyword')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Meta Description</label>
                                                <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $event->meta_description ?? '') }}</textarea>
                                                @error('meta_description')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" value="1" {{ old('status', $event->status ?? 1) == 1 ? 'checked' : '' }}> Active
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" value="0" {{ old('status', $event->status ?? 1) == 0 ? 'checked' : '' }}> Inactive
                                    </div>
                                </div>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror                                
                            </div>
                            <div class="col-md-6">
                                <label class="form-check-label">Featured</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="featured" value="1" {{ old('featured', $event->featured ?? 0) == 1 ? 'checked' : '' }}>
                                </div>
                                @error('featured')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror                                
                            </div>
                            <div class="col-12">
                                <input type="hidden" name="id" value="{{ old('id', $id) }}">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@slot('footerBlock')
<script src="{{ url('/js/ckeditor/ckeditor.js') }}"></script>
<script>
$(function() {
    CKEDITOR.replace('content', {
        filebrowserImageUploadUrl: '{{ route($routeName . ".ck_upload", ['_token' => csrf_token()]) }}',
        filebrowserUploadMethod: 'form',
        filebrowserBrowseUrl: '{{ route($routeName . ".ck_browse") }}',
        filebrowserImageBrowseUrl: '{{ route($routeName . ".ck_browse") }}',
        extraPlugins: 'filebrowser'
    });
});
$(document).on('click', '.delImg', function () {
    let eventId = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: 'This will permanently delete the image!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-danger mx-2',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route($routeName.'.events.ajax_delete_image', ['type' => request('type')]) }}',
                method: 'POST',
                data: {
                    id: eventId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res.success) {
                        toastr.success(res.msg);
                        location.reload();
                    } else {
                        toastr.error(res.msg);
                    }
                },
                error: function () {
                    toastr.error('Something went wrong');
                }
            });
        }
    });
});
</script>
@endslot

@endcomponent
