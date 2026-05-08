@extends('frontend.layouts.main')

@section('content')
  <section class="breadcrumb-section breadcrumb-area">
    <div class="container">
      <div class="row m-0">
        <div class="col-lg-12 col-12 text-center">
          <h2 class="text-white breadcrumb-title">Events</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
              <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
              <li class="breadcrumb-item active text-white" aria-current="page">Events</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </section>

  <section class="pt-60 pb-60">
    <div class="container">
      <div class="row">
        @forelse($events as $event)
          <div class="col-lg-4 col-md-6 col-12 mb-4">
            <div class="card latest-new-box h-100">
              <a href="{{ url('events/'.$event->slug) }}">
                <img src="{{ $event->image_url }}" class="img-fluid" alt="{{ $event->title }}">
              </a>
              <div class="card-body news-text-box">
                <div class="article__category">{{ date('d F Y', strtotime($event->blog_date)) }}</div>
                <h5 class="article__title text-dark">
                  <a href="{{ url('events/'.$event->slug) }}" class="text-dark">{{ $event->title }}</a>
                </h5>
                <a href="{{ url('events/'.$event->slug) }}" class="btn news-box-btn rounded-pill">View Detail</a>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="alert alert-info">No events found.</div>
          </div>
        @endforelse
      </div>
      <div class="mt-4">
        {{ $events->links() }}
      </div>
    </div>
  </section>
@endsection
