@extends('frontend.layouts.main')

@section('content')
	<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">{{ $page->title ?? 'Archers' }}</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb  justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item active text-white" aria-current="page">Archers</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>



<section class="pt-60 pb-60">
  <div class="container">
    <form method="GET" class="row g-3 align-items-end mb-4">
      <div class="col-xl-4 col-lg-4 col-md-6 col-12 mb-3">
        <label class="form-label">Search by Name/Email/Phone</label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Type name, email or phone">
      </div>
      <div class="col-xl-4 col-lg-4 col-md-6 col-12  mb-3">
        <label class="form-label">Filter by Category</label>
        <input type="text" name="category" value="{{ request('category') }}" class="form-control" placeholder="e.g., Recurve / Compound">
      </div>
      <div class="col-xl-2 col-lg-2 col-md-6 col-12 d-grid  mb-3">
        <button class="btn common-btn rounded-pill">Apply</button>
      </div>
    </form>

    <div class="row">
      @forelse($items as $item)
        <div class="col-lg-4 col-md-6 col-12 mb-4">
          <div class="border rounded h-100 overflow-hidden">
            <img src="{{ $item->avatar_url }}" alt="{{ trim($item->first_name . ' ' . $item->surname) }}" class="w-100" style="height:220px;object-fit:cover;">
            <div class="p-4">
              <h5 class="mb-1">{{ trim($item->first_name . ' ' . $item->surname) }}</h5>
              <div class="small text-muted mb-2">
                @if($item->dob)
                  <div><strong>DOB:</strong> {{ $item->dob->format('d M Y') }}</div>
                @endif
                @if($item->category)
                  <div><strong>Category:</strong> {{ $item->category }}</div>
                @endif
              </div>
              <div class="small">
                @if($item->member_id)
                  <div><strong>Member ID:</strong> {{ $item->member_id }}</div>
                @endif
                @if($item->member_association)
                  <div><strong>Association:</strong> {{ $item->member_association }}</div>
                @endif
              </div>
            </div>
          </div> 
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-info">No archers found.</div>
        </div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $items->withQueryString()->links() }}
    </div>
  </div>
  </section>
@endsection
