@extends('frontend.layouts.main')

@section('styles')
    <style>
   
    </style>
@endsection

@section('content')
<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">{{ $folder->name }}</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item"><a href="{{ route('gallery') }}" class="text-white">Gallery</a></li>
					   <li class="breadcrumb-item active text-white" aria-current="page">{{ $folder->name }}</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>

    <section class="gallery-section pt-60 pb-60">
        <div class="container">
            <div class="row" id="lightgallery">
                <div class="col-lg-12">
                     @forelse($images as $image)
                <img class="lightboxed" rel="group1" src="{{ $image->url }}" data-link="{{ $image->url }}" alt="{{ $image->title }}" data-caption="{{ $image->title }}" />
                    @empty
                        <div class="col-12 text-center">
                                <p>No images found in this folder.</p>
                        </div>
                <@endforelse
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    
</script>
@endsection
