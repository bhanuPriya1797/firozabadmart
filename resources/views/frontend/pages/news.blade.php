@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">Gallery</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Latest News</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>


    <section class="latest-new-section pt-60 pb-60">
        <div class="container">
            <div class="row">
                @forelse($news as $item)
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="latest-new-box">
                        @if($item->image_url)
                            <a href="{{ route('news.detail', $item->slug) }}"><img src="{{ $item->image_url }}" class="img-fluid w-100" alt="{{ $item->title }}"></a>
                        @else
                             <img src="{{ asset('frontend/assets/images/news-image.jpg') }}" class="img-fluid w-100" alt="{{ $item->title }}">
                        @endif
                        <div class="news-text-box">
                            <div class="article__category"><time datetime="{{ $item->blog_date }}">{{ date('d F Y', strtotime($item->blog_date)) }}</time></div>
                            <h5 class="article__title text-dark"> 
                                <a href="{{ route('news.detail', $item->slug) }}" class="text-dark">{{ $item->title }}</a>
                            </h5>
                            <div class="article__publish_date">
                                <div class="article__field-publish-date">
                                    <a href="{{ route('news.detail', $item->slug) }}" class="btn news-box-btn rounded-pill">Read More</a>
                                </div>
                            </div>
                        </div>                          
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p>No news found.</p>
                </div>
                @endforelse
            </div>
            
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    {{ $news->links() }}
                </div>
            </div>
        </div>
    </section>

@endsection
