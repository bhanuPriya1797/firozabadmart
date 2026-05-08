@component('admin.layouts.main')

@slot('title')
Archer Details - {{ config('app.name') }}
@endslot

@php
    $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Archer Details</h4>
        <a href="{{ route($ADMIN_ROUTE_NAME . '.archers.index') }}" class="btn btn-secondary btn-sm">
            Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-12 mb-2">
                    <h6 class="text-muted mb-0">Basic Information</h6>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">Name</div>
                    <div>{{ trim($archer->first_name . ' ' . $archer->surname) }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">Email</div>
                    <div>{{ $archer->email }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">Phone</div>
                    <div>{{ $archer->phone }}</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-2">
                    <h6 class="text-muted mb-0">Contact Details</h6>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">WhatsApp Number</div>
                    <div>{{ $archer->whatsapp_number }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">Alternate Phone</div>
                    <div>{{ $archer->alternate_phone ?: '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">Gender</div>
                    <div>{{ $archer->gender }}</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-2">
                    <h6 class="text-muted mb-0">Personal Details</h6>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">Date of Birth</div>
                    <div>{{ optional($archer->dob)->format('d M Y') ?: '-' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">Marital Status</div>
                    <div>{{ $archer->marital_status }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="fw-medium text-muted small">Is Minor</div>
                    <div>{{ $archer->is_minor ? 'Yes' : 'No' }}</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-2">
                    <h6 class="text-muted mb-0">Family Details</h6>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="fw-medium text-muted small">Father Name</div>
                    <div>{{ $archer->father_name }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="fw-medium text-muted small">Mother Name</div>
                    <div>{{ $archer->mother_name }}</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12 mb-2">
                    <h6 class="text-muted mb-0">Aadhaar & Status</h6>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="fw-medium text-muted small">Aadhaar Card Number</div>
                    <div>{{ $archer->aadhar_card_number }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="fw-medium text-muted small">Status</div>
                    <div>
                        @if($archer->status)
                            <span class="badge bg-label-success">Active</span>
                        @else
                            <span class="badge bg-label-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                @if($archer->aadhar_document)
                <div class="col-12 mb-3">
                    <div class="fw-medium text-muted small">Aadhaar Document</div>
                    <a href="{{ asset('storage/' . $archer->aadhar_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        View / Download Aadhaar File
                    </a>
                </div>
                @endif
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="fw-medium text-muted small">Created At</div>
                    <div>{{ $archer->created_at ? $archer->created_at->format('d M Y H:i') : '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="fw-medium text-muted small">Updated At</div>
                    <div>{{ $archer->updated_at ? $archer->updated_at->format('d M Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endcomponent
