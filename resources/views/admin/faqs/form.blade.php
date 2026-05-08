@component('admin.layouts.main')
@slot('title') {{ $page_heading }} @endslot
@php $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName(); @endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $page_heading }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route($ADMIN_ROUTE_NAME.'.faqs.add') }}">
                        @csrf
                        @if(isset($faq->id))
                            <input type="hidden" name="id" value="{{ \App\Helpers\CustomHelper::encrypt($faq->id) }}">
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('question') is-invalid @enderror" 
                                           id="question" name="question" value="{{ old('question', $faq->question ?? '') }}" 
                                           placeholder="Enter question" required>
                                    @error('question')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $id => $name)
                                            <option value="{{ $id }}" {{ old('category_id', $faq->category_id ?? '') == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="answer" class="form-label">Answer <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('answer') is-invalid @enderror" 
                                              id="answer" name="answer" rows="6" 
                                              placeholder="Enter answer">{{ old('answer', $faq->answer ?? '') }}</textarea>
                                    @error('answer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" 
                                           placeholder="0" min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="1" {{ old('status', $faq->status ?? '') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $faq->status ?? '') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Assign to Page Type</label>
                                @php $selectedType = old('page_type', $faq->page_type ?? ''); @endphp
                                <select name="page_type" class="form-select" id="pageTypeSelect">
                                    <option value="">Select Page Type</option>
                                    <option value="service" {{ $selectedType === 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="blog" {{ $selectedType === 'blog' ? 'selected' : '' }}>Blog</option>
                                    <option value="category" {{ $selectedType === 'category' ? 'selected' : '' }}>Category</option>
                                    <option value="cms" {{ $selectedType === 'cms' ? 'selected' : '' }}>CMS Page</option>
                                </select>
                                @error('page_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6" id="pageSelectWrapper" style="{{ $selectedType ? '' : 'display: none;' }}">
                            <div class="mb-3">
                                <label class="form-label">Select Page</label>
                                @php $selectedPageIds = old('page_ids', isset($faq->page_id) ? [$faq->page_id] : []); @endphp
                                <select name="page_ids[]" class="form-select" id="pageSelect" multiple>
                                    <option value="">Select Page</option>
                                </select>
                                <small class="text-muted">Hold Ctrl/Command to select multiple pages. Select "All CMS Pages" to apply site‑wide.</small>
                                @error('page_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route($ADMIN_ROUTE_NAME.'.faqs.index') }}" class="btn btn-secondary">
                                    <i class="ti tabler-arrow-left me-1"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti tabler-device-floppy me-1"></i> Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@slot('headerBlock')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endslot

@slot('footerBlock')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
<script>
    // Initialize CKEditor for answer field
    ClassicEditor
        .create(document.querySelector('#answer'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent'],
            placeholder: 'Enter answer...'
        })
        .catch(error => {
            console.error(error);
        });

    $(function(){
        const ADMIN_ROUTE_NAME = @json($ADMIN_ROUTE_NAME);
        const selectedType = @json(old('page_type', $faq->page_type ?? ''));
        const selectedPageIds = @json($selectedPageIds);

        $('#pageSelect').select2({ placeholder: 'Select Page(s)', allowClear: true, width: '100%' });

        function loadPages(type) {
            if (!type) return;
            $('#pageSelectWrapper').show();
            $('#pageSelect').html('<option value="">Loading...</option>');
            const url = @json(route($ADMIN_ROUTE_NAME . '.faqs.ajax_pages'));
            $.get(url, { type }, function(resp){
                if (resp && resp.success) {
                    const opts = [];
                    (resp.options || []).forEach(function(o){
                        const sel = (selectedPageIds || []).map(String).includes(String(o.id)) ? ' selected' : '';
                        opts.push('<option value="' + o.id + '"' + sel + '>' + o.label + '</option>');
                    });
                    $('#pageSelect').html(opts.join(''));
                    $('#pageSelect').trigger('change');
                } else {
                    $('#pageSelect').html('<option value="">No pages found</option>');
                }
            }, 'json').fail(function(){
                $('#pageSelect').html('<option value="">Failed to load</option>');
            });
        }

        $('#pageTypeSelect').on('change', function(){
            const type = $(this).val();
            if (!type) {
                $('#pageSelectWrapper').hide();
                $('#pageSelect').html('<option value="">Select Page</option>');
            } else {
                loadPages(type);
            }
        });

        if (selectedType) {
            loadPages(selectedType);
        }
    });
</script>
@endslot
@endcomponent
















