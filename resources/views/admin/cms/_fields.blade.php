<div class="col-md-6">
    <label class="form-label">Page Title <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}">
</div>
@error('title')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6">
    <label class="form-label">Slug</label>
    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $page->slug) }}">
</div>
@error('slug')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6">
    <label class="form-label">Heading</label>
    <input type="text" name="heading" class="form-control @error('heading') is-invalid @enderror" value="{{ old('heading', $page->heading) }}">
</div>
@error('heading')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6">
    <label class="form-label">Brief</label>
    <input type="text" name="brief" class="form-control @error('brief') is-invalid @enderror" value="{{ old('brief', $page->brief) }}">
</div>
@error('brief')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6">
    <label class="form-label">Template</label>
    <select name="template" class="form-select @error('template') is-invalid @enderror">
        <option value="">Default</option>
        @foreach ($templates as $template)
            <option value="{{ $template }}" {{ $template == old('template', $page->template) ? 'selected' : '' }}>
                {{ ucfirst($template) }}
            </option>
        @endforeach
    </select>
</div>
@error('template')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6">
    <label class="form-label">Sort Order</label>
    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $page->sort_order) }}">
</div>
@error('sort_order')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6">
    <label class="form-label">Parent Page</label>
    <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
        <option value="0">None</option>
        @foreach(\App\Models\Cms::where('id', '!=', $page->id ?? 0)->pluck('title', 'id') as $id => $title)
            <option value="{{ $id }}" {{ old('parent_id', $page->parent_id) == $id ? 'selected' : '' }}>{{ $title }}</option>
        @endforeach
    </select>
</div>
@error('parent_id')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6">
    <label class="form-label">Banner</label>
    <input type="file" name="banner" class="form-control @error('banner') is-invalid @enderror">
    @if (!empty($page->banner))
        <div class="mt-2" id="banner-preview">
            <img src="{{ asset('storage/' . $page->banner) }}" alt="Banner" height="80" class="d-block mb-2">
            <button type="button" class="btn btn-sm btn-danger" id="deleteBannerBtn">Delete Banner</button>
        </div>
        <!-- Hidden field to flag deletion -->
        <input type="hidden" name="delete_banner" id="deleteBannerInput" value="0">
    @endif
</div>
@error('banner')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-12">
    <label class="form-label">Description</label>
    <textarea name="description" class="ckeditor form-control @error('description') is-invalid @enderror">{{ old('description', $page->description) }}</textarea>
</div>
@error('description')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6 mb-3">
    <label class="form-label">Status</label>
    <div class="form-check form-switch">
        <input type="checkbox" name="status" class="form-check-input" id="statusSwitch"
               {{ old('status', $page->status ?? 1) ? 'checked' : '' }} value="1">
        <label class="form-check-label" for="statusSwitch">Active</label>
    </div>
    @error('status')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-6 mb-3">
    <label class="form-label">Featured</label>
    <div class="form-check form-switch">
        <input type="checkbox" name="featured" class="form-check-input" id="featuredSwitch"
               {{ old('featured', $page->featured ?? 0) ? 'checked' : '' }} value="1">
        <label class="form-check-label" for="featuredSwitch">Yes</label>
    </div>
    @error('featured')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

{{-- SEO Section --}}
<div class="col-md-12 mt-4"><h5 class="fw-bold border-bottom pb-1">SEO Details</h5></div>

<div class="col-md-6">
    <label class="form-label">Meta Title</label>
    <input type="text" name="seo[title]" class="form-control @error('seo.title') is-invalid @enderror" value="{{ old('seo.title', $page->seo['title'] ?? '') }}">
</div>
@error('seo.title')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-6">
    <label class="form-label">Meta Keywords</label>
    <input type="text" name="seo[keywords]" class="form-control @error('seo.keywords') is-invalid @enderror" value="{{ old('seo.keywords', $page->seo['keywords'] ?? '') }}">
</div>
@error('seo.keywords')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

<div class="col-md-12">
    <label class="form-label">Meta Description</label>
    <textarea name="seo[description]" rows="3" class="form-control @error('seo.description') is-invalid @enderror">{{ old('seo.description', $page->seo['description'] ?? '') }}</textarea>
</div>
@error('seo.description')
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror

{{-- Custom Fields --}}
@if($customFields->isNotEmpty())
    <div class="col-md-12 mt-4">
        <h5 class="fw-bold border-bottom pb-1">Custom Fields</h5>
    </div>

    @foreach($customFields as $field)
        @php
            $pageId = $page->id ?? 0;
            $fieldValue = old("custom_fields.{$field->key}", $field->getValueByKey($field->key, $pageId ?? 0, 'cms') ?? '');
            if($field->type === "checkbox"){
                $fieldValue = old("custom_fields.{$field->key}", json_decode($field->getValueByKey($field->key, $pageId, 'cms') ?? '', true) ?? '');
            }
            $options = !empty($field->options) ? explode(',', $field->options) : [];
            $colWidth = in_array($field->type, ['textarea', 'editor']) ? 'col-md-12' : 'col-md-6';
        @endphp

        <div class="{{ $colWidth }} mb-3">
            <label class="form-label d-block">{{ $field->label }}</label>

            @if($field->type == 'text')
                <input type="text" name="custom_fields[{{ $field->key }}]" class="form-control {{ $field->class }} @error('custom_fields.' . $field->key) is-invalid @enderror" value="{{ $fieldValue }}">

            @elseif($field->type == 'number')
                <input type="number" name="custom_fields[{{ $field->key }}]"
                    class="form-control {{ $field->class }} @error('custom_fields.' . $field->key) is-invalid @enderror"
                    value="{{ $fieldValue }}">

            @elseif($field->type == 'email')
                <input type="email" name="custom_fields[{{ $field->key }}]"
                    class="form-control {{ $field->class }} @error('custom_fields.' . $field->key) is-invalid @enderror"
                    value="{{ $fieldValue }}">

            @elseif($field->type == 'phone')
                <input type="tel" name="custom_fields[{{ $field->key }}]"
                    class="form-control {{ $field->class }} @error('custom_fields.' . $field->key) is-invalid @enderror"
                    value="{{ $fieldValue }}">

            @elseif($field->type == 'textarea')
                <textarea name="custom_fields[{{ $field->key }}]" class="form-control {{ $field->class }} @error('custom_fields.' . $field->key) is-invalid @enderror">{{ $fieldValue }}</textarea>

            @elseif($field->type == 'editor')
                <textarea id="editor-{{ $field->key }}" name="custom_fields[{{ $field->key }}]" class="form-control {{ $field->class }} @error('custom_fields.' . $field->key) is-invalid @enderror">{{ $fieldValue }}</textarea>

            @elseif($field->type == 'select')
                <select name="custom_fields[{{ $field->key }}]" class="form-select {{ $field->class }} @error('custom_fields.' . $field->key) is-invalid @enderror">
                    <option value="">Please Select</option>
                    @foreach($options ?? [] as $option)
                        <option value="{{ $option }}" {{ $fieldValue == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>

            @elseif($field->type == 'checkbox')
                @foreach($options ?? [] as $option)
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="custom_fields[{{ $field->key }}][]" value="{{ $option }}"
                            {{ is_array($fieldValue) && in_array($option, $fieldValue) ? 'checked' : '' }}
                            class="form-check-input @error('custom_fields.' . $field->key) is-invalid @enderror">
                        <label class="form-check-label">{{ $option }}</label>
                    </div>
                @endforeach

            @elseif($field->type == 'radio')
                @foreach($options ?? [] as $option)
                    <div class="form-check form-check-inline mt-4">
                        <input type="radio" name="custom_fields[{{ $field->key }}]" value="{{ $option }}"
                            {{ $fieldValue == $option ? 'checked' : '' }}
                            class="form-check-input @error('custom_fields.' . $field->key) is-invalid @enderror">
                        <label class="form-check-label">{{ $option }}</label>
                    </div>
                @endforeach

            @elseif($field->type == 'file')
                <input type="file" name="custom_fields[{{ $field->key }}]" class="form-control {{ $field->class }} @error('custom_fields.' . $field->key) is-invalid @enderror">
                @if (!empty($fieldValue))
                    <div id="custom-file-preview-{{ $field->key }}">
                        @php
                            $filePath = 'storage/' . $fieldValue;
                            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                        @endphp

                        @if(in_array($extension, $imageExtensions))
                            <img src="{{ asset($filePath) }}" alt="Image Preview" style="max-width: 200px; height: auto;" class="mb-2">
                        @else
                            <a href="{{ asset($filePath) }}" target="_blank">View File</a>
                        @endif
                        <button type="button" onclick="deleteCustomFile('{{ $field->key }}')" class="btn btn-danger btn-sm ms-2">Delete</button>
                    </div>
                    <input type="hidden" name="custom_fields[delete_custom_field_{{ $field->key }}]" id="delete_custom_field_{{ $field->key }}" value="0">
                @endif
            @endif
            @error('custom_fields.' . $field->key)
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
@endif