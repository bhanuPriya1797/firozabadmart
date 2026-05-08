@extends('frontend.layouts.app')

@section('content')

<!-- slider section -->
    {{-- Banners --}}
    @if($banners->count() > 0)
      <section class="slider-section">
         <div id="carouselExampleDark" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                 @foreach($banners as $banner)
               <div class="carousel-item active" data-bs-interval="5000">
                    @php
                        $img = $banner->images->first();
                        $src = $img ? asset('storage/'.$img->image_path) : asset('assets/img/general/placeholder.png');
                    @endphp
                    <img src="{{ $src }}" alt="{{ $banner->title }}" class="d-block slider">
                  <div class="carousel-caption d-flex flex-column justify-content-center h-100">
                       <h2>{{ $banner->title }}</h2>
                        @if($banner->subtitle)
                            <p class="">
                                {{ $banner->subtitle }}
                            </p>
                        @endif
                        @if($banner->link)
                            <div class="masthead__buttons mt-40 wow fadeInUp" data-wow-delay=".4s">
                                <a href="{{ $banner->link }}" class="button -md -outline-white text-white">{{ $banner->button_text ?: 'Learn More' }}</a>
                            </div>
                        @endif
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




    {{-- Featured Events --}}
    @if($featuredEvents->count() > 0)
    <section class="layout-pt-md layout-pb-md bg-light-2">
        <div class="container">
            <div class="row justify-center text-center">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Featured Events</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">Upcoming matches and tournaments</p>
                    </div>
                </div>
            </div>
            <div class="row y-gap-30 pt-40">
                @foreach($featuredEvents as $event)
                    <div class="col-lg-4 col-md-6">
                        <div class="eventCard -type-1 rounded-4 shadow-1 bg-white hover-up-1 transition-normal">
                            <div class="eventCard__image ratio ratio-3:2">
                                <img src="{{ $event->image ? asset('storage/'.$event->image) : asset('assets/img/general/placeholder.png') }}" alt="{{ $event->title }}" class="img-ratio rounded-top-4">
                            </div>
                            <div class="eventCard__content px-30 py-30">
                                <div class="d-flex items-center text-13 text-light-1 mb-10">
                                    <i class="icon-calendar-2 text-16 mr-10"></i>
                                    {{ \Carbon\Carbon::parse($event->blog_date)->format('d M, Y') }}
                                </div>
                                <h4 class="text-18 fw-500 mb-10">
                                    <a href="{{ route('blog.show', $event->slug) }}" class="text-dark-1">{{ $event->title }}</a>
                                </h4>
                                <div class="d-flex items-center text-13 text-light-1 mb-20">
                                    <i class="icon-location-2 text-16 mr-10"></i>
                                    {{ $event->posted_by ?? 'Location TBD' }}
                                </div>
                                <div class="d-flex justify-between items-center">
                                    <a href="{{ route('blog.show', $event->slug) }}" class="button -md -outline-accent-1 text-accent-1">View Match</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- About Section --}}
    @if($aboutTitle)
    <section class="layout-pt-md layout-pb-md">
        <div class="container">
            <div class="row y-gap-30 justify-between items-center">
                <div class="col-lg-6">
                    <h2 class="text-30 fw-600">{{ $aboutTitle }}</h2>
                    <p class="mt-20">{!! $aboutBrief !!}</p>
                    <div class="d-inline-block mt-30">
                        <a href="{{ url('about') }}" class="button -md -dark-1 bg-dark-1 text-white">Read More</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="{{ $aboutImgTop ? asset('storage/'.$aboutImgTop) : asset('assets/img/general/placeholder.png') }}" alt="About" class="rounded-4">
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- News/Blogs --}}
    @if($featuredBlogs->count() > 0)
    <section class="layout-pt-md layout-pb-md">
        <div class="container">
             <div class="row justify-center text-center">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Latest News</h2>
                    </div>
                </div>
            </div>
            <div class="row y-gap-30 pt-40">
                @foreach($featuredBlogs as $blog)
                    <div class="col-lg-4 col-md-6">
                        <div class="blogCard -type-1">
                            <div class="blogCard__image ratio ratio-3:2">
                                <img src="{{ $blog->image ? asset('storage/'.$blog->image) : asset('assets/img/general/placeholder.png') }}" alt="{{ $blog->title }}" class="img-ratio rounded-4">
                            </div>
                            <div class="blogCard__content mt-20">
                                <div class="text-14 text-light-1 mb-10">{{ \Carbon\Carbon::parse($blog->blog_date)->format('M d, Y') }}</div>
                                <h4 class="text-18 fw-500">
                                    <a href="{{ route('blog.show', $blog->slug) }}" class="text-dark-1">{{ $blog->title }}</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Testimonials --}}
    @if($testimonials->count() > 0)
    <section class="layout-pt-md layout-pb-md bg-accent-1-05">
        <div class="container">
            <div class="row justify-center text-center">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title">Success Stories</h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0">What our community says</p>
                    </div>
                </div>
            </div>
            <div class="row y-gap-30 pt-40">
                @foreach($testimonials as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="testimonials -type-1">
                            <div class="testimonials__content">
                                <h4 class="testimonials__title">{{ $item->title }}</h4>
                                <p class="testimonials__text">{!! \Illuminate\Support\Str::limit(strip_tags($item->description), 100) !!}</p>
                                <div class="testimonials__author">
                                    <h5 class="testimonials__name">{{ $item->name }}</h5>
                                    <p class="testimonials__position">{{ $item->designation }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

@endsection
