@extends('frontend.layouts.app')

@section('content')
<section class="section-bg layout-pt-lg layout-pb-lg">
<section class="section-bg layout-pt-lg layout-pb-lg">
    <img src="{{ $page->banner ? asset('storage/'.$page->banner) : asset('frontend/img/pages/about/1.png') }}" alt="about-us-banner-image">
  </div>
  <div class="container">
    <div class="row justify-center text-center">
      <div class="col-xl-6 col-lg-8 col-md-10">
      <div class="col-xl-6 col-lg-8 col-md-10">
        <h1 class="text-40 md:text-25 fw-600 text-white">{{ $page->heading ?? '' }}</h1>
        <div class="text-white mt-15">{{ $page->brief ?? '' }}</div>
      </div>
    </div>
  </div>
  </section>

<section class="layout-pt-lg layout-pb-md">
  <div class="container">
    <div class="row justify-center text-center">
      <div class="col-auto">
        <div class="sectionTitle -md">
          <h2 class="sectionTitle__title">Why Choose Us</h2>
          <p class="sectionTitle__text mt-5 sm:mt-0">These popular destinations have a lot to offer</p>
        </div>
      </div>
    </div>
    <div class="row y-gap-40 justify-between pt-50">
      @php
        $featuresRaw = $page->custom_fields['features'] ?? null;
        $features = [];
        if (is_string($featuresRaw) && strlen($featuresRaw)) {
          $decoded = json_decode($featuresRaw, true);
          $features = is_array($decoded) ? $decoded : [];
        }

      @endphp
      @foreach($features as $f)
      <div class="col-lg-3 col-sm-6">
        <div class="featureIcon -type-1">
          <div class="d-flex justify-center">
            <img src="{{ \Illuminate\Support\Str::startsWith($f['icon'] ?? '', ['http://','https://']) ? $f['icon'] : asset($f['icon'] ?? 'frontend/img/featureIcons/1/1.svg') }}" alt="{{ $f['title'] ?? '' }}" class="js-lazy">
          </div>
          <div class="text-center mt-30">
            <h4 class="text-18 fw-500">{{ $f['title'] ?? '' }}</h4>
            <p class="text-15 mt-10">{{ $f['desc'] ?? '' }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section class="layout-pt-md">
  <div class="container">
    <div class="row y-gap-30 justify-between items-center">
      <div class="col-lg-5">
        <div class="text-dark-1 mt-60 lg:mt-40 md:mt-20">
          {!! $page->description !!}
        </div>
      </div>
      <div class="col-lg-6">
        <img src="{{ $page->page_image ? asset('storage/'.$page->page_image) : asset('frontend/img/pages/about/2.png') }}" alt="image" class="rounded-4">
      </div>
    </div>
  </div>
</section>

<section class="pt-60">
  <div class="container">
    <div class="border-bottom-light pb-40">
      <div class="row y-gap-30 justify-center text-center">
        @php
          $countersRaw = $page->custom_fields['counters'] ?? null;
          $counters = [];
          if (is_string($countersRaw) && strlen($countersRaw)) {
            $decoded = json_decode($countersRaw, true);
            $counters = is_array($decoded) ? $decoded : [];
          }

        @endphp
        @foreach($counters as $c)
        <div class="col-xl-3 col-6">
          <div class="text-40 lg:text-30 lh-13 fw-600">{{ $c['value'] ?? '' }}</div>
          <div class="text-14 lh-14 text-light-1 mt-5">{{ $c['label'] ?? '' }}</div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

@php
  $teamRaw = $page->custom_fields['team_members'] ?? null;
  $team = [];
  if (is_string($teamRaw) && strlen($teamRaw)) {
    $decodedT = json_decode($teamRaw, true);
    $team = is_array($decodedT) ? $decodedT : [];
  }
@endphp
@if(count($team))
  <section class="layout-pt-lg layout-pb-lg">
    <div class="container">
      <div class="row y-gap-20 justify-between items-end">
        <div class="col-auto">
          <div class="sectionTitle -md">
            <h2 class="sectionTitle__title">Our Team</h2>
            <!-- <p class="sectionTitle__text mt-5 sm:mt-0">Lorem ipsum dolor sit amet</p> -->
          </div>
        </div>
        <div class="col-auto">
          <div class="d-flex x-gap-15 items-center justify-center">
            <div class="col-auto">
              <button class="d-flex items-center text-24 arrow-left-hover js-team-prev">
                <i class="icon icon-arrow-left"></i>
              </button>
            </div>
            <div class="col-auto">
              <div class="pagination -dots text-border js-team-pag"></div>
            </div>
            <div class="col-auto">
              <button class="d-flex items-center text-24 arrow-right-hover js-team-next">
                <i class="icon icon-arrow-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="overflow-hidden pt-40 js-section-slider" data-gap="30" data-slider-cols="xl-5 lg-4 md-2 sm-2 base-1" data-nav-prev="js-team-prev" data-pagination="js-team-pag" data-nav-next="js-team-next">
        <div class="swiper-wrapper">
          @foreach($team as $m)
          <div class="swiper-slide">
            <div>
              <img src="{{ \Illuminate\Support\Str::startsWith($m['image'] ?? '', ['http://','https://']) ? $m['image'] : asset($m['image'] ?? 'frontend/img/team/1.png') }}" alt="image" class="rounded-4 col-12">
              <div class="mt-10">
                <div class="text-18 lh-15 fw-500">{{ $m['name'] ?? '' }}</div>
                <div class="text-14 lh-15">{{ $m['role'] ?? '' }}</div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>
@endif

@if(count($testimonials ?? []) > 0)
<section class="section-bg layout-pt-lg layout-pb-lg">
  <div class="section-bg__item -mx-20 bg-light-2"></div>
  <div class="container">
    <div class="row justify-center text-center">
      <div class="col-auto">
        <div class="sectionTitle -md">
          <h2 class="sectionTitle__title">Overheard from travelers</h2>
          <p class="sectionTitle__text mt-5 sm:mt-0">These popular destinations have a lot to offer</p>
        </div>
      </div>
    </div>
    <div class="overflow-hidden pt-80 js-section-slider" data-gap="30" data-slider-cols="xl-3 lg-3 md-2 sm-1 base-1">
      <div class="swiper-wrapper">
        @foreach(($testimonials ?? collect()) as $story)
        <div class="swiper-slide">
          <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40">
            <div class="rounded-4 overflow-hidden" style="aspect-ratio: 3 / 2;">
              <img src="{{ $story->image_url }}" alt="image" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div class="pt-20"></div>
            <h4 class="text-16 fw-500 text-blue-1 mb-20">{{ $story->title }}</h4>
            <p class="testimonials__text lh-18 fw-500 text-dark-1">{{ \Illuminate\Support\Str::limit(strip_tags($story->description), 200) }}</p>
            <div class="pt-20 mt-28 border-top-light">
              <div class="row x-gap-20 y-gap-20 items-center">
                <div class="col-auto">
                  <div class="text-15 fw-500 lh-14">{{ $story->title }}</div>
                  <div class="text-14 lh-14 text-light-1 mt-5">{{ $story->brief }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

@php
  $nl = \App\Helpers\CustomHelper::getSettings(['about_newsletter_title','about_newsletter_subtitle']);
  $nlTitle = $nl['about_newsletter_title'] ?? 'Your Travel Journey Starts Here';
  $nlSubtitle = $nl['about_newsletter_subtitle'] ?? "Sign up and we'll send the best deals to you";
@endphp
<section class="layout-pt-md layout-pb-md bg-dark-2">
  <div class="container">
    <div class="row y-gap-30 justify-between items-center">
      <div class="col-auto">
        <div class="row y-gap-20 flex-wrap items-center">
          <div class="col-auto">
            <div class="icon-newsletter text-60 sm:text-40 text-white"></div>
          </div>
          <div class="col-auto">
            <h4 class="text-26 text-white fw-600">{{ $nlTitle }}</h4>
            <div class="text-white">{{ $nlSubtitle }}</div>
          </div>
        </div>
      </div>
      <div class="col-auto">
        <form id="aboutPageNewsletterForm" action="{{ route('newsletter.subscribe') }}" method="post" class="single-field -w-410 d-flex x-gap-10 y-gap-20">
          @csrf
          <div><input class="bg-white h-60" type="email" name="email" required placeholder="Your Email"></div>
          <div><button class="button -md h-60 bg-blue-1 text-white subscribe-btn" type="submit">Subscribe</button></div>
        </form>
        <div id="aboutPageNewsletterMsg" class="mt-10"></div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var nlForm = document.getElementById('aboutPageNewsletterForm');
  var nlMsg = document.getElementById('aboutPageNewsletterMsg');
  if (nlForm) {
    nlForm.addEventListener('submit', function(e) {
      e.preventDefault();
      nlMsg.className = '';
      nlMsg.textContent = '';
      var btn = nlForm.querySelector('.subscribe-btn');
      var originalBtnHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Subscribing...';
      var formData = new FormData(nlForm);
      fetch(nlForm.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData })
        .then(function(r){ return r.json().then(function(res){ return { status: r.status, data: res }; }); })
        .then(function(payload){
          var res = payload.data;
          if (res.success) {
            nlMsg.className = 'alert alert-success';
            nlMsg.textContent = res.message || 'Thank you for subscribing!';
            nlForm.reset();
          } else {
            nlMsg.className = 'alert alert-danger';
            var text = res.message || 'Validation failed';
            if (res.errors) {
              var list = Object.values(res.errors).flat().join(' ');
              text = list || text;
            }
            nlMsg.textContent = text;
          }
        })
        .catch(function(){
          nlMsg.className = 'alert alert-danger';
          nlMsg.textContent = 'Sorry, there was an error subscribing. Please try again.';
        })
        .finally(function(){
          btn.disabled = false;
          btn.innerHTML = originalBtnHtml;
        });
    });
  }
});
</script>
@endpush
