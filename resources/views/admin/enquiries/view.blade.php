@component('admin.layouts.main')

@slot('title')
    Enquiry Details - {{ config('app.name') }}
@endslot

@php
    $routeName = CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Enquiry Details</h4>
            <a href="{{ route($routeName . '.enquiries.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>Name:</strong> {{ $enquiry->name ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong> {{ $enquiry->contact_email ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong> {{ $enquiry->phone ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Country:</strong> {{ $enquiry->country ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <strong>IP Address:</strong> {{ $enquiry->ip_address ?? 'N/A' }}
                    </div>
                    <div class="mb-3">
                        <strong>Submitted At:</strong> {{ \Carbon\Carbon::parse($enquiry->created_at)->format('d M Y, h:i A') }}
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <strong>Message:</strong><br>
                <div class="mt-2 p-3 bg-light rounded">
                    {{ $enquiry->comment ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>
</div>

@endcomponent
