@extends('frontend.layouts.main')

@section('content')
  <section class="breadcrumb-section breadcrumb-area">
    <div class="container">
      <div class="row m-0">
        <div class="col-lg-12 col-12 text-center">
          <h2 class="text-white breadcrumb-title">Calendar</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
              <li class="breadcrumb-item active text-white" aria-current="page">Calendar</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </section>

  <section class="pt-60 pb-60">
    <div class="container">
      @forelse($grouped as $month => $events)
        <div class="mb-4">
          <h3 class="sec-title">{{ $month }}</h3>
          <div class="list-group">
            @foreach($events as $ev)
              <div class="list-group-item">
                <div class="d-flex justify-content-between flex-wrap">
                  <div class="me-3">
                    <div class="fw-bold">{{ $ev->title }}</div>
                    @if(!empty($ev->category))
                      <div class="small text-muted">{{ $ev->category }}</div>
                    @endif
                    @if(!empty($ev->location))
                      <div class="small"><i class="fa fa-map-marker-alt me-1"></i>{{ $ev->location }}</div>
                    @endif
                  </div>
                  <div class="text-end">
                    @php
                      $start = $ev->start_date ? $ev->start_date->format('d M, Y') : '';
                      $end = $ev->end_date ? $ev->end_date->format('d M, Y') : '';
                    @endphp
                    <div class="small">{{ $start }} @if($end) - {{ $end }} @endif</div>
                    @if(!empty($ev->external_url))
                      <a class="btn btn-sm common-btn rounded-pill mt-2" href="{{ $ev->external_url }}" target="_blank">View</a>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @empty
        <div class="alert alert-info">No calendar events available.</div>
      @endforelse
    </div>
  </section>
@endsection
