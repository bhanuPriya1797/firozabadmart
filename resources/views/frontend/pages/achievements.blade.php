@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">Achievements</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb  justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item active text-white" aria-current="page">Achievements</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>

<section class="pt-60 pb-60">
  <div class="container">
    <form method="GET" class="row g-3 align-items-end mb-5">
      <div class="col-xl-4 col-lg-4 col-md-6 col-12">
        <label class="form-label">Search by Name/Event</label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name, event or location">
      </div>
      <div class="col-xl-3 col-lg-4 col-md-6 col-12">
        <label class="form-label">Filter by Medal</label>
        <select class="form-select" name="medal">
          <option value="">All</option>
          <option value="gold" {{ request('medal')==='gold'?'selected':'' }}>Gold</option>
          <option value="silver" {{ request('medal')==='silver'?'selected':'' }}>Silver</option>
          <option value="bronze" {{ request('medal')==='bronze'?'selected':'' }}>Bronze</option>
        </select>
      </div>
      <div class="col-xl-3 col-lg-4 col-md-6 col-12">
        <label class="form-label">Filter by Category</label>
        <select class="form-select" name="category">
          <option value="">All</option>
          @php $cats = ['Recurve','Compound','Indian Round','Para']; @endphp
          @foreach($cats as $c)
            <option value="{{ $c }}" {{ request('category')===$c?'selected':'' }}>{{ $c }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-xl-2 col-lg-4 col-md-6 col-12 d-grid">
        <button class="btn common-btn rounded-pill">Apply</button>
      </div>
    </form>

    <div class="row achievement-section">
      @forelse($items as $item)
      <div class="col-xl-3 col-lg-4 col-md-6 col-12">
					<div class="card achievement-info">
					  <div class="card-body">
					    <h4><strong>{{ $item->winner_name }}</strong></h4>
							<ul>
                            @php
                                $map = ['gold'=>'warning','silver'=>'secondary','bronze'=>'brown'];
                                $cls = $map[$item->medal] ?? 'info';
                            @endphp
                            @if($item->medal)
                                <li><span class="achieve-color">Medal:</span> <span class="badge bg-{{ $cls }}">{{ ucfirst($item->medal) }}</span></li>
                            @endif
								<li><span class="achieve-color">Event:</span> {{ $item->event_name ?? 'N/A' }}</li>
								<li><span class="achieve-color">Location:</span> {{ $item->location ?? 'N/A' }}</li>
								<li><span class="achieve-color">Category:</span> {{ $item->category ?? 'N/A' }}</li>
							</ul>
					  </div>
					</div>
				</div>
      @empty
        <div class="col-12">
          <div class="alert alert-info">No achievements found.</div>
        </div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $items->withQueryString()->links() }}
    </div>
  </div>
</section>
@endsection

