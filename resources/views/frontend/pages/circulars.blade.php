@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">Circulars</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item active text-white" aria-current="page">Circulars</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>
	
<section class="pt-60 pb-60">
  <div class="container">
    <div class="row">
      <div class="col-12">
        @forelse($items as $c)
          <div class="p-3 mb-3 rounded-3 d-flex justify-content-between align-items-center" style="background:#e9ecef;border:1px solid #cbd3da;">
            <div class="me-3">
              <div class="fw-semibold">{{ $c->title }}</div>
            </div>
            @if(!empty($c->document_path))
              <a class="btn common-btn" target="_blank" href="{{ asset('storage/'.$c->document_path) }}">
                View
              </a>
            @endif
          </div>
        @empty
          <div class="alert alert-info">No circulars found.</div>
        @endforelse
      </div>
      <div class="col-12 mt-3">
        {{ $items->links() }}
      </div>
    </div>
  </div>
</section>
@endsection
