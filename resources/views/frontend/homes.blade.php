@extends('frontend.layouts.main')

@section('content')
    @php
        $marqueeItems = json_decode(\App\Helpers\CustomHelper::getSetting('home_marquee_items') ?: '[]', true) ?: [];
        $aboutTitle = \App\Helpers\CustomHelper::getSetting('home_about_title');
        $aboutBrief = \App\Helpers\CustomHelper::getSetting('home_about_brief');
        $aboutTop = \App\Helpers\CustomHelper::getSetting('home_about_image_top');
        $aboutBottom = \App\Helpers\CustomHelper::getSetting('home_about_image_bottom');
        $aboutReadMore = \App\Helpers\CustomHelper::getSetting('home_about_readmore_link') ?: url('about');
        $joinTitle = \App\Helpers\CustomHelper::getSetting('home_join_title') ?: 'Join Our Leagues';
        $joinSubtitle = \App\Helpers\CustomHelper::getSetting('home_join_subtitle') ?: 'CDAA Archery Club';
        $joinDesc = \App\Helpers\CustomHelper::getSetting('home_join_description');
        $joinImage = \App\Helpers\CustomHelper::getSetting('home_join_image');
        $joinLink = \App\Helpers\CustomHelper::getSetting('home_join_link') ?: url('register');
        $joinBtnText = \App\Helpers\CustomHelper::getSetting('home_join_button_text') ?: 'Become a Member';
        $circulars = $circulars ?? collect();
    @endphp
    
    @if($banners && $banners->images->count() > 0)
  <section class="slider-section">
         <div id="carouselExampleDark" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
            @foreach($banners->images as $key => $image)
               <div class="carousel-item {{ $key == 0 ? 'active' : '' }}" data-bs-interval="5000">
                  <img src="{{ $image->image_url }}" class="d-block slider" alt="{{ $image->title }}">
                  <div class="carousel-caption d-flex flex-column justify-content-center h-100">
                     <h2>{{ $image->title }}</h2>
					<p>{!! $image->description ?? $image->sub_title !!}</p>
                  </div>
               </div>
             	@endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
            <span class="arrow-prev"><i class="fa-solid fa-arrow-left"></i></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
            <span class="arrow-next"><i class="fa-solid fa-arrow-right"></i></span>
            </button>
         </div>
      </section> 
	@endif

 @if(count($marqueeItems))
      <section class="highlights-section">
         <div class="container p-0">
            <div class="row m-0">
               <div class="col-lg-12 col-12 d-flex align-items-center">
                  <marquee onmouseover="this.stop();" onmouseout="this.start();">
                     <h5>{{ implode(' • ', $marqueeItems) }}</h5>
                  </marquee>
               </div>
            </div>
         </div>
      </section>
  @endif
  
	<!-- about us area -->
	<section class="about-us-section pt-60 pb-60">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-lg-6 col-md-12 col-12">
                  <div class="about-image-bx">
                      @if(count($marqueeItems))
                     <div class="tagline-bx">
                        <marquee onmouseover="this.stop();" onmouseout="this.start();">
                           <h5>{{ implode(' • ', $marqueeItems) }}</h5>
                        </marquee>
                     </div>
                     @endif
                     	<div class="about-image-bx-one">							
							<img src="{{ $aboutTop ? asset('storage/'.$aboutTop) : "" }}" alt="img" class="img-fluid">							
						</div>						
						<div class="about-image-bx-two">
							<img src="{{ $aboutBottom ? asset('storage/'.$aboutBottom) : "" }}" alt="img" class="img-fluid">
						</div>
                  </div>
               </div>
               <div class="col-lg-6 col-md-12 col-12">
                  <div class="about-content-bx">
                     <h2 class="">{{ $aboutTitle ?: 'About Us' }}</h2>
					<p>{!! $aboutBrief ?: '' !!}</p>
                     <a class="btn common-btn rounded-pill" href="{{ $aboutReadMore }}">Read More</a>
                  </div>
               </div>
            </div>
         </div>
      </section>
	

 <!-- join club section -->
      <section class="join-club-section">
         <div class="container-fluid p-0">
            <div class="row align-items-center">
               <div class="col-xl-6 col-lg-12 col-12">
                  <div class="archery-club-outer">
                     <div class="shine">
                        <img src="{{ $joinImage ? asset('storage/'.$joinImage) : "" }}" class="img-fluid" alt="indoor-stadium-img">
                     </div>
                  </div>
               </div>
               <div class="col-xl-6 col-lg-12 col-12">
                  <div class="archery-club-txt p-5">
                     <h2 class="join-league-title mb-1">{{ $joinTitle }}</h2>
                     @if(!empty($joinSubtitle))
                            <h3>{{ $joinSubtitle }}</h3>
                            @endif
                     	<p>{{ $joinDesc }}</p>
                     	<a href="{{ $joinLink }}" class="btn common-btn mt-2">{{ $joinBtnText }}</a>
                  </div>
               </div>
            </div>
         </div>
      </section>


<!-- join club section -->
      <section class="circular-section pt-60 pb-60">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-xl-7 col-lg-7 col-md-12 col-12">
                  <div class="archery-club-outer">
                     <div class="shine mb-4">
                        <a href="{{ url('gallery') }}"><img src="{{ asset('frontend/assets/images/gallery-photo.png') }}" class="img-fluid" alt="indoor-stadium-img"></a>
                     </div>
                  </div>
               </div>
                @if(isset($circulars) && $circulars->count() > 0)
               <div class="col-xl-4 col-lg-5 col-md-12 offset-xl-1 col-md-6 col-12">
                  <div class="card circular-info border-0">                     
                     <div class="card-body circular-bx-inner p-0">
                        <h2 class="circular-title">Circular</h2>
                         @foreach($circulars as $c)
                        <ul>
                            @if(!empty($c->document_path))
                           <li>
                              <a href="{{ asset('storage/'.$c->document_path) }}">{{ $c->title }}</a>
                           </li>
                            @else
                                <li>{{ $c->title }}</li>
                            @endif
                        </ul>
                         @endforeach
                        <a href="{{ url('circulars') }}" class="btn btn-lg explore-btn">View All Circular<i class="fa fa-arrow-right"></i></a>
                     </div>                     
                  </div>
               </div>
                @endif
            </div>
         </div>
      </section>


 
 
 
 <!-- latest news area -->
 	@if($news->count() > 0)
      <section class="latest-new-section pt-60 pb-60 bg-light">
         <div class="container">
            <div class="row align-items-center mb-2">
               <div class="col-lg-6 col-6">
                  <h2 class="sec-title">Latest News</h2>
               </div>
               <div class="col-lg-6 col-6 text-end">
                  <a href="{{ url('news') }}" class="btn common-btn rounded-pill">View More</a>
               </div>
            </div>
            <div class="row m-0 p-0 d-flex align-items-center">
               <div class="col-md-12 m-0 p-0">
                  <div class="indoor-news-carousel owl-carousel owl-theme">
                      @foreach($news as $item)
                     <div class="card latest-new-box item m-2">
                        <a href="{{ url('news/'.$item->slug) }}"><img src="{{ $item->image_url }}" class="img-fluid" alt="{{ $item->title }}" title="{{ $item->title }}"></a>
                        <div class="card-body news-text-box">
                           <div class="article__category">{{ date('d F Y', strtotime($item->blog_date)) }}</div>
                           <h5 class="article__title text-dark"> <a href="{{ url('news/'.$item->slug) }}" class="text-dark">{{ $item->title }}</a></h5>
                           <a href="{{ url('news/'.$item->slug) }}" class="btn news-box-btn rounded-pill">Read More</a>
                        </div>
                     </div>
                     @endforeach
                  </div>
               </div>
            </div>
         </div>
      </section>
 @endif
 



 <!-- archery match section -->
      <section class="become-member pt-60 pb-60">
         <div class="container">
            <div class="row bg-image-area m-0 p-0 align-items-center">
               <div class="col-lg-6 col-md-6 col-12">
                  <div class="archery-club-info">
                    @php
                      $memberTitle = \App\Helpers\CustomHelper::getSetting('home_member_title') ?: 'CDAA Archery Club';
                      $memberDesc = \App\Helpers\CustomHelper::getSetting('home_member_description');
                      $memberBtnText = \App\Helpers\CustomHelper::getSetting('home_member_button_text') ?: 'Become a Member';
                      $memberBtnLink = \App\Helpers\CustomHelper::getSetting('home_member_button_link') ?: url('register');
                      $memberImage = \App\Helpers\CustomHelper::getSetting('home_member_image');
                    @endphp
                    <h2 class="sec-title">{{ $memberTitle }}</h2>
                    <p>{!! nl2br(e($memberDesc)) !!}</p>
                    <a href="{{ $memberBtnLink }}" class="btn member-btn btn-lg rounded-pill">{{ $memberBtnText }}</a>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-12 p-0">
                  <div class="archer-match-txt text-end">
                    @php $memberImg = $memberImage ? asset('storage/'.$memberImage) : asset('frontend/assets/images/become-member-image.jpg'); @endphp
                    <img src="{{ $memberImg }}" class="img-fluid" alt="become-member">
                  </div>
               </div>
            </div>
         </div>
      </section>



<!-- upcoming event area -->
	@if($events->count() > 0)
      <section class="upcoming-event pt-60 pb-60">
         <div class="container">
            <div class="row align-items-center mb-2">
               <div class="col-lg-6 col-6">
                  <h2 class="sec-title">Upcoming Events</h2>
               </div>
               <div class="col-lg-6 col-6 text-end">
                  <a href="{{ url('events') }}" class="btn common-btn rounded-pill">View More</a>
               </div>
            </div>
            <div class="row m-0 p-0 d-flex align-items-center">
               <div class="col-md-12 m-0 p-0">
                  <div class="owl-carousel match-carousel owl-theme">
                       @foreach($events as $event)
                     <div class="card event-box item m-2 text-center border-0 px-0">
                        <h2 class="event-date">{{ date('d', strtotime($event->blog_date)) }}</h2>
                        <p>{{ date('F Y', strtotime($event->blog_date)) }}</p>
                        <div class="card-body">
                           <div class="team-box">
                            @php
                                $team1 = $event->team_image_1_url ?? '';
                                $team2 = $event->team_image_2_url ?? '';
                            @endphp
                            <img src="{{ $team1 ?: "" }}" class="img-fluid">
                              <div class="date-box">
                                 <h4>{{ $event->title }}</h4>
                                 <!--<h6 class="text-dark">{{ $event->location ?? 'Location' }}</h6>-->
                              </div>
                              <img src="{{ $team2 ?: "" }}" class="img-fluid">
                           </div>
                           <a href="{{ url('events/'.$event->slug) }}" class="btn common-btn rounded-pill">View Detail</a>
                        </div>
                     </div>
                      @endforeach
                  </div>
               </div>
            </div>
         </div>
      </section>
	@endif


 <!-- coach section -->
      <section class="coach-section">
         <div class="container">
            <div class="row">
               @php
                 $coachTitle = 'Our Coach';
                 $coachName = isset($coach) ? $coach->name : 'Coach';
                 $coachDesignation = isset($coach) ? ($coach->designation ?? '') : '';
                 $coachBio = isset($coach) ? ($coach->bio ?? '') : '';
                 $coachImg = isset($coach) ? ($coach->image_url ?? asset('frontend/assets/images/coach.png')) : asset('frontend/assets/images/coach.png');
               @endphp
               <div class="col-lg-5 col-md-4 col-12 order-2 order-md-1">
                  <img src="{{ $coachImg }}" class="img-fluid" alt="coach">
               </div>
               <div class="col-lg-6 offset-lg-1 col-md-8 col-12 order-1 order-md-2">
                  <div class="coach-info">
                     <h2 class="sec-title">{{ $coachTitle }}</h2>
                     @if(!empty($coachBio))
                       <p>{!! nl2br(e($coachBio)) !!}</p>
                     @endif
                     <h4>{{ $coachName }}</h4>
                     @if(!empty($coachDesignation))
                       <h5><i>{{ $coachDesignation }}</i></h5>
                     @endif
                  </div>                  
               </div>
            </div>
         </div>
      </section>




	@if($partners->count() > 0)
  <!-- partner section -->
      <section class="partner-section pt-60 pb-60">
         <div class="container">
            <div class="row mb-3">
               <div class="col-lg-4 text-lg-end pe-lg-5">
                  @php
                    $partnersTitle = \App\Helpers\CustomHelper::getSetting('home_partners_title') ?: 'Our Partner & Sponsors';
                    $partnersDesc = \App\Helpers\CustomHelper::getSetting('home_partners_description');
                  @endphp
                  <h2 class="sec-title partner-title sec-heading text-dark">{{ $partnersTitle }}</h2>
               </div>
               <div class="col-lg-8">
                  {!! nl2br(e($partnersDesc)) !!}
               </div>
            </div>
            <div class="row m-0 p-0 d-flex align-items-center">
               <div class="col-md-12 m-0 p-0">
                  <div class="owl-carousel partner-carousel owl-theme">
                   @foreach($partners as $partner)
                     <div class="card event-box item m-2 text-center border-0 px-0">
                        <div class="card-body client-info d-flex">
                          <img src="{{ asset('storage/' . $partner->logo_path) }}" class="img-fluid">
                        </div>
                     </div>
                     @endforeach
                  </div>
               </div>
            </div>
         </div>
      </section>
@endif

@endsection

@section('scripts')
	<script>
		$(document).ready(function(){
			$('.match-carousel').owlCarousel({
				loop:true,
			    margin:10,
			    autoplay:true,
			    autoplayTimeout: 4000,
			    responsive:{
			        0:{
			            items:1
			        },
			        600:{
			            items:3
			        },
			        1000:{
			            items:2
			        }
			    }
			});
		})
	</script>

	<script>
		$(document).ready(function(){
			$('.indoor-news-carousel').owlCarousel({
				loop:true,
			    margin:10,
			    autoplay:true,
			    autoplayTimeout: 4000,
			    responsive:{
			        0:{
			            items:1
			        },
			        600:{
			            items:3
			        },
			        1000:{
			            items:3
			        }
			    }
			});
		})
	</script>
@endsection
