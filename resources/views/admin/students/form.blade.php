@component('admin.layouts.main')

    @slot('title')
        {{ isset($student) ? 'Edit Student' : 'Create Student' }} - {{ config('app.name') }}
    @endslot

    @php
        $routeName = CustomHelper::getAdminRouteName();
        $hasApplication = isset($studentApplication) && $studentApplication->exists;
    @endphp

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">{{ isset($student) ? 'Edit' : 'Add' }} Student</h4>
        </div>

        <div class="row g-6">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4>Student Application Form</h4></div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="studentForm">
                            @csrf
                            <input type="hidden" name="is_submitted" id="is_submitted" value="0">

                            {{-- Nav Tabs --}}
                            <ul class="nav nav-tabs" id="studentTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#personal" role="tab">Personal</a></li>
                                <li class="nav-item"><a class="nav-link {{ $hasApplication ? '' : 'disabled' }}" data-bs-toggle="tab" href="#financial" role="tab" id="financial-tab">Financial Info</a></li>
                                <li class="nav-item"><a class="nav-link {{ $hasApplication ? '' : 'disabled' }}" data-bs-toggle="tab" href="#course" role="tab" id="course-tab">Course Info</a></li>
                                <li class="nav-item"><a class="nav-link {{ $hasApplication ? '' : 'disabled' }}" data-bs-toggle="tab" href="#bank" role="tab" id="bank-tab">Bank Info</a></li>
                                <li class="nav-item"><a class="nav-link {{ $hasApplication ? '' : 'disabled' }}" data-bs-toggle="tab" href="#documents" role="tab" id="documents-tab">Documents</a></li>
                            </ul>

                            <div class="tab-content p-3" id="studentTabContent">
                                {{-- PERSONAL TAB --}}
                                <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">First & Middle Name (As per your certificates) *</label>
                                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $student->first_name ?? '') }}" required>
                                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Surname *</label>
                                            <input type="text" name="surname" class="form-control @error('surname') is-invalid @enderror"
                                                   value="{{ old('surname', $student->surname ?? '') }}" required>
                                            @error('surname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Email *</label>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" autocomplete="off" value="{{ old('email', $student->email ?? '') }}" required>
                                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Contact No *</label>
                                            <input type="text" name="contact_no" class="form-control @error('contact_no') is-invalid @enderror"
                                                   value="{{ old('contact_no', $student->contact_no ?? '') }}" required>
                                            @error('contact_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Date of Birth</label>
                                            <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                                   value="{{ old('dob', $student->dob ?? '') }}">
                                            @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Family Lineage *</label>
                                            <select name="family_lineage" class="form-select @error('family_lineage') is-invalid @enderror" required>
                                                <option value="">Select</option>
                                                <option value="SYED" {{ old('family_lineage', $student->family_lineage ?? '') == 'SYED' ? 'selected' : '' }}>SYED</option>
                                                <option value="NON-SYED" {{ old('family_lineage', $student->family_lineage ?? '') == 'NON-SYED' ? 'selected' : '' }}>NON-SYED</option>
                                            </select>
                                            @error('family_lineage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label d-block">Gender *</label>
                                            @php $gender = old('gender', $student->gender ?? ''); @endphp
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender" value="Male" {{ $gender == 'Male' ? 'checked' : '' }} required>
                                                <label class="form-check-label">Male</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender" value="Female" {{ $gender == 'Female' ? 'checked' : '' }}>
                                                <label class="form-check-label">Female</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="gender" value="Other" {{ $gender == 'Other' ? 'checked' : '' }}>
                                                <label class="form-check-label">Other</label>
                                            </div>
                                            @error('gender') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $student->address ?? '') }}</textarea>
                                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ isset($student) ? 'Change Password' : 'Password *' }}</label>
                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                                   {{ isset($student) ? '' : 'required' }} autocomplete="new-password">
                                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-3"></div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">State *</label>
                                            <select name="state" class="form-select @error('state') is-invalid @enderror" required>
                                                <option value="">Select State</option>
                                                @foreach($states as $state)
                                                    <option value="{{ $state }}" {{ old('state', $student->state ?? '') == $state ? 'selected' : '' }}>{{ $state }}</option>
                                                @endforeach
                                            </select>
                                            @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">District *</label>
                                            <input type="text" name="district" class="form-control @error('district') is-invalid @enderror"
                                                   value="{{ old('district', $student->district ?? '') }}" required>
                                            @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Pincode</label>
                                            <input type="text" name="pincode" class="form-control @error('pincode') is-invalid @enderror"
                                                   value="{{ old('pincode', $student->pincode ?? '') }}">
                                            @error('pincode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Father/Guardian's Name</label>
                                            <input type="text" name="guardian_name" class="form-control @error('guardian_name') is-invalid @enderror"
                                                   value="{{ old('guardian_name', $student->guardian_name ?? '') }}">
                                            @error('guardian_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Father/Guardian's Contact No.</label>
                                            <input type="text" name="guardian_contact_no" class="form-control @error('guardian_contact_no') is-invalid @enderror"
                                                   value="{{ old('guardian_contact_no', $student->guardian_contact_no ?? '') }}">
                                            @error('guardian_contact_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Father/Guardian's Occupation</label>
                                            <input type="text" name="guardian_occupation" class="form-control @error('guardian_occupation') is-invalid @enderror" value="{{ old('guardian_occupation', $student->guardian_occupation ?? '') }}">
                                            <div class="form-text">Exact Details of Father Occupation.</div>
                                            @error('guardian_occupation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label">Father/Guardian's Occupation Details</label>
                                            <textarea name="guardian_occupation_details" class="form-control @error('guardian_occupation_details') is-invalid @enderror" rows="3" placeholder="Please provide detailed information about your father/guardian's occupation, including job title, company name, and nature of work">{{ old('guardian_occupation_details', $student->guardian_occupation_details ?? '') }}</textarea>
                                            @error('guardian_occupation_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Monthly Family Income</label>
                                            <input type="number" name="monthly_income" class="form-control @error('monthly_income') is-invalid @enderror"
                                                   value="{{ old('monthly_income', $student->monthly_income ?? '') }}">
                                            <div class="form-text">Please confirm if your family income in below 30,000 per month for being eligible for ILM loan.</div>
                                            @error('monthly_income') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Total Family Members</label>
                                            <input type="number" name="family_members" class="form-control @error('family_members') is-invalid @enderror"
                                                   value="{{ old('family_members', $student->family_members ?? '') }}">
                                            @error('family_members') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="">Select Status</option>
                                                <option value="1" {{ old('status', $student->status ?? '') == 1 ? 'selected' : '' }}>{{ __('Activate') }}</option>
                                                <option value="0" {{ old('status', $student->status ?? '') == 0 ? 'selected' : '' }}>{{ __('Deactivate') }}</option>
                                            </select>
                                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3 next-tab" data-next="financial">Next Step</button>
                                </div>

                                {{-- FINANCIAL INFO TAB --}}
                                <div class="tab-pane fade" id="financial" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">Why are you seeking Financial aid from ILM?</label>
                                            <textarea name="reason_for_aid" class="form-control @error('reason_for_aid') is-invalid @enderror" rows="3">{{ old('reason_for_aid', $studentApplication->reason_for_aid ?? '') }}</textarea>
                                            @error('reason_for_aid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Have you received any financial support from relatives or friends so far?</label>
                                            <select name="received_support_from_others" class="form-control @error('received_support_from_others') is-invalid @enderror" required>
                                                <option value="no" {{ old('received_support_from_others', $studentApplication->received_support_from_others ?? 'no') == 'no' ? 'selected' : '' }}>No</option>
                                                <option value="yes" {{ old('received_support_from_others', $studentApplication->received_support_from_others ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            </select>
                                            @error('received_support_from_others') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Have you received or sought scholarship or grants from ILM or others?</label>
                                            <select name="received_scholarship" id="received_scholarship" class="form-control @error('received_scholarship') is-invalid @enderror" required>
                                                <option value="no" {{ old('received_scholarship', $studentApplication->received_scholarship ?? 'no') == 'no' ? 'selected' : '' }}>No</option>
                                                <option value="yes" {{ old('received_scholarship', $studentApplication->received_scholarship ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            </select>
                                            @error('received_scholarship') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-12 mb-2" id="scholarship_details_div" style="display: {{ (old('received_scholarship', $studentApplication->received_scholarship ?? 'no') == 'yes') ? 'block' : 'none' }}">
                                            <label class="form-label">Scholarship or grants details</label>
                                            <textarea name="scholarship_details" class="form-control @error('scholarship_details') is-invalid @enderror" rows="3">{{ old('scholarship_details', $studentApplication->scholarship_details ?? '') }}</textarea>
                                            @error('scholarship_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label>Please describe how you have managed to pay for your fees and expenses so far?</label>
                                            <textarea name="how_fees_paid_before" class="form-control @error('how_fees_paid_before') is-invalid @enderror" rows="3">{{ old('how_fees_paid_before', $studentApplication->how_fees_paid_before ?? '') }}</textarea>
                                            @error('how_fees_paid_before') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3 next-tab" data-next="course">Next Step</button>
                                    <button type="button" class="btn btn-secondary mt-3 prev-tab" data-prev="personal">Previous</button>
                                </div>

                                {{-- COURSE INFO TAB (Updated) --}}
                                <div class="tab-pane fade" id="course" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Course Name *</label>
                                            <input type="text" name="course_name" class="form-control @error('course_name') is-invalid @enderror" value="{{ old('course_name', $studentApplication->course_name ?? '') }}" required>
                                            <div class="form-text">Enter the name of your course (e.g., B.Tech, MBA).</div>
                                            @error('course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Branch</label>
                                            <input type="text" name="branch" class="form-control @error('branch') is-invalid @enderror" value="{{ old('branch', $studentApplication->branch ?? '') }}">
                                            <div class="form-text">Specify your branch or specialization (e.g., Computer Science) if applicable.</div>
                                            @error('branch') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Educational Stage *</label>
                                            <select name="edu_stage" class="form-control @error('edu_stage') is-invalid @enderror" required>
                                                <option value="">Select</option>
                                                @php
                                                    $eduStages = config('custom.education_stages');
                                                @endphp
                                                @foreach($eduStages as $stage)
                                                    <option value="{{ $stage }}" {{ old('edu_stage', $studentApplication->edu_stage ?? '') == $stage ? 'selected' : '' }}>
                                                        {{ $stage }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">Select the level of education (e.g., Undergraduate, Postgraduate).</div>
                                            @error('edu_stage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Total Course Duration (Years)</label>
                                            <select name="course_duration_years" class="form-control @error('course_duration_years') is-invalid @enderror">
                                                <option value="">Year</option>
                                                @for($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ old('course_duration_years', $studentApplication->course_duration_years ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <div class="form-text">Select the total years of your course duration (optional).</div>
                                            @error('course_duration_years') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Total Course Duration (Months)</label>
                                            <select name="course_duration_months" class="form-control @error('course_duration_months') is-invalid @enderror">
                                                <option value="">Months</option>
                                                @for($i = 0; $i <= 11; $i++)
                                                    <option value="{{ $i }}" {{ old('course_duration_months', $studentApplication->course_duration_months ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <div class="form-text">Select additional months for course duration (optional).</div>
                                            @error('course_duration_months') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Year of Enrolment *</label>
                                            <select name="enrolment_year" class="form-control @error('enrolment_year') is-invalid @enderror" required>
                                                @php
                                                    $currentYear = date('Y');
                                                    $yearRangeStart = $currentYear - 5;
                                                    $yearRangeEnd = $currentYear + 1;
                                                @endphp
                                                <option value="">Select Year</option>
                                                @for ($year = $yearRangeStart; $year <= $yearRangeEnd; $year++)
                                                    <option value="{{ $year }}" {{ old('enrolment_year', $studentApplication->enrolment_year ?? '') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                                @endfor
                                            </select>
                                            <div class="form-text">Select the year you enrolled in the course.</div>
                                            @error('enrolment_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Currently Enrolled In *</label>
                                            <select name="current_year_or_sem" class="form-control @error('current_year_or_sem') is-invalid @enderror" required>
                                                <option value="">Select</option>
                                                <option value="Year" {{ old('current_year_or_sem', $studentApplication->current_year_or_sem ?? '') == 'Year' ? 'selected' : '' }}>Year</option>
                                                <option value="Semester" {{ old('current_year_or_sem', $studentApplication->current_year_or_sem ?? '') == 'Semester' ? 'selected' : '' }}>Semester</option>
                                            </select>
                                            <div class="form-text">Select whether you are in a year or semester system.</div>
                                            @error('current_year_or_sem') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-2 mb-2">
                                            <label class="form-label">Year/Sem Number *</label>
                                            <input type="number" name="current_number" min="1" max="12"
                                                   class="form-control @error('current_number') is-invalid @enderror"
                                                   value="{{ old('current_number', $studentApplication->current_number ?? '') }}" required>
                                            <div class="form-text">Enter your current year or semester number (1-12).</div>
                                            @error('current_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Overall Course Fees (Estimated)</label>
                                            <input type="number" step="0.01" name="total_course_fees"
                                                   class="form-control @error('total_course_fees') is-invalid @enderror"
                                                   value="{{ old('total_course_fees', $studentApplication->total_course_fees ?? '') }}">
                                            <div class="form-text">Enter the estimated total fees for the entire course in INR (optional).</div>
                                            @error('total_course_fees') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>                                        
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Current Year/Semester Fees</label>
                                            <input type="number" step="0.01" name="current_year_semester_fees"
                                                   class="form-control @error('current_year_semester_fees') is-invalid @enderror"
                                                   value="{{ old('current_year_semester_fees', $studentApplication->current_year_semester_fees ?? '') }}">
                                            <div class="form-text">Enter the fees for your current year/semester in INR (optional).</div>
                                            @error('current_year_semester_fees') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Last date of Fees/Amount Submission</label>
                                            <select name="fees_submission_status" class="form-control @error('fees_submission_status') is-invalid @enderror" id="fees_submission_status">
                                                <option value="not_yet_known" {{ old('fees_submission_status', $studentApplication->fees_submission_status ?? 'date') == 'not_yet_known' ? 'selected' : '' }}>Not yet known</option>
                                                <option value="date" {{ old('fees_submission_status', $studentApplication->fees_submission_status ?? 'date') == 'date' ? 'selected' : '' }}>Date</option>
                                            </select>
                                            <div class="form-text">Select whether you know the last date of fees submission.</div>
                                            @error('fees_submission_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-2" id="fees_submission_date_field" style="display: {{ old('fees_submission_status', $studentApplication->fees_submission_status ?? 'date') == 'date' ? 'block' : 'none' }};">
                                            <label class="form-label">Submission Date</label>
                                            <input type="date" name="last_fees_submission_date" class="form-control @error('last_fees_submission_date') is-invalid @enderror" value="{{ old('last_fees_submission_date', $studentApplication->last_fees_submission_date ?? '') }}">
                                            <div class="form-text">Enter the last date of fees/amount submission.</div>
                                            @error('last_fees_submission_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Amount Needed from ILM</label>
                                            <input type="number" step="0.01" name="amount_needed"
                                                   class="form-control @error('amount_needed') is-invalid @enderror"
                                                   value="{{ old('amount_needed', $studentApplication->amount_needed ?? '') }}">
                                            <div class="form-text">Enter the total amount you are requesting from ILM for all fees purposes (optional).</div>
                                            @error('amount_needed') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="form-label">Support Required *</label>
                                            <select name="support_required" class="form-control @error('support_required') is-invalid @enderror" required>
                                                <option value="">Select</option>
                                                <option value="One-time" {{ old('support_required', $studentApplication->support_required ?? '') == 'One-time' ? 'selected' : '' }}>Only Once</option>
                                                <option value="Recurring" {{ old('support_required', $studentApplication->support_required ?? '') == 'Recurring' ? 'selected' : '' }}>Recurring/Repeated</option>
                                            </select>
                                            <div class="form-text">Please note that further support will be contingent upon your performance meeting our established criteria.</div>
                                            @error('support_required') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>                                        
                                        <!-- Financial Aid Request Details Section -->
                                        <div class="col-12 mb-4">
                                            <h5 class="mb-3">Financial Aid Request Details</h5>
                                            <div id="fees_entries_container">
                                                @php
                                                    $feesEntries = old('fees_entries', $studentApplication->fees_entries ?? [[]]);
                                                @endphp
                                                @foreach($feesEntries as $index => $entry)
                                                    <div class="fees_entry card mb-2 p-3" data-index="{{ $index }}">
                                                        <div class="row gx-2">
                                                            <div class="col-md-4 mb-2">
                                                                <label class="form-label">Purpose of Fees Requested *</label>
                                                                <select name="fees_entries[{{ $index }}][fees_purpose]" class="form-control fees-purpose @error('fees_entries.' . $index . '.fees_purpose') is-invalid @enderror" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Tuition Fees" {{ old('fees_entries.' . $index . '.fees_purpose', $entry['fees_purpose'] ?? '') == 'Tuition Fees' ? 'selected' : '' }}>Tuition Fees</option>
                                                                    <option value="Hostel Fees" {{ old('fees_entries.' . $index . '.fees_purpose', $entry['fees_purpose'] ?? '') == 'Hostel Fees' ? 'selected' : '' }}>Hostel Fees</option>
                                                                    <option value="Mess Fees" {{ old('fees_entries.' . $index . '.fees_purpose', $entry['fees_purpose'] ?? '') == 'Mess Fees' ? 'selected' : '' }}>Mess Fees</option>
                                                                    <option value="Transport Fees" {{ old('fees_entries.' . $index . '.fees_purpose', $entry['fees_purpose'] ?? '') == 'Transport Fees' ? 'selected' : '' }}>Transport Fees</option>
                                                                    <option value="Room Rent" {{ old('fees_entries.' . $index . '.fees_purpose', $entry['fees_purpose'] ?? '') == 'Room Rent' ? 'selected' : '' }}>Room Rent</option>
                                                                    <option value="Hostel & Mess" {{ old('fees_entries.' . $index . '.fees_purpose', $entry['fees_purpose'] ?? '') == 'Hostel & Mess' ? 'selected' : '' }}>Hostel & Mess</option>
                                                                    <option value="Others" {{ old('fees_entries.' . $index . '.fees_purpose', $entry['fees_purpose'] ?? '') == 'Others' ? 'selected' : '' }}>Others</option>
                                                                </select>
                                                                <div class="form-text">Select the specific purpose for which you are requesting financial aid from ILM.</div>
                                                                @error('fees_entries.' . $index . '.fees_purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </div>
                                                            <div class="col-md-12 mb-2 fees-purpose-description-div" style="display: {{ old('fees_entries.' . $index . '.fees_purpose', $entry['fees_purpose'] ?? '') == 'Others' ? 'block' : 'none' }};">
                                                                <label class="form-label">Describe Other Fees Purpose *</label>
                                                                <textarea name="fees_entries[{{ $index }}][fees_purpose_description]" class="form-control @error('fees_entries.' . $index . '.fees_purpose_description') is-invalid @enderror" rows="3">{{ old('fees_entries.' . $index . '.fees_purpose_description', $entry['fees_purpose_description'] ?? '') }}</textarea>
                                                                <div class="form-text">Describe the specific fees you are requesting assistance for.</div>
                                                                @error('fees_entries.' . $index . '.fees_purpose_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Payment Period *</label>
                                                                <select name="fees_entries[{{ $index }}][fees_duration]" class="form-control @error('fees_entries.' . $index . '.fees_duration') is-invalid @enderror" required>
                                                                    <option value="">Select</option>
                                                                    <option value="Yearly" {{ old('fees_entries.' . $index . '.fees_duration', $entry['fees_duration'] ?? '') == 'Yearly' ? 'selected' : '' }}>Yearly</option>
                                                                    <option value="Semester" {{ old('fees_entries.' . $index . '.fees_duration', $entry['fees_duration'] ?? '') == 'Semester' ? 'selected' : '' }}>Semester</option>
                                                                    <option value="Quarterly" {{ old('fees_entries.' . $index . '.fees_duration', $entry['fees_duration'] ?? '') == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                                    <option value="4-months" {{ old('fees_entries.' . $index . '.fees_duration', $entry['fees_duration'] ?? '') == '4-months' ? 'selected' : '' }}>4-months</option>
                                                                    <option value="5-months" {{ old('fees_entries.' . $index . '.fees_duration', $entry['fees_duration'] ?? '') == '5-months' ? 'selected' : '' }}>5 Months</option>
                                                                    <option value="Monthly" {{ old('fees_entries.' . $index . '.fees_duration', $entry['fees_duration'] ?? '') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                                                                    <option value="One-time" {{ old('fees_entries.' . $index . '.fees_duration', $entry['fees_duration'] ?? '') == 'One-time' ? 'selected' : '' }}>One-time</option>
                                                                </select>
                                                                <div class="form-text">Select the payment period for which the fees amount applies.</div>
                                                                @error('fees_entries.' . $index . '.fees_duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <label class="form-label">Fees Amount (INR) *</label>
                                                                <input type="number" step="0.01" name="fees_entries[{{ $index }}][fees_amount]" class="form-control fees-amount @error('fees_entries.' . $index . '.fees_amount') is-invalid @enderror" value="{{ old('fees_entries.' . $index . '.fees_amount', $entry['fees_amount'] ?? '') }}" required>
                                                                <div class="form-text">Enter the total amount for this fees purpose in INR.</div>
                                                                @error('fees_entries.' . $index . '.fees_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </div>
                                                            <div class="col-md-2 mb-2">
                                                                <label class="form-label">&nbsp;</label>
                                                                <button type="button" class="btn btn-danger btn-sm remove-fees-entry w-100">Remove</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <button type="button" class="btn btn-primary" id="addFeesEntryBtn">
                                                        <i class="bx bx-plus"></i> Add Another Fee Entry
                                                    </button>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Total Fees Amount (INR)</label>
                                                    <input type="text" class="form-control" id="total_fees_amount" value="0.00" readonly>
                                                    <div class="form-text">Total amount of all fees entries entered above.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Name of College/University *</label>
                                            <input type="text" name="college_name"
                                                   class="form-control @error('college_name') is-invalid @enderror"
                                                   value="{{ old('college_name', $studentApplication->college_name ?? '') }}" required>
                                            <div class="form-text">Enter the full name of your college or university.</div>
                                            @error('college_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label">College/University Address *</label>
                                            <textarea name="college_address" rows="3"
                                                      class="form-control @error('college_address') is-invalid @enderror" required>{{ old('college_address', $studentApplication->college_address ?? '') }}</textarea>
                                            <div class="form-text">Provide the complete address of your college or university.</div>
                                            @error('college_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3 next-tab" data-next="bank">Next Step</button>
                                    <button type="button" class="btn btn-secondary mt-3 prev-tab" data-prev="financial">Previous</button>
                                </div>

                                {{-- BANK INFO TAB --}}
                                <div class="tab-pane fade" id="bank" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Institute Account No.</label>
                                            <input type="text" name="account_no" class="form-control @error('account_no') is-invalid @enderror" value="{{ old('account_no', $studentApplication->account_no ?? '') }}">
                                            @error('account_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label">Account Name</label>
                                            <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $studentApplication->account_name ?? '') }}">
                                            @error('account_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Bank Name</label>
                                            <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $studentApplication->bank_name ?? '') }}">
                                            @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">Bank Branch</label>
                                            <input type="text" name="bank_branch" class="form-control @error('bank_branch') is-invalid @enderror" value="{{ old('bank_branch', $studentApplication->bank_branch ?? '') }}">
                                            @error('bank_branch') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">IFS Code</label>
                                            <input type="text" name="ifsc_code" class="form-control @error('ifsc_code') is-invalid @enderror" value="{{ old('ifsc_code', $studentApplication->ifsc_code ?? '') }}">
                                            @error('ifsc_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3 next-tab" data-next="documents">Next Step</button>
                                    <button type="button" class="btn btn-secondary mt-3 prev-tab" data-prev="course">Previous</button>
                                </div>

                                {{-- DOCUMENTS TAB --}}
                                <div class="tab-pane fade" id="documents" role="tabpanel">
                                    <div id="document_container">
                                        <h4>Please Add your latest Marksheets/Certificates Starting from 10th</h4>
                                        @if(old('documents'))
                                            @foreach(old('documents') as $idx => $doc)
                                                <div class="document_entry row gx-2 mb-2">
                                                    <div class="col-md-2">
                                                        <input type="text" name="documents[{{ $idx }}][course_name]" placeholder="Course" class="form-control" value="{{ $doc['course_name'] ?? '' }}">
                                                        @error("documents.$idx.course_name") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="documents[{{ $idx }}][college]" placeholder="College/University" class="form-control" value="{{ $doc['college'] ?? '' }}">
                                                        @error("documents.$idx.college") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="documents[{{ $idx }}][academic_year]" placeholder="Year (e.g. 2020-2021)" class="form-control" value="{{ $doc['academic_year'] ?? '' }}">
                                                        @error("documents.$idx.academic_year") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-1">
                                                        <input type="text" name="documents[{{ $idx }}][percentage]" placeholder="Percent" class="form-control" value="{{ $doc['percentage'] ?? '' }}">
                                                        @error("documents.$idx.percentage") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="documents[{{ $idx }}][document_name]" placeholder="Document Name" class="form-control" value="{{ $doc['document_name'] ?? '' }}">
                                                        @error("documents.$idx.document_name") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="file" name="documents[{{ $idx }}][document_file]" class="form-control" accept=".png,.jpg,.jpeg,.pdf,.webp">
                                                        @error("documents.$idx.document_file") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>
                                            @endforeach
                                        @elseif(isset($studentApplication) && $studentApplication->documents->count() > 0)
                                            @foreach($studentApplication->documents as $idx => $document)
                                                <div class="document_entry row gx-2 mb-2">
                                                    <div class="col-md-2">
                                                        <input type="text" name="documents[{{ $idx }}][course_name]" placeholder="Course" class="form-control" value="{{ $document->course_name }}">
                                                        @error("documents.$idx.course_name") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="documents[{{ $idx }}][college]" placeholder="College/University" class="form-control" value="{{ $document->college }}">
                                                        @error("documents.$idx.college") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="documents[{{ $idx }}][academic_year]" placeholder="Year (e.g. 2020-2021)" class="form-control" value="{{ $document->academic_year }}">
                                                        @error("documents.$idx.academic_year") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-1">
                                                        <input type="text" name="documents[{{ $idx }}][percentage]" placeholder="Percent" class="form-control" value="{{ $document->percentage }}">
                                                        @error("documents.$idx.percentage") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" name="documents[{{ $idx }}][document_name]" placeholder="Document Name" class="form-control" value="{{ $document->document_name }}">
                                                        @error("documents.$idx.document_name") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        @if($document->document_file)
                                                            <a href="{{ asset('storage/' . $document->document_file) }}" target="_blank" class="btn btn-sm btn-info mb-1">View File</a>
                                                        @endif
                                                        <input type="file" name="documents[{{ $idx }}][document_file]" class="form-control" accept=".png,.jpg,.jpeg,.pdf,.webp">
                                                        @error("documents.$idx.document_file") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                        <input type="hidden" name="documents[{{ $idx }}][id]" value="{{ $document->id }}">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-danger btn-sm remove-document" data-id="{{ $document->id }}">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="document_entry row gx-2 mb-2">
                                                <div class="col-md-2">
                                                    <input type="text" name="documents[0][course_name]" placeholder="Course" class="form-control">
                                                    @error('documents.0.course_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="documents[0][college]" placeholder="College/University" class="form-control">
                                                    @error('documents.0.college') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="documents[0][academic_year]" placeholder="Year (e.g. 2020-2021)" class="form-control">
                                                    @error('documents.0.academic_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-1">
                                                    <input type="text" name="documents[0][percentage]" placeholder="Percent" class="form-control">
                                                    @error('documents.0.percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="documents[0][document_name]" placeholder="Document Name" class="form-control">
                                                    @error('documents.0.document_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="file" name="documents[0][document_file]" class="form-control" accept=".png,.jpg,.jpeg,.pdf,.webp">
                                                    @error('documents.0.document_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <button type="button" class="btn btn-primary mt-3" id="addDocBtn">Add More Documents</button>
                                    <button type="button" class="btn btn-primary mt-3 next-tab" data-next="preview">Next Step</button>
                                    <button type="button" class="btn btn-secondary mt-3 prev-tab" data-prev="bank">Previous</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('footerBlock')
    <script>
        // Check if student application exists
        const hasApplication = {{ isset($studentApplication) && $studentApplication->exists ? 'true' : 'false' }};

        // Scholarship details toggle
        $('#received_scholarship').on('change', function() {
            $('#scholarship_details_div').css('display', $(this).val() === 'yes' ? 'block' : 'none');
        });

        // Handle document removal
        $(document).on('click', '.remove-document', function() {
            let documentId = $(this).data('id');
            let $documentEntry = $(this).closest('.document_entry');
            
            Swal.fire({
                title: 'Are you sure?',
                text: 'This document will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route($routeName.'.students.remove_document') }}',
                        method: 'POST',
                        data: {
                            document_id: documentId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $documentEntry.remove();
                                toastr.success('Document removed successfully!');
                            } else {
                                toastr.error('Failed to remove document.');
                            }
                        },
                        error: function() {
                            toastr.error('An error occurred while removing the document.');
                        }
                    });
                }
            });
        });

        // Fees purpose description toggle
        function toggleDescriptionField(selectElement, index) {
            const descriptionDiv = $(selectElement).closest('.fees_entry').find('.fees-purpose-description-div');
            const textarea = descriptionDiv.find('textarea');
            descriptionDiv.css('display', selectElement.value === 'Others' ? 'block' : 'none');
            textarea.prop('required', selectElement.value === 'Others');
        }

        // Update total fees amount
        function updateTotalFees() {
            let total = 0;
            $('.fees-amount').each(function() {
                const value = parseFloat($(this).val()) || 0;
                total += value;
            });
            $('#total_fees_amount').val(total.toFixed(2));
        }

        // Add new fees entry
        $('#addFeesEntryBtn').on('click', function() {
            const container = $('#fees_entries_container');
            const index = container.find('.fees_entry').length;
            
            const newEntry = $(`
                <div class="fees_entry card mb-2 p-3" data-index="${index}">
                    <div class="row gx-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Purpose of Fees Requested *</label>
                            <select name="fees_entries[${index}][fees_purpose]" class="form-control fees-purpose" required>
                                <option value="">Select</option>
                                <option value="Tuition Fees">Tuition Fees</option>
                                <option value="Hostel Fees">Hostel Fees</option>
                                <option value="Mess Fees">Mess Fees</option>
                                <option value="Transport Fees">Transport Fees</option>
                                <option value="Room Rent">Room Rent</option>
                                <option value="Hostel & Mess">Hostel & Mess</option>
                                <option value="Others">Others</option>
                            </select>
                            <div class="form-text">Select the specific purpose for which you are requesting financial aid from ILM.</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-12 mb-2 fees-purpose-description-div" style="display: none;">
                            <label class="form-label">Describe Other Fees Purpose *</label>
                            <textarea name="fees_entries[${index}][fees_purpose_description]" class="form-control" rows="3"></textarea>
                            <div class="form-text">Describe the specific fees you are requesting assistance for.</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Payment Period *</label>
                            <select name="fees_entries[${index}][fees_duration]" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Yearly">Yearly</option>
                                <option value="Semester">Semester</option>
                                <option value="Quarterly">Quarterly</option>
                                <option value="4-months">4-months</option>
                                <option value="5-months">5 Months</option>
                                <option value="Monthly">Monthly</option>
                                <option value="One-time">One-time</option>
                            </select>
                            <div class="form-text">Select the payment period for which the fees amount applies.</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Fees Amount (INR) *</label>
                            <input type="number" step="0.01" name="fees_entries[${index}][fees_amount]" class="form-control fees-amount" required>
                            <div class="form-text">Enter the total amount for this fees purpose in INR.</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm remove-fees-entry w-100">Remove</button>
                        </div>
                    </div>
                </div>
            `);
            container.append(newEntry);

            // Attach event listeners to new fields
            newEntry.find('.fees-purpose').on('change', function() {
                toggleDescriptionField(this, index);
            });
            newEntry.find('.fees-amount').on('input', updateTotalFees);
        });

        // Remove fees entry
        $(document).on('click', '.remove-fees-entry', function() {
            const entry = $(this).closest('.fees_entry');
            if ($('#fees_entries_container .fees_entry').length > 1) {
                entry.remove();
                updateTotalFees();
            } else {
                Swal.fire({
                    title: 'Cannot Remove',
                    text: 'At least one fee entry is required.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });

        // Initialize event listeners for existing fields
        $('.fees-purpose').each(function() {
            toggleDescriptionField(this, $(this).closest('.fees_entry').data('index'));
            $(this).on('change', function() {
                toggleDescriptionField(this, $(this).closest('.fees_entry').data('index'));
            });
        });

        $('.fees-amount').on('input', updateTotalFees);

        // Initial total calculation
        updateTotalFees();

        // Fees submission status toggle
        $('#fees_submission_status').on('change', function() {
            const dateField = $('#fees_submission_date_field');
            if ($(this).val() === 'date') {
                dateField.show();
            } else {
                dateField.hide();
                $('#last_fees_submission_date').val('');
            }
        });

        // Document addition
        $('#addDocBtn').on('click', function() {
            let docIndex = $('#document_container .document_entry').length;
            
            let newRow = $(`
                <div class="document_entry row gx-2 mb-2">
                    <div class="col-md-2">
                        <input type="text" name="documents[${docIndex}][course_name]" placeholder="Course" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="documents[${docIndex}][college]" placeholder="College/University" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="documents[${docIndex}][academic_year]" placeholder="Year (e.g. 2020-2021)" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-1">
                        <input type="text" name="documents[${docIndex}][percentage]" placeholder="Percent" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="documents[${docIndex}][document_name]" placeholder="Document Name" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3">
                        <input type="file" name="documents[${docIndex}][document_file]" class="form-control" accept=".png,.jpg,.jpeg,.pdf,.webp">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            `);
            $('#document_container').append(newRow);
            docIndex++;
        });



        // Function to submit form data
        function submitFormData(isFinalSubmit = false) {
            let $form = $('#studentForm');
            let formData = new FormData($form[0]);
            formData.append('tab', 'documents');
            formData.append('_token', '{{ csrf_token() }}');

            if (isFinalSubmit) {
                formData.set('is_submitted', '1');
            } else {
                formData.set('is_submitted', '0');
            }

            // Clear previous validation errors
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('').css('display', 'none');

            $.ajax({
                url: '{{ route($routeName.'.students.validate_step') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.success) {
                        if (isFinalSubmit) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'Once submitted, you will not be able to edit this form without admin permission.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, submit it!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = '{{ route($routeName.'.students.index') }}';
                                    toastr.success('Form submitted successfully!');
                                }
                            });
                        } else {
                            toastr.success('Documents saved successfully!');
                        }
                    } else {
                        console.log('Validation errors:', data.errors); // Debug: Log errors
                        $.each(data.errors, function(field, errors) {
                            let $input;
                            if (field.includes('documents') || field.includes('fees_entries')) {
                                let fieldName = field.replace(/\.(\d+)\./, '[$1][').replace(/\./g, '][') + ']';
                                $input = $form.find(`input[name="${fieldName}"], select[name="${fieldName}"], textarea[name="${fieldName}"]`);
                                console.log(`Field: ${fieldName}, Found: ${$input.length}`); // Debug: Check if input is found
                            } else {
                                $input = $form.find(`input[name="${field}"], select[name="${field}"], textarea[name="${field}"]`);
                                console.log(`Field: ${field}, Found: ${$input.length}`); // Debug: Check if input is found
                            }
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                let $errorDiv = $input.next('.invalid-feedback');
                                if ($errorDiv.length) {
                                    console.log(`Error for ${field}: ${errors[0]}`); // Debug: Log error message
                                    $errorDiv.text(errors[0]).show();
                                } else {
                                    $errorDiv = $input.closest('.mb-3, .mb-2').find('.invalid-feedback');
                                    if ($errorDiv.length) {
                                        console.log(`Fallback error for ${field}: ${errors[0]}`); // Debug: Log fallback
                                        $errorDiv.text(errors[0]).show();
                                    } else {
                                        console.log(`No invalid-feedback div for ${field}`); // Debug: No div found
                                    }
                                }
                            } else if (field === 'general') {
                                toastr.error(errors.join('<br>'), 'Validation Error');
                            } else {
                                console.log(`Input not found for field: ${field}`); // Debug: Input not found
                            }
                        });
                    }
                },
                error: function(xhr) {
                    console.log('AJAX error:', xhr); // Debug: Log AJAX error
                    toastr.error('An error occurred while validating data.', 'Error');
                }
            });
        }

        // Save button handler
        $('#saveDocuments').on('click', function() {
            submitFormData(false);
        });

        // Final submit handler
        $('#finalSubmit').on('click', function() {
            submitFormData(true);
        });

        // Step navigation and server-side validation
        $('.next-tab').on('click', function() {
            let currentTab = $(this).closest('.tab-pane').attr('id');
            let nextTab = $(this).data('next');

            // Clear previous errors
            let $form = $('#studentForm');
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('').hide();

            // Prepare form data
            let formData = new FormData($form[0]);
            formData.append('tab', currentTab);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route($routeName.'.students.validate_step') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (data.success) {
                        let $nextTabLink = $(`a[href="#${nextTab}"]`);
                        $nextTabLink.removeClass('disabled');
                        let tab = new bootstrap.Tab($nextTabLink[0]);
                        tab.show();
                        toastr.success('Data for this step has been saved successfully!');
                    } else {
                        $.each(data.errors, function(field, messages) {
                            let $input;
                            if (field.includes('documents') || field.includes('fees_entries')) {
                                let fieldName = field.replace(/\.(\d+)\./, '[$1][').replace(/\./g, '][') + ']';
                                $input = $form.find(`input[name="${fieldName}"], select[name="${fieldName}"], textarea[name="${fieldName}"]`);
                                console.log(`Field: ${fieldName}, Found: ${$input.length}`); // Debug: Check if input is found
                            } else {
                                $input = $form.find(`input[name="${field}"], select[name="${field}"], textarea[name="${field}"]`);
                                console.log(`Field: ${field}, Found: ${$input.length}`); // Debug: Check if input is found
                            }
                            if ($input.length) {
                                $input.addClass('is-invalid');
                                let $errorDiv = $input.next('.invalid-feedback');
                                if ($errorDiv.length) {
                                    console.log(`Error for ${field}: ${messages[0]}`); // Debug: Log error message
                                    $errorDiv.text(messages[0]).show();
                                } else {
                                    $errorDiv = $input.closest('.mb-3, .mb-2').find('.invalid-feedback');
                                    if ($errorDiv.length) {
                                        console.log(`Fallback error for ${field}: ${messages[0]}`); // Debug: Log fallback
                                        $errorDiv.text(messages[0]).show();
                                    } else {
                                        console.log(`No invalid-feedback div for ${field}`); // Debug: No div found
                                    }
                                }
                            } else if (field === 'general') {
                                toastr.error(messages.join('<br>'), 'Validation Error');
                            } else {
                                console.log(`Input not found for field: ${field}`); // Debug: Input not found
                            }
                        });
                    }
                },
                error: function(xhr) {
                    console.log('AJAX error:', xhr); // Debug: Log AJAX error
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            const $input = $form.find(`[name="${key}"]`);
                            $input.addClass('is-invalid');
                            const $errorDiv = $input.next('.invalid-feedback');
                            if ($errorDiv.length) {
                                $errorDiv.text(value[0]).show();
                            } else {
                                $input.after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                            }
                            toastr.error(value.join('<br>'), 'Validation Error');
                        });
                    } else {
                        toastr.error('Something went wrong. Please try again.', 'Error');
                    }
                }
            });
        });

        // Prevent direct tab clicks only if no application exists
        if (!hasApplication) {
            $('.nav-tabs .nav-link').on('click', function(e) {
                if ($(this).hasClass('disabled')) {
                    e.preventDefault();
                    toastr.warning('Please complete the previous steps before accessing this tab.', 'Complete Previous Steps');
                }
            });
        }

        // Previous tab navigation (no validation needed)
        $('.prev-tab').on('click', function() {
            let prevTab = $(this).data('prev');
            $(`a[href="#${prevTab}"]`).trigger('click');
        });
    </script>

    <style>
    /* Enhanced Document Form Styling */
    .document_entry .card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
    }

    .document_entry .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transform: translateY(-2px);
    }

    .document_entry .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
    }

    .upload-area {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 2px dashed #dee2e6 !important;
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        border-color: #007bff !important;
        background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%);
    }

    .upload-area input[type="file"] {
        border: none;
        background: transparent;
    }

    .form-label.fw-semibold {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .document_entry .btn-outline-danger:hover {
        transform: scale(1.05);
    }

    .document_entry .card-body {
        padding: 1.5rem;
    }

    /* Button Styling */
     .btn-lg {
         padding: 0.75rem 1.5rem;
         font-size: 1.1rem;
         font-weight: 500;
         border-radius: 0.5rem;
         transition: all 0.3s ease;
     }

     .btn-outline-primary:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
     }

     .btn-primary:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
     }

     .btn-secondary:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
     }

     .btn-success:hover {
         transform: translateY(-2px);
         box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
     }

     #addDocBtn {
         background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
         border: 2px dashed #007bff;
         color: #007bff;
         min-width: 200px;
     }

     #addDocBtn:hover {
         background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
         color: white;
         border-color: #0056b3;
     }

     @media (max-width: 768px) {
         .document_entry .card-body {
             padding: 1rem;
         }
         
         .upload-area {
             padding: 1rem !important;
         }
         
         .btn-lg {
             padding: 0.5rem 1rem;
             font-size: 1rem;
         }
         
         .d-flex.justify-content-between {
             flex-direction: column;
         }
         
         .d-flex.justify-content-between .btn {
             width: 100%;
             margin-bottom: 0.5rem;
         }
         
         .d-flex.gap-2 {
             flex-direction: column;
             width: 100%;
         }
     }
    </style>

    @endslot
@endcomponent
