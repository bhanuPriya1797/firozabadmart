@php
    use App\Helpers\CustomHelper;
    $industriesTitle = CustomHelper::getSetting('home_industries_section_title');
    $industriesItems = json_decode(CustomHelper::getSetting('home_industries_items') ?: '[]', true) ?: [];
    $whatTitle = CustomHelper::getSetting('home_whatwedo_section_title');
    $whatItems = json_decode(CustomHelper::getSetting('home_whatwedo_items') ?: '[]', true) ?: [];

    $aboutTitle = CustomHelper::getSetting('home_about_title');
    $aboutBrief = CustomHelper::getSetting('home_about_brief');
    $aboutImgTop = CustomHelper::getSetting('home_about_image_top');
    $aboutImgBottom = CustomHelper::getSetting('home_about_image_bottom');

    $experienceItems = json_decode(CustomHelper::getSetting('home_experience_cards') ?: '[]', true) ?: [];
    $whyHeading = CustomHelper::getSetting('home_why_heading');
    $whyStatsItems = json_decode(CustomHelper::getSetting('home_why_stats') ?: '[]', true) ?: [];
    $processItems = json_decode(CustomHelper::getSetting('home_process_steps') ?: '[]', true) ?: [];
    $techFeaturesItems = json_decode(CustomHelper::getSetting('home_tech_features') ?: '[]', true) ?: [];
    $techBarsItems = json_decode(CustomHelper::getSetting('home_tech_index_bars') ?: '[]', true) ?: [];
    $trustItems = json_decode(CustomHelper::getSetting('home_trust_quality_cards') ?: '[]', true) ?: [];
@endphp

@php
    $marqueeRaw = CustomHelper::getSetting('home_marquee_items');
    $decoded = json_decode($marqueeRaw ?? '[]', true);
    if (is_array($decoded) && !empty($decoded)) {
        $marqueeItems = $decoded;
    } else {
        $fallback = $marqueeRaw ?? '';
        $parts = preg_split('/(\r\n|\r|\n|•|\|\,|,)/', $fallback) ?: [];
        $marqueeItems = array_values(array_filter(array_map('trim', $parts), function($v){ return $v !== ''; }));
    }
    $aboutReadMore = CustomHelper::getSetting('home_about_readmore_link');
    $joinTitle = CustomHelper::getSetting('home_join_title');
    $joinSubtitle = CustomHelper::getSetting('home_join_subtitle');
    $joinDesc = CustomHelper::getSetting('home_join_description');
    $joinImage = CustomHelper::getSetting('home_join_image');
    $joinLink = CustomHelper::getSetting('home_join_link');
@endphp

<div class="row g-4">
  <div class="col-12">
    <h5 class="fw-bold border-bottom pb-1 mb-3">Marquee</h5>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Items</h6>
        <button type="button" class="btn btn-primary btn-sm" id="addMarqueeItem"><i class="ti tabler-plus me-1"></i> Add More</button>
      </div>
      <div class="card-body">
        <div id="marqueeRepeater" class="row g-2">
          @forelse($marqueeItems as $idx => $text)
            <div class="col-12 marquee-item d-flex align-items-center gap-2" data-index="{{ $idx }}">
              <input type="text" class="form-control marquee-text" value="{{ $text }}" placeholder="e.g., Championships 2025 ...">
              <button type="button" class="btn btn-outline-danger btn-sm removeMarquee"><i class="ti tabler-x"></i></button>
            </div>
          @empty
          @endforelse
        </div>
        <textarea name="home_marquee_items" id="home_marquee_items" class="d-none"></textarea>
      </div>
    </div>
  </div>

  <div class="col-12 mt-4">
    <h5 class="fw-bold border-bottom pb-1 mb-3">About Section</h5>
  </div>
  <div class="col-md-6">
    <label class="form-label">Title</label>
    <input type="text" name="home_about_title" class="form-control" value="{{ old('home_about_title', $aboutTitle) }}" placeholder="About section title">
  </div>
  <div class="col-md-6">
    <label class="form-label">Read More Link</label>
    <input type="text" name="home_about_readmore_link" class="form-control" value="{{ old('home_about_readmore_link', $aboutReadMore) }}" placeholder="/about">
  </div>
  <div class="col-12">
    <label class="form-label">Description</label>
    <textarea name="home_about_brief" id="home_about_brief" class="form-control" rows="5" placeholder="Short description...">{{ old('home_about_brief', $aboutBrief) }}</textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label">Big Image</label>
    @php $atop = $aboutImgTop ?? ''; $atopUrl = $atop ? asset('storage/'.$atop) : ''; @endphp
    <div class="d-flex align-items-start gap-3">
      <div class="preview-box border rounded p-2" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
        <img src="{{ $atopUrl }}" id="home_about_image_top_preview" class="img-fluid {{ $atopUrl ? '' : 'd-none' }}" style="max-height: 100%; max-width: 100%;">
      </div>
      <div>
        <button type="button" class="btn btn-primary mb-2" onclick="openMediaManager('home_about_image_top')">
          <i class="ti tabler-photo me-1"></i> Choose From Library
        </button>
        <button type="button" id="home_about_image_top_remove" class="btn btn-label-danger mb-2 {{ $atopUrl ? '' : 'd-none' }}" onclick="clearMediaPreview('home_about_image_top')">
          <i class="ti tabler-trash me-1"></i> Remove
        </button>
        <input type="hidden" id="home_about_image_top" name="home_about_image_top" value="{{ old('home_about_image_top', $aboutImgTop) }}">
        @if(!empty($atopUrl))
          <div class="form-check mt-1">
            <input type="checkbox" class="form-check-input" id="home_about_image_top_delete" name="home_about_image_top_delete" value="1">
            <label class="form-check-label" for="home_about_image_top_delete">Delete saved image</label>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <label class="form-label">Small Image</label>
    @php $abtm = $aboutImgBottom ?? ''; $abtmUrl = $abtm ? asset('storage/'.$abtm) : ''; @endphp
    <div class="d-flex align-items-start gap-3">
      <div class="preview-box border rounded p-2" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
        <img src="{{ $abtmUrl }}" id="home_about_image_bottom_preview" class="img-fluid {{ $abtmUrl ? '' : 'd-none' }}" style="max-height: 100%; max-width: 100%;">
      </div>
      <div>
        <button type="button" class="btn btn-primary mb-2" onclick="openMediaManager('home_about_image_bottom')">
          <i class="ti tabler-photo me-1"></i> Choose From Library
        </button>
        <button type="button" id="home_about_image_bottom_remove" class="btn btn-label-danger mb-2 {{ $abtmUrl ? '' : 'd-none' }}" onclick="clearMediaPreview('home_about_image_bottom')">
          <i class="ti tabler-trash me-1"></i> Remove
        </button>
        <input type="hidden" id="home_about_image_bottom" name="home_about_image_bottom" value="{{ old('home_about_image_bottom', $aboutImgBottom) }}">
        @if(!empty($abtmUrl))
          <div class="form-check mt-1">
            <input type="checkbox" class="form-check-input" id="home_about_image_bottom_delete" name="home_about_image_bottom_delete" value="1">
            <label class="form-check-label" for="home_about_image_bottom_delete">Delete saved image</label>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 mt-4">
    <h5 class="fw-bold border-bottom pb-1 mb-3">Join Our League</h5>
  </div>
  <div class="col-md-6">
    <label class="form-label">Title</label>
    <input type="text" name="home_join_title" class="form-control" value="{{ old('home_join_title', $joinTitle) }}" placeholder="Join Our Leagues">
  </div>
  <div class="col-md-6">
    <label class="form-label">Subtitle</label>
    <input type="text" name="home_join_subtitle" class="form-control" value="{{ old('home_join_subtitle', $joinSubtitle) }}" placeholder="CDAA Archery Club">
  </div>
  <div class="col-12">
    <label class="form-label">Description</label>
    <textarea name="home_join_description" class="form-control" rows="5" placeholder="Short description">{{ old('home_join_description', $joinDesc) }}</textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label">Image</label>
    @php $jimg = $joinImage ?? ''; $jurl = $jimg ? asset('storage/'.$jimg) : ''; @endphp
    <div class="d-flex align-items-start gap-3">
      <div class="preview-box border rounded p-2" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
        <img src="{{ $jurl }}" id="home_join_image_preview" class="img-fluid {{ $jurl ? '' : 'd-none' }}" style="max-height: 100%; max-width: 100%;">
      </div>
      <div>
        <button type="button" class="btn btn-primary mb-2" onclick="openMediaManager('home_join_image')">
          <i class="ti tabler-photo me-1"></i> Choose From Library
        </button>
        <button type="button" id="home_join_image_remove" class="btn btn-label-danger mb-2 {{ $jurl ? '' : 'd-none' }}" onclick="clearMediaPreview('home_join_image')">
          <i class="ti tabler-trash me-1"></i> Remove
        </button>
        <input type="hidden" id="home_join_image" name="home_join_image" value="{{ old('home_join_image', $joinImage) }}">
        @if(!empty($jurl))
          <div class="form-check mt-1">
            <input type="checkbox" class="form-check-input" id="home_join_image_delete" name="home_join_image_delete" value="1">
            <label class="form-check-label" for="home_join_image_delete">Delete saved image</label>
          </div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <label class="form-label">Join Link</label>
    <input type="text" name="home_join_link" class="form-control" value="{{ old('home_join_link', $joinLink) }}" placeholder="/register">
  </div>
  <div class="col-md-6">
    <label class="form-label">Button Text</label>
    @php $joinBtn = CustomHelper::getSetting('home_join_button_text'); @endphp
    <input type="text" name="home_join_button_text" class="form-control" value="{{ old('home_join_button_text', $joinBtn) }}" placeholder="Become a Member">
  </div>

  <div class="col-12 mt-4">
    <h5 class="fw-bold border-bottom pb-1 mb-3">Become a Member (CTA)</h5>
  </div>
  @php
    $memberTitle = CustomHelper::getSetting('home_member_title');
    $memberDesc = CustomHelper::getSetting('home_member_description');
    $memberBtnText = CustomHelper::getSetting('home_member_button_text');
    $memberBtnLink = CustomHelper::getSetting('home_member_button_link');
    $memberImage = CustomHelper::getSetting('home_member_image');
    $memberImgUrl = $memberImage ? asset('storage/'.$memberImage) : '';
  @endphp
  <div class="col-md-6">
    <label class="form-label">Section Title</label>
    <input type="text" name="home_member_title" class="form-control" value="{{ old('home_member_title', $memberTitle) }}" placeholder="CDAA Archery Club">
  </div>
  <div class="col-md-6">
    <label class="form-label">Button Text</label>
    <input type="text" name="home_member_button_text" class="form-control" value="{{ old('home_member_button_text', $memberBtnText) }}" placeholder="Become a Member">
  </div>
  <div class="col-12">
    <label class="form-label">Description</label>
    <textarea name="home_member_description" class="form-control" rows="4" placeholder="Short description">{{ old('home_member_description', $memberDesc) }}</textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label">Button Link</label>
    <input type="text" name="home_member_button_link" class="form-control" value="{{ old('home_member_button_link', $memberBtnLink) }}" placeholder="/register">
  </div>
  <div class="col-md-6">
    <label class="form-label">Side Image</label>
    <div class="d-flex align-items-start gap-3">
      <div class="preview-box border rounded p-2" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
        <img src="{{ $memberImgUrl }}" id="home_member_image_preview" class="img-fluid {{ $memberImgUrl ? '' : 'd-none' }}" style="max-height: 100%; max-width: 100%;">
      </div>
      <div>
        <button type="button" class="btn btn-primary mb-2" onclick="openMediaManager('home_member_image')">
          <i class="ti tabler-photo me-1"></i> Choose From Library
        </button>
        <button type="button" id="home_member_image_remove" class="btn btn-label-danger mb-2 {{ $memberImgUrl ? '' : 'd-none' }}" onclick="clearMediaPreview('home_member_image')">
          <i class="ti tabler-trash me-1"></i> Remove
        </button>
        <input type="hidden" id="home_member_image" name="home_member_image" value="{{ old('home_member_image', $memberImage) }}">
        @if(!empty($memberImgUrl))
          <div class="form-check mt-1">
            <input type="checkbox" class="form-check-input" id="home_member_image_delete" name="home_member_image_delete" value="1">
            <label class="form-check-label" for="home_member_image_delete">Delete saved image</label>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 mt-4">
    <h5 class="fw-bold border-bottom pb-1 mb-3">Partners & Sponsors</h5>
  </div>
  @php
    $partnersTitle = CustomHelper::getSetting('home_partners_title');
    $partnersDesc = CustomHelper::getSetting('home_partners_description');
  @endphp
  <div class="col-md-6">
    <label class="form-label">Section Title</label>
    <input type="text" name="home_partners_title" class="form-control" value="{{ old('home_partners_title', $partnersTitle) }}" placeholder="Our Partner & Sponsors">
  </div>
  <div class="col-12">
    <label class="form-label">Description</label>
    <textarea name="home_partners_description" class="form-control" rows="6" placeholder="Section description...">{{ old('home_partners_description', $partnersDesc) }}</textarea>
  </div>
</div>

<script src="{{ asset('admin/assets/js/media-selector.js') }}"></script>
<script>
  (function(){
    function serializeMarquee() {
      const items = [];
      document.querySelectorAll('#marqueeRepeater .marquee-item .marquee-text').forEach(function(inp){
        const v = inp.value.trim();
        if (v) items.push(v);
      });
      const target = document.getElementById('home_marquee_items');
      if (target) target.value = JSON.stringify(items);
    }
    document.getElementById('addMarqueeItem')?.addEventListener('click', function(){
      const tpl = `<div class="col-12 marquee-item d-flex align-items-center gap-2">
        <input type="text" class="form-control marquee-text" placeholder="e.g., Championships 2025 ...">
        <button type="button" class="btn btn-outline-danger btn-sm removeMarquee"><i class="ti tabler-x"></i></button>
      </div>`;
      document.getElementById('marqueeRepeater')?.insertAdjacentHTML('beforeend', tpl);
    });
    document.getElementById('marqueeRepeater')?.addEventListener('click', function(e){
      if (e.target.closest('.removeMarquee')) {
        e.target.closest('.marquee-item').remove();
      }
    });
    const form = document.getElementById('homepageSettingsForm');
    if (form) {
      form.addEventListener('submit', function(){
        serializeMarquee();
      });
    }
  })();
</script>

<script src="{{ asset('admin/assets/ckeditor/ckeditor.js') }}"></script>
<script>
  (function(){
    if (window.CKEDITOR) {
      try {
        CKEDITOR.replace('home_about_brief', {
          filebrowserImageUploadUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_upload", ["_token" => csrf_token()]) }}',
          filebrowserUploadMethod: 'form',
          filebrowserBrowseUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_browse") }}',
          filebrowserImageBrowseUrl: '{{ route(CustomHelper::getAdminRouteName() . ".ck_browse") }}',
          extraPlugins: 'filebrowser'
        });
      } catch(e){}
    }
  })();
</script>
