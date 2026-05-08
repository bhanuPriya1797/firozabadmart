@extends('frontend.layouts.app')

@section('content')

<div class="ratio ratio-16:9 contact-map">
    <div class="map-ratio">
        <div class="map js-map-single" data-address="{{ $additionalData['settings']['site_address'] ?? '' }}"></div>
    </div>
</div>

<section>
    <div class="relative container">
        <div class="row justify-end">
            <div class="col-xl-5 col-lg-7">
                <div class="map-form px-40 pt-40 pb-50 lg:px-30 lg:py-30 md:px-24 md:py-24 bg-white rounded-4 shadow-4">
                    <div class="text-22 fw-500">
                        Send a message
                    </div>
                    <form id="contactForm" method="post" action="{{ route('contact.submit') }}" class="pt-20">
                        @csrf
                        <div id="contactMsg" class="mb-3"></div>
                        <div class="row y-gap-20">
                            <div class="col-12">
                                <div class="form-input">
                                    <input type="text" name="name" required>
                                    <label class="lh-1 text-16 text-light-1">Full Name</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-input">
                                    <input type="email" name="contact_email" required>
                                    <label class="lh-1 text-16 text-light-1">Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-input">
                                    <input type="text" name="phone" required>
                                    <label class="lh-1 text-16 text-light-1">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-input">
                                    <select name="country" class="form-select">
                                        <option value="">Select Country</option>
                                        @foreach(($additionalData['countries'] ?? []) as $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-input">
                                    <textarea name="comment" rows="4" required></textarea>
                                    <label class="lh-1 text-16 text-light-1">Your Messages</label>
                                </div>
                            </div>
                            <div class="col-auto">
                        <button class="button px-24 h-50 -dark-1 bg-blue-1 text-white submit-btn" type="submit">
                            Send a Messsage <div class="icon-arrow-top-right ml-15"></div>
                        </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="layout-pt-md layout-pb-lg">
    <div class="container">
        <div class="row x-gap-80 y-gap-20 justify-between">
            <div class="col-12">
                <div class="text-30 sm:text-24 fw-600">Contact Us</div>
            </div>
            <div class="col-lg-3">
                <div class="text-14 text-light-1">Address</div>
                <div class="text-18 fw-500 mt-10">{{ $additionalData['settings']['site_address'] ?? '' }}</div>
            </div>
            <div class="col-auto">
                <div class="text-14 text-light-1">Toll Free Customer Care</div>
                <div class="text-18 fw-500 mt-10">{{ $additionalData['settings']['contact_phone'] ?? '' }}</div>
            </div>
            <div class="col-auto">
                <div class="text-14 text-light-1">Need live support?</div>
                <div class="text-18 fw-500 mt-10">{{ $additionalData['settings']['contact_email'] ?? '' }}</div>
            </div>
            <div class="col-auto">
                <div class="text-14 text-light-1">Follow us on social media</div>
                <div class="d-flex x-gap-20 items-center mt-10">
                    @if(!empty($additionalData['settings']['facebook']))<a href="{{ $additionalData['settings']['facebook'] }}" target="_blank"><i class="icon-facebook text-14"></i></a>@endif
                    @if(!empty($additionalData['settings']['twitter']))<a href="{{ $additionalData['settings']['twitter'] }}" target="_blank"><i class="icon-twitter text-14"></i></a>@endif
                    @if(!empty($additionalData['settings']['instagram']))<a href="{{ $additionalData['settings']['instagram'] }}" target="_blank"><i class="icon-instagram text-14"></i></a>@endif
                    @if(!empty($additionalData['settings']['linkedin']))<a href="{{ $additionalData['settings']['linkedin'] }}" target="_blank"><i class="icon-linkedin text-14"></i></a>@endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="layout-pt-lg layout-pb-lg bg-blue-2">
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
            <div class="col-lg-3 col-sm-6">
                <div class="featureIcon -type-1">
                    <div class="d-flex justify-center">
                        <img src="{{ asset('frontend/img/featureIcons/1/1.svg') }}" alt="Best Price Guarantee" class="js-lazy">
                    </div>
                    <div class="text-center mt-30">
                        <h4 class="text-18 fw-500">Best Price Guarantee</h4>
                        <p class="text-15 mt-10">We offer unbeatable prices across popular destinations.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="featureIcon -type-1">
                    <div class="d-flex justify-center">
                        <img src="{{ asset('frontend/img/featureIcons/1/2.svg') }}" alt="Easy & Quick Booking" class="js-lazy">
                    </div>
                    <div class="text-center mt-30">
                        <h4 class="text-18 fw-500">Easy & Quick Booking</h4>
                        <p class="text-15 mt-10">Book in minutes with our streamlined process.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="featureIcon -type-1">
                    <div class="d-flex justify-center">
                        <img src="{{ asset('frontend/img/featureIcons/1/3.svg') }}" alt="Customer Care 24/7" class="js-lazy">
                    </div>
                    <div class="text-center mt-30">
                        <h4 class="text-18 fw-500">Customer Care 24/7</h4>
                        <p class="text-15 mt-10">Get round-the-clock support from our team.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.contact-map { overflow: visible; }
.map-ratio { z-index: 1; }
.map-form { position: relative; z-index: 2; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ \App\Helpers\CustomHelper::getSetting('google_maps_api_key') ?: env('GOOGLE_MAPS_API_KEY') }}"></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer@2.6.2/dist/index.min.js"></script>
@endpush

    @php
      $nl2 = \App\Helpers\CustomHelper::getSettings(['contact_newsletter_title','contact_newsletter_subtitle']);
      $nl2Title = $nl2['contact_newsletter_title'] ?? 'Your Travel Journey Starts Here';
      $nl2Subtitle = $nl2['contact_newsletter_subtitle'] ?? "Sign up and we'll send the best deals to you";
    @endphp
    <section class="layout-pt-md layout-pb-md bg-dark-2">
      <div class="container">
        <div class="row y-gap-30 justify-between items-center">
          <div class="col-auto">
            <div class="row y-gap-20  flex-wrap items-center">
              <div class="col-auto">
                <div class="icon-newsletter text-60 sm:text-40 text-white"></div>
              </div>

              <div class="col-auto">
                <h4 class="text-26 text-white fw-600">{{ $nl2Title }}</h4>
                <div class="text-white">{{ $nl2Subtitle }}</div>
              </div>
            </div>
          </div>

          <div class="col-auto">
            <form id="contactPageNewsletterForm" action="{{ route('newsletter.subscribe') }}" method="post" class="single-field -w-410 d-flex x-gap-10 y-gap-20">
              @csrf
              <div><input class="bg-white h-60" type="email" name="email" required placeholder="Your Email"></div>
              <div><button class="button -md h-60 bg-blue-1 text-white subscribe-btn" type="submit">Subscribe</button></div>
            </form>
            <div id="contactPageNewsletterMsg" class="mt-10"></div>
          </div>
        </div>
      </div>
    </section>

@if(($faqs ?? collect())->count())
<section class="layout-pt-md layout-pb-md">
    <div class="container section-padding">
        <div class="row justify-content-center">
            <div class="faq-wrapper col-lg-10">
                <h3 class="fw-bold mb-3">Frequently Asked Questions</h3>
                <p>Answers to common questions.</p>
                @php $idx = 1; @endphp
                @foreach($faqs as $faq)
                    <div class="faq-item {{ $idx === 1 ? 'active' : '' }}">
                        <button class="faq-question"><span>{{ $idx }}. {{ $faq->question }}</span><div class="icon">{{ $idx === 1 ? '−' : '+' }}</div></button>
                        <div class="faq-answer" style="{{ $idx === 1 ? 'display: block;' : '' }}"><p>{!! $faq->answer !!}</p></div>
                    </div>
                    @php $idx++; @endphp
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@push('scripts')
<script>
(function(){
  var metaTokenEl = document.querySelector('meta[name="csrf-token"]');
  var CSRF = metaTokenEl ? metaTokenEl.getAttribute('content') : '{{ csrf_token() }}';
  function getCookie(name){var m=document.cookie.match(new RegExp('(?:^|; )'+name.replace(/([.$?*|{}()\\[\\]\\\\\\/\\+^])/g,'\\$1')+'=([^;]*)'));return m?decodeURIComponent(m[1]):'';}
  var XSRF = getCookie('XSRF-TOKEN');

  var topForm = document.getElementById('contactTopNewsletterForm');
  var topMsg = document.getElementById('contactTopNewsletterMsg');
  if (topForm) {
    topForm.addEventListener('submit', function(e) {
      e.preventDefault();
      topMsg.className = '';
      topMsg.textContent = '';
      var btn = topForm.querySelector('.subscribe-btn');
      var originalBtnHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Subscribing...';
      var formData = new FormData(topForm);
      fetch(topForm.action, { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }, body: formData })
        .then(function(r){ return r.json().then(function(res){ return { status: r.status, data: res }; }); })
        .then(function(payload){
          var res = payload.data;
          if (res.success) {
            topMsg.className = 'alert alert-success';
            topMsg.textContent = res.message || 'Thank you for subscribing!';
            topForm.reset();
          } else {
            topMsg.className = 'alert alert-danger';
            var text = res.message || 'Validation failed';
            if (res.errors) {
              var list = Object.values(res.errors).flat().join(' ');
              text = list || text;
            }
            topMsg.textContent = text;
          }
        })
        .catch(function(){
          topMsg.className = 'alert alert-danger';
          topMsg.textContent = 'Sorry, there was an error subscribing. Please try again.';
        })
        .finally(function(){
          btn.disabled = false;
          btn.innerHTML = originalBtnHtml;
        });
    });
  }

  var form = document.getElementById('contactForm');
  var msg = document.getElementById('contactMsg');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      msg.className = '';
      msg.textContent = '';
      var btn = form.querySelector('.submit-btn');
      var fields = form.querySelectorAll('input, select, textarea, button');
      fields.forEach(function(el){ el.disabled = true; });
      var originalBtnHtml = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...';
      var formData = new FormData(form);
      fetch(form.action, { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF, 'X-XSRF-TOKEN': XSRF, 'Accept': 'application/json' }, body: formData })
        .then(function(r){ return r.json().then(function(res){ return { status: r.status, data: res }; }); })
        .then(function(payload){
          var res = payload.data;
          if (res.success) {
            msg.className = 'alert alert-success';
            msg.textContent = res.message || 'Your message has been sent successfully!';
            form.reset();
          } else {
            msg.className = 'alert alert-danger';
            var text = res.message || 'Validation failed';
            if (res.errors) {
              var list = Object.values(res.errors).flat().join(' ');
              text = list || text;
            }
            msg.textContent = text;
          }
        })
        .catch(function(){
          msg.className = 'alert alert-danger';
          msg.textContent = 'Sorry, there was an error sending your message. Please try again.';
        })
        .finally(function(){
          btn.innerHTML = originalBtnHtml;
          fields.forEach(function(el){ el.disabled = false; });
        });
    });
  }

  var bottomForm = document.getElementById('contactPageNewsletterForm');
  var bottomMsg = document.getElementById('contactPageNewsletterMsg');
  if (bottomForm) {
    bottomForm.addEventListener('submit', function(e) {
      e.preventDefault();
      bottomMsg.className = '';
      bottomMsg.textContent = '';
      var btn = bottomForm.querySelector('.subscribe-btn');
      var originalBtnHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Subscribing...';
      var formData = new FormData(bottomForm);
      fetch(bottomForm.action, { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }, body: formData })
        .then(function(r){ return r.json().then(function(res){ return { status: r.status, data: res }; }); })
        .then(function(payload){
          var res = payload.data;
          if (res.success) {
            bottomMsg.className = 'alert alert-success';
            bottomMsg.textContent = res.message || 'Thank you for subscribing!';
            bottomForm.reset();
          } else {
            bottomMsg.className = 'alert alert-danger';
            var text = res.message || 'Validation failed';
            if (res.errors) {
              var list = Object.values(res.errors).flat().join(' ');
              text = list || text;
            }
            bottomMsg.textContent = text;
          }
        })
        .catch(function(){
          bottomMsg.className = 'alert alert-danger';
          bottomMsg.textContent = 'Sorry, there was an error subscribing. Please try again.';
        })
        .finally(function(){
          btn.disabled = false;
          btn.innerHTML = originalBtnHtml;
        });
    });
  }
})();
</script>
@endpush

@endsection
