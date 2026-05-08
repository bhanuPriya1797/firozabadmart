@extends('frontend.layouts.main')

@section('styles')
    <style>
        .content-box-event {
            border: 2px solid var(--primary-color);
            border-radius: 12px;
            /* Ensure background is white */
            background: #fff;
        }
        .content-box-event h5 {
            font-weight: 800; /* Extra bold */
            color: var(--primary-color); /* Match title color if needed */
        }
        /* Override the global .content-box-event a styles for the image link */
        a.gallery-image-link {
            background: transparent !important;
            padding: 0 !important;
            border-radius: 0 !important;
            display: block !important;
        }
    </style>
@endsection

@section('content')

	<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">{{ $page->title ?? 'Gallery' }}</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item active text-white" aria-current="page">Gallery</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>
	
  
    <section class="circular-area pt-60 pb-60">
        <div class="container">
            <div class="row">
                @forelse($folders as $folder)
                <div class="col-lg-4 col-md-4 col-12">               
                  <div class="card gallery-box rounded mb-3 border-0">
                      <div class="gallery-img-box">
                        <a href="{{ route('gallery.detail', $folder->slug) }}">
                             <img src="{{ $folder->cover_url }}" class="img-fluid rounded" alt="{{ $folder->name }}">
                        </a> 
                      </div>
                    </div>
                    <h5 class="card-title text-center mt-5">
                       <a href="{{ route('gallery.detail', $folder->slug) }}">{{ $folder->name }}</a>
                     </h5> 
                    @if($folder->description)
                        <p class="text-center mb-2">{{ $folder->description }}</p>
                     @endif
                 </div>
            </div>
                @empty
                <div class="col-12 text-center">
                    <p>No gallery folders found.</p>
                </div>
                @endforelse
            </div>
            
            @if(method_exists($folders, 'links'))
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    {{ $folders->links() }}
                </div>
            </div>
            @endif
        </div>
    </section>

@endsection
