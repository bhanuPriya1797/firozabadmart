@php
  $partnersTitle = \App\Helpers\CustomHelper::getSetting('home_partners_title') ?: 'Our Partner & Sponsors';
  $partnersDesc = \App\Helpers\CustomHelper::getSetting('home_partners_description');
  $partners = isset($partners) ? $partners : \App\Models\Partner::where('status', 1)->orderBy('sort_order', 'asc')->get();
@endphp
@if($partners->count() > 0)
  <section class="partner-section pt-60 pb-60">
    <div class="container">
      <div class="row mb-3">
        <div class="col-lg-4 text-lg-end pe-lg-5">
          <h2 class="sec-title partner-title sec-heading text-dark">{{ $partnersTitle }}</h2>
        </div>
        <div class="col-lg-8">
          {!! nl2br(e($partnersDesc)) !!}
        </div>
      </div>
      <div class="row m-0 p-0 d-flex align-items-center">
        <div class="col-md-12 m-0 p-0">
          <div class="owl-carousel partner-carousel owl-theme">
            @foreach($partners as $partner)
              <div class="card event-box item m-2 text-center border-0 px-0">
                <div class="card-body client-info d-flex">
                  <img src="{{ asset('storage/' . $partner->logo_path) }}" class="img-fluid" alt="partner">
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>
@endif
