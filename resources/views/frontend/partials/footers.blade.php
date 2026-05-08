@php
  $social = \App\Helpers\CustomHelper::getSettings(['facebook','instagram','twitter','linkedin','youtube','tiktok']);
@endphp
<footer class="footer -type-2 bg-light-2">
  <div class="container">
    <div class="pt-60 pb-60">
      <div class="row y-gap-40 justify-between xl:justify-start">
        <div class="col-xl-4 col-lg-6">
          <img src="{{ asset('frontend/img/general/logo.png') }}" alt="" style="max-width: 200px;">
          <div class="row y-gap-30 justify-between pt-30">
            <div class="col-sm-6">
              <div class="text-14">Toll Free Customer Care</div>
              <a href="#" class="text-18 fw-500 text-dark-1 mt-5">{{ \App\Helpers\CustomHelper::getSettings(['contact_phone'])['contact_phone'] ?? '' }}</a>
            </div>
            <div class="col-sm-5">
              <div class="text-14">Need live support?</div>
              <a href="#" class="text-18 fw-500 text-dark-1 mt-5">{{ \App\Helpers\CustomHelper::getSettings(['contact_email'])['contact_email'] ?? '' }}</a>
            </div>
          </div>
          <div class="mt-60">
            <h5 class="text-16 fw-500 mb-10">Follow us on social media</h5>
            <div class="d-flex x-gap-20 items-center">
              @if(!empty($social['facebook']))<a href="{{ $social['facebook'] }}" target="_blank" rel="noopener"><i class="icon-facebook text-14"></i></a>@endif
              @if(!empty($social['twitter']))<a href="{{ $social['twitter'] }}" target="_blank" rel="noopener"><i class="icon-twitter text-14"></i></a>@endif
              @if(!empty($social['instagram']))<a href="{{ $social['instagram'] }}" target="_blank" rel="noopener"><i class="icon-instagram text-14"></i></a>@endif
              @if(!empty($social['linkedin']))<a href="{{ $social['linkedin'] }}" target="_blank" rel="noopener"><i class="icon-linkedin text-14"></i></a>@endif
              @if(!empty($social['youtube']))<a href="{{ $social['youtube'] }}" target="_blank" rel="noopener"><i class="icon-youtube text-14"></i></a>@endif
              @if(!empty($social['tiktok']))<a href="{{ $social['tiktok'] }}" target="_blank" rel="noopener"><i class="icon-tiktok text-14"></i></a>@endif
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="mb-30">
            <h5 class="text-16 fw-500 mb-10">Get Updates & More</h5>
            <form id="footerNewsletterForm" action="{{ route('newsletter.subscribe') }}" method="post" class="single-field relative d-flex justify-end items-center">
              @csrf
              <input class="bg-white rounded-8 w-100" type="email" name="email" required placeholder="Your Email">
              <button class="absolute px-20 h-full text-15 fw-500 underline text-dark-1 subscribe-btn" type="submit">Subscribe</button>
            </form>
            <div id="footerNewsletterMsg" class="mt-10"></div>
          </div>
          <div class="row y-gap-30">
            <div class="col-lg-6 col-sm-6">
              <h5 class="text-16 fw-500 mb-30">Explore</h5>
              <div class="d-flex y-gap-10 flex-column">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/about') }}">About Us</a>
                <a href="">Upcoming Tours</a>
                <a href="">Blog</a>
              </div>
            </div>
            <div class="col-lg-6 col-sm-6">
              <h5 class="text-16 fw-500 mb-30">Support</h5>
              <div class="d-flex y-gap-10 flex-column">
                <a href="{{ url('/contact') }}">Contact</a>
                <a href="{{ url('/privacy-policy') }}">Privacy Policy</a>
                <a href="{{ url('/terms') }}">Terms and Conditions</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="py-20 border-top-light">
      <div class="row items-center y-gap-10">
        <div class="col-auto">
          <div class="row x-gap-30 y-gap-10">
            <div class="col-auto">
              <div class="text-center">
                © {{ date('Y') }} IKIGAI - Your Travel Genie.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var nlForm = document.getElementById('footerNewsletterForm');
  var nlMsg = document.getElementById('footerNewsletterMsg');
  if (!nlForm) return;
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
});
</script>
