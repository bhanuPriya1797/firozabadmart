@component('admin.layouts.main')

@slot('title')
    Service Details - {{ config('app.name') }}
@endslot

@slot('headerBlock')
@endslot

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $item->title }}</h4>
            <p class="text-muted">Slug: {{ $item->slug }}</p>
            @if(!empty($item->image_url))
                <img src="{{ $item->image_url }}" class="rounded mb-3" width="240">
            @endif
            <p class="mb-2"><strong>Brief:</strong> {{ $item->brief }}</p>
            <div class="content">{!! $item->description !!}</div>
        </div>
    </div>
</div>

@endcomponent

