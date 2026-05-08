@extends('frontend.layouts.main')

@section('content')

<section class="section-bg layout-pt-lg layout-pb-lg">
    <div class="section-bg__item col-12">
        @php
            $bg = !empty($page->page_image) ? (\Illuminate\Support\Str::startsWith($page->page_image, 'uploads/') ? asset('storage/' . $page->page_image) : asset($page->page_image)) : asset('assets/image/h1-banner8.jpg');
        @endphp
        <img src="{{ $bg }}" alt="">
    </div>
    <div class="container">
        <div class="row justify-center text-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <h1 class="text-40 md:text-25 fw-600 text-white">{{ $page->title }}</h1>
                @if(!empty($page->brief))
                    <div class="text-white mt-15">{!! $page->brief !!}</div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="layout-pt-md layout-pb-md">
    <div class="container">
        @if(!empty($page->heading))
            <div class="text-center mb-4">
                <h2 class="fw-bold">{!! nl2br(e($page->heading)) !!}</h2>
            </div>
        @endif

        @if(!empty($page->page_image))
            <div class="text-center mb-4">
                <img src="{{ \Illuminate\Support\Str::startsWith($page->page_image, 'uploads/') ? asset('storage/' . $page->page_image) : asset($page->page_image) }}" class="img-fluid rounded-4" alt="">
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content">
                    {!! $page->description !!}
                </div>
            </div>
        </div>
    </div>
</section>

@if(($faqs ?? collect())->count())
<section class="layout-pt-md layout-pb-md">
    <div class="container">
        <div class="row justify-content-center">
            <div class="faq-wrapper col-lg-10">
                <h3 class="fw-bold mb-3">Frequently Asked Questions</h3>
                <p>Answers to common questions.</p>
                @php $idx = 1; @endphp
                @foreach($faqs as $faq)
                    <div class="faq-item {{ $idx === 1 ? 'active' : '' }}">
                        <button class="faq-question"><span>{{ $idx }}. {{ $faq->question }}</span><div class="icon">{{ $idx === 1 ? '−' : '+' }}</div></button>
                        <div class="faq-answer" style="{{ $idx === 1 ? 'display: block;' : '' }}"><p>{!! $faq->answer !!}</p></div>
                    </div>
                    @php $idx++; @endphp
                @endforeach
            </div>
        </div>
    </div>
    </section>
@endif

@endsection
