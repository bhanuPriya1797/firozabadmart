@extends('frontend.layouts.main')

@section('content')
  <section class="breadcrumb-section breadcrumb-area">
    <div class="container">
      <div class="row m-0">
        <div class="col-lg-12 col-12 text-center">
          <h2 class="text-white breadcrumb-title">{{ $event->title }}</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
              <li class="breadcrumb-item"><a href="{{ url('events') }}" class="text-white">Events</a></li>
              <li class="breadcrumb-item active text-white" aria-current="page">Details</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </section>

  <section class="pt-60 pb-60">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-12 mb-4">
          <img src="{{ $event->image_url }}" class="img-fluid mb-3" alt="{{ $event->title }}">
          <div class="mb-2 text-muted">{{ date('d F Y', strtotime($event->blog_date)) }}</div>
          <h3 class="mb-3">{{ $event->title }}</h3>
          @if(!empty($event->brief))
            <p class="lead">{{ $event->brief }}</p>
          @endif
          @if(!empty($event->content))
            <div>{!! $event->content !!}</div>
          @endif
        </div>
        <div class="col-lg-4 col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="mb-2">Teams</h5>
              <div class="d-flex align-items-center justify-content-between">
                @php
                  $team1 = $event->team_image_1_url ?? '';
                  $team2 = $event->team_image_2_url ?? '';
                @endphp
                @if($team1)
                  <img src="{{ $team1 }}" class="img-fluid" style="max-height:80px;">
                @endif
                @if($team2)
                  <img src="{{ $team2 }}" class="img-fluid" style="max-height:80px;">
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
