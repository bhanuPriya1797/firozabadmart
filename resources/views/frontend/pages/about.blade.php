@extends('frontend.layouts.main')

@section('content')
@php
    $marqueeItems = json_decode(\App\Helpers\CustomHelper::getSetting('home_marquee_items') ?: '[]', true) ?: [];
    $aboutTitle = \App\Helpers\CustomHelper::getSetting('home_about_title');
    $aboutBrief = \App\Helpers\CustomHelper::getSetting('home_about_brief');
    $aboutTop = \App\Helpers\CustomHelper::getSetting('home_about_image_top');
    $aboutBottom = \App\Helpers\CustomHelper::getSetting('home_about_image_bottom');
    $aboutReadMore = \App\Helpers\CustomHelper::getSetting('home_about_readmore_link') ?: '#';
@endphp
<!-- breadcrumb section -->
	<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row  m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">About Us</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb  justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item active text-white" aria-current="page">About Us</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>






<!-- about us area -->
	<section class="about-us-section pt-60 pb-60">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-12 col-12">
                  <div class="about-image-bx">
                     <div class="tagline-bx">
                        <marquee>
                            @if(count($marqueeItems))
                                <h5>{{ implode(' • ', $marqueeItems) }}</h5>
                            @else
                                <h5>India finishes No.1 at the 24th Asian Archery Championships 2025 with a historic 10-medal haul—6 gold, 3 silver, 1 bronze—marking breakthrough recurve wins, compound dominance and rising young stars</h5>
                            @endif
                        </marquee>
                     </div>
                     <div class="about-image-bx-one">							
                        <img src="{{ $aboutTop ? asset('storage/'.$aboutTop) : asset('frontend/assets/images/vihan-reddy.jpg') }}" alt="img" class="img-fluid">						
                     </div>
                     <div class="about-image-bx-two">
                        <img src="{{ $aboutBottom ? asset('storage/'.$aboutBottom) : asset('frontend/assets/images/photo-gallery-1.jpg') }}" alt="img" class="img-fluid">
                     </div>
                  </div>
               </div>
				<div class="col-lg-6 col-md-12 col-12">
					<div class="about-content-bx">
						<h2 class="">{{ $aboutTitle ?: 'About Us' }}</h2>
                    <p>{!! $aboutBrief ?: '' !!}</p>
                     <!--<a href="{{ $aboutReadMore }}" class="btn common-btn rounded-pill">Read More</a>-->
					</div>					
				</div>
			</div>
		</div>
	</section>



<!-- award area -->
	<section class="award-section pt-60 pb-60">
		<div class="container">
			<div class="row">
				<div class="col-xl-3 col-lg-6 col-md-6 col-12 text-center mb-3">
					<div class="award-box">
						<img src="assets/images/trophy1.png" class="img-fluid" alt="trophy1">
						<div class="cup-txt-box">
							<h4>FL Cup (Carabao Cup)</h4>
							<h5>2011 / 2012 / 2015</h5>
						</div>						
					</div>					
				</div>
				<div class="col-xl-3 col-lg-6 col-md-6 col-12 text-center mb-3">
					<div class="award-box">
						<img src="assets/images/trophy2.png" class="img-fluid" alt="trophy1">
						<div class="cup-txt-box">
							<h4>FIFA Club World Cup</h4>
							<h5>2019</h5>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-6 col-md-6 col-12 text-center mb-3">
					<div class="award-box">
						<img src="assets/images/trophy3.png" class="img-fluid" alt="trophy1">
						<div class="cup-txt-box">
							<h4>FL Cup (Carabao Cup)</h4>
							<h5>2021</h5>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-6 col-md-6 col-12 text-center mb-3">
					<div class="award-box">
						<img src="assets/images/trophy4.png" class="img-fluid" alt="trophy1">
						<div class="cup-txt-box">
							<h4>Premier League Trophy</h4>
							<h5>2023</h5>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>



	@include('frontend.partials.partners', ['partners' => $partners ?? collect()])



@endsection
