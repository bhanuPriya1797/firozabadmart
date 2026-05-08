@php
    $mediaItem = isset($media) ? $media : null;
    $details = isset($mediaDetails) ? $mediaDetails : null;
@endphp

@if($mediaItem && $details)
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Attachment Details</h6>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Caption</dt>
                <dd class="col-sm-9">{{ $mediaItem->caption ?? '-' }}</dd>

                <dt class="col-sm-3">MIME Type</dt>
                <dd class="col-sm-9">{{ $details->mime_type ?? '-' }}</dd>

                <dt class="col-sm-3">File Size</dt>
                <dd class="col-sm-9">{{ method_exists($details, 'size') ? number_format($details->size) . ' bytes' : '-' }}</dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ optional($mediaItem->created_at)->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>
@else
    <div class="text-center py-3 text-muted">No attachment details available.</div>
@endif

