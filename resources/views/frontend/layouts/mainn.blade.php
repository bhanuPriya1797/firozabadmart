<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="X-UA-Compatible" content="IE=edge">
	<title>Cuttack District Archery Association | Affiliated to Odisha Archery Association</title>
	
	<link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/bootstrap.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/style.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/responsive.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/owl.carousel.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/owl.theme.default.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/fontawesome.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/css/all.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('frontend/assets/lightboxed/lightboxed.css') }}">
	
    @yield('styles')
</head>
<body>
    @php
        $settings = \App\Helpers\CustomHelper::getSettings();
        $mainMenu = \App\Helpers\CustomHelper::getMenu('main-menu');
        $footerMenu = \App\Helpers\CustomHelper::getMenu('footer-menu');
        $mainMenuItems = $mainMenu ? $mainMenu->menuParentItems : collect();
        $footerMenuItems = $footerMenu ? $footerMenu->menuParentItems : collect();
    @endphp
<!-- top header area -->
      <section class="top_header">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                   @if(!empty($settings['contact_phone']))
                  <a href="tel:{{ $settings['contact_phone'] }}" class="btn call-btn rounded-pill"><i class="fas fa-phone"></i>{{ $settings['contact_phone'] }}</a>
                   @endif
               </div>
               <div class="col-lg-6 col-md-6 col-sm-6 col-6 text-end">
                  <a class="btn register-btn rounded-pill" href="{{ url('login') }}">Login</a>
                  <a class="btn login-btn rounded-pill me-2" href="{{ url('register') }}">Register</a>                  
               </div>
            </div>
         </div>
      </section>

 <!-- header logo area -->
      <section class="header-logo-area">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-lg-3 col-md-3 col-12 text-center text-lg-start">
                   @if(!empty($settings['logo_header']))
						<a href="{{ url('/') }}"><img src="{{ asset('storage/' . $settings['logo_header']) }}" class="img-fluid archer-logo" alt="logo"></a>
                        @else
                        <a href="{{ url('/') }}"><img src="{{ asset('frontend/assets/images/logo.png') }}" class="img-fluid archer-logo" alt="logo"></a>
                        @endif
               </div>
               <div class="col-lg-6 col-md-6 col-12">
                   @if(!empty($settings['middle_logo']))
						<a href="{{ url('/') }}"><img src="{{ asset('storage/' . $settings['middle_logo']) }}" class="img-fluid cuttack-logo" alt="logo"></a>
                        @else
                        <a href="{{ url('/') }}"></a>
                        @endif
               </div>
               <div class="col-lg-3 col-md-3 col-12 text-lg-end text-center">
                  <div class="social-icon-list">
                     <ul>
                         @if(!empty($settings['facebook']))
							<li><a href="{{ $settings['facebook'] }}" target="_blank"><i class="fab fa-facebook-f fb-color"></i></a></li>
                            @endif
                            @if(!empty($settings['twitter']))
							<li><a href="{{ $settings['twitter'] }}" target="_blank"><i class="fab fa-twitter tw-color"></i></a></li>
                            @endif
                            @if(!empty($settings['instagram']))
							<li><a href="{{ $settings['instagram'] }}" target="_blank"><i class="fab fa-instagram insta-color"></i></a></li>
                            @endif
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </section>
<!-- main navbar area -->
      <section class="navbar-section">
         <nav class="navbar navbar-expand-lg p-1">
            <div class="container p-0">
               <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
               <i class="fa-solid fa-bars"></i>
               </button>
               <div class="collapse navbar-collapse" id="navbarScroll">
                   {!! \App\Helpers\CustomHelper::getMenuForFront(
                        $mainMenuItems, 
                        true, 
                        'navbar-nav m-auto my-2 my-lg-0 navbar-nav-scroll', 
                        'nav-item', 
                        'dropdown-menu', 
                        'dropdown', 
                        'nav-link'
                    ) !!}
               </div>
            </div>
         </nav>
      </section>

    @yield('content')

	<!-- footer area -->
  <!-- social section -->
      <section class="social-media-section pt-3 pb-3">
         <div class="container">
            <div class="row">
               <div class="col-md-12 text-center">
                  <div class="social-media-icon">
                     <h4 class="text-white mb-0">Follow us</h4>
                     <ul class="follow-list-item mb-0">
                        @if(!empty($settings['facebook']))
							<li><a href="{{ $settings['facebook'] }}" target="_blank"><span><i class="fab fa-facebook-f"></i></span></a></li>
                            @endif
                            @if(!empty($settings['twitter']))
							<li><a href="{{ $settings['twitter'] }}" target="_blank"><span><i class="fab fa-twitter"></i></span></a></li>
                            @endif
                            @if(!empty($settings['instagram']))
							<li><a href="{{ $settings['instagram'] }}" target="_blank"><span><i class="fab fa-instagram"></i></span></a></li>
                            @endif
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </section>

      <!-- footer area -->
      <footer class="footer">
         <div class="container">
            <div class="row">
               <div class="col-lg-12 text-center">
                  <div class="footer-logo">
                      @if(!empty($settings['logo_footer']))
						<img src="{{ asset('storage/' . $settings['logo_footer']) }}" class="img-fluid" alt="logo">
                        @else
                        <img src="{{ asset('frontend/assets/images/logo.png') }}" class="img-fluid" alt="logo">
                        @endif
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12 text-center">
                  <div class="official-detail">
                     <h5>Cuttack District Archery Association</h5>
                     <h6>Plot No.2/D 798, Markat Nagar, CDA, Sector-11, Cuttack-753014, Odisha</h6>
                     <ul>
                        <li><span><i class="fas fa-phone"></i></span><a href="tel:+919438553446">+91-9438553446</a></li>
                        <li><span><i class="fas fa-envelope"></i></span><a href="mailto:info@cdaa.in">info@cdaa.in</a></li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12 text-center mt-4 mb-4">
                  <div class="footer-navbar">
                     {!! \App\Helpers\CustomHelper::getMenuForFront(
                            $footerMenuItems, 
                            true, 
                            '', 
                            'nav-item', 
                            '', 
                            '', 
                            'nav-link'
                        ) !!}
                  </div>
               </div>
            </div>
         </div>
         <section class="footer-bottom">
            <div class="container">
               <div class="row">
                  <div class="col-md-12 text-center">
                     <div class="copyright-bx text-center">
                        <p>© Copyright {{ date('Y') }} {{ $settings['website_name'] ?? 'Cuttack District Archery Association' }}. All Rights Reserved.</p>
                     </div>
                  </div>
               </div>
            </div>
         </section>
      </footer>

















	<!-- JS Libraries -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
	
    <script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/script.js') }}"></script>
    <script src="{{ asset('frontend/assets/lightboxed/lightboxed.js') }}"></script>
	
		<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


	<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: "5000",
            positionClass: "toast-top-right"
        };
    </script>
    <div id="flash-messages" style="display:none"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}"
         data-info="{{ session('info') }}"
         data-warning="{{ session('warning') }}"></div>
    <script>
      (function(){
        var el=document.getElementById('flash-messages');
        if(!el) return;
        var s=el.getAttribute('data-success');
        var e=el.getAttribute('data-error');
        var i=el.getAttribute('data-info');
        var w=el.getAttribute('data-warning');
        if(s) toastr.success(s);
        if(e) toastr.error(e);
        if(i) toastr.info(i);
        if(w) toastr.warning(w);
      })();
    </script>
    @yield('scripts')
</body>
</html>
