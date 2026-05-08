@extends('frontend.layouts.main')

@section('content')
<!-- breadcrumb section -->
<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">{{ $news->title }}</h2>
					<nav aria-label="breadcrumb">
					   <ol class="breadcrumb  justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
                    
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $news->title }}</li>
                  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>
<!-- news detail area -->
<section class="latest-new-section pt-60 pb-60">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12 col-12">
                <div class="news-detail-bx">
                    <div class="news-image-bx mb-4">
                        @if($news->image)
                            <img src="{{ $news->image_url }}" alt="{{ $news->title }}" class="img-fluid w-100" style="border-radius: 10px;">
                        @else
                            <img src="{{ asset('frontend/assets/images/news-image.jpg') }}" alt="default news image" class="img-fluid w-100" style="border-radius: 10px;">
                        @endif
                    </div>
                    
                    <div class="news-content-bx">
                        <div class="date-bx mb-3">
                            <span class="text-muted"><i class="far fa-calendar-alt me-2"></i>{{ \Carbon\Carbon::parse($news->blog_date)->format('d M, Y') }}</span>
                        </div>
                        
                        <h2 class="mb-4">{{ $news->title }}</h2>
                        
                        <div class="news-description">
                            {!! $news->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
