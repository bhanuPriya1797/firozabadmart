@component('admin.layouts.main')

@slot('title')
Edit Archer - {{ config('app.name') }}
@endslot

@php
    $ADMIN_ROUTE_NAME = \App\Helpers\CustomHelper::getAdminRouteName();
    $encryptedId = \App\Helpers\CustomHelper::encrypt($archer->id);
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Archer</h4>
        <a href="{{ route($ADMIN_ROUTE_NAME . '.archers.index') }}" class="btn btn-secondary btn-sm">
            Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route($ADMIN_ROUTE_NAME . '.archers.update', $encryptedId) }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $archer->first_name) }}">
                        @error('first_name')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Surname</label>
                        <input type="text" name="surname" class="form-control" value="{{ old('surname', $archer->surname) }}">
                        @error('surname')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $archer->email) }}">
                        @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $archer->phone) }}">
                        @error('phone')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Alternate Phone</label>
                        <input type="text" name="alternate_phone" class="form-control" value="{{ old('alternate_phone', $archer->alternate_phone) }}">
                        @error('alternate_phone')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $archer->whatsapp_number) }}">
                        @error('whatsapp_number')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Father Name</label>
                        <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $archer->father_name) }}">
                        @error('father_name')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Mother Name</label>
                        <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $archer->mother_name) }}">
                        @error('mother_name')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">Select</option>
                            <option value="Male" {{ old('gender', $archer->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $archer->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender', $archer->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob', optional($archer->dob)->format('Y-m-d')) }}">
                        @error('dob')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Marital Status</label>
                        <select name="marital_status" class="form-select">
                            <option value="">Select</option>
                            <option value="Single" {{ old('marital_status', $archer->marital_status) === 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('marital_status', $archer->marital_status) === 'Married' ? 'selected' : '' }}>Married</option>
                        </select>
                        @error('marital_status')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-center">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_minor" id="is_minor" value="1" {{ old('is_minor', $archer->is_minor) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_minor">Is Minor</label>
                        </div>
                        @error('is_minor')
                        <div class="text-danger small d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" value="{{ old('category', $archer->category) }}" placeholder="Recurve / Compound / Indian Round / Para">
                        @error('category')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Member ID</label>
                        <input type="text" name="member_id" class="form-control" value="{{ old('member_id', $archer->member_id) }}">
                        @error('member_id')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Member Association</label>
                        <input type="text" name="member_association" class="form-control" value="{{ old('member_association', $archer->member_association) }}" placeholder="e.g., Odisha Archery Association">
                        @error('member_association')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Aadhaar Card Number</label>
                        <input type="text" name="aadhar_card_number" class="form-control" value="{{ old('aadhar_card_number', $archer->aadhar_card_number) }}">
                        @error('aadhar_card_number')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Aadhaar Document</label>
                        <input type="file" name="aadhar_document" class="form-control">
                        <div class="form-text">PDF, JPG, JPEG or PNG, max 5 MB.</div>
                        @if($archer->aadhar_document)
                        <div class="mt-2">
                            <a href="{{ asset('storage/' . $archer->aadhar_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                View / Download current file
                            </a>
                        </div>
                        @endif
                        @error('aadhar_document')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ old('status', $archer->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $archer->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Archer</button>
                    <a href="{{ route($ADMIN_ROUTE_NAME . '.archers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h5 class="mb-3">Application Status</h5>
            <div class="d-flex flex-wrap gap-2">
                @if(auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('archers.edit'))
                <button type="button" class="btn btn-success btn-app-status" data-status="approved" data-id="{{ $archer->id }}">Affiliate</button>
                <button type="button" class="btn btn-outline-secondary btn-app-status" data-status="pending" data-id="{{ $archer->id }}">Mark Pending</button>
                <button type="button" class="btn btn-outline-danger btn-app-status" data-status="rejected" data-id="{{ $archer->id }}">Reject</button>
                <button type="button" class="btn btn-outline-info btn-app-status" data-status="re_evaluate" data-id="{{ $archer->id }}">Re-evaluate</button>
                <button type="button" class="btn btn-outline-warning btn-app-status" data-status="renewal_pending" data-id="{{ $archer->id }}">Renewal Pending</button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="affiliateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Affiliate Archer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="affiliate_archer_id" value="{{ $archer->id }}" />
                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" id="affiliate_category" class="form-control" value="{{ $archer->category }}" placeholder="Recurve / Compound / Indian Round / Para" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Member ID</label>
                    <input type="text" id="affiliate_member_id" class="form-control" value="{{ $archer->member_id }}" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Member Association</label>
                    <input type="text" id="affiliate_member_association" class="form-control" value="{{ $archer->member_association }}" placeholder="e.g., Odisha Archery Association" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="affiliateSaveBtn">Affiliate</button>
            </div>
        </div>
    </div>
    </div>

@slot('footerBlock')
<script>
$(function () {
    const updateAppStatusUrl = @json(route($ADMIN_ROUTE_NAME . '.archers.update-app-status'));

    $(document).on('click', '.btn-app-status', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const status = $(this).data('status');
        if (status === 'approved') {
            const categoryVal = $('input[name="category"]').val() || '';
            const memberIdVal = $('input[name="member_id"]').val() || '';
            const assocVal = $('input[name="member_association"]').val() || '';
            $('#affiliate_archer_id').val(id);
            $('#affiliate_category').val(categoryVal);
            $('#affiliate_member_id').val(memberIdVal);
            $('#affiliate_member_association').val(assocVal);
            const modal = new bootstrap.Modal(document.getElementById('affiliateModal'));
            modal.show();
            $('#affiliateModal').data('modalInstance', modal);
        } else {
            $.ajax({
                url: updateAppStatusUrl,
                type: 'POST',
                data: {
                    archer_id: id,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    toastr.success(response.message || 'Application status updated.');
                },
                error: function () {
                    toastr.error('Failed to update application status.');
                }
            });
        }
    });

    $('#affiliateSaveBtn').on('click', function () {
        const id = $('#affiliate_archer_id').val();
        const category = $('#affiliate_category').val();
        const member_id = $('#affiliate_member_id').val();
        const member_association = $('#affiliate_member_association').val();
        if (!category || !member_id || !member_association) {
            toastr.error('Please fill Category, Member ID and Member Association.');
            return;
        }
        $.ajax({
            url: updateAppStatusUrl,
            type: 'POST',
            data: {
                archer_id: id,
                status: 'approved',
                category: category,
                member_id: member_id,
                member_association: member_association,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                toastr.success(response.message || 'Archer affiliated successfully.');
                const modal = $('#affiliateModal').data('modalInstance');
                if (modal) {
                    modal.hide();
                }
            },
            error: function () {
                toastr.error('Failed to affiliate archer.');
            }
        });
    });
});
</script>
@endslot

@endcomponent
