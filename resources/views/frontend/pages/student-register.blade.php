@extends('frontend.layouts.main')

@section('content')
	<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">Register</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb  justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item active text-white" aria-current="page">Register</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>

<section class="register-form-area pt-60 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-12 text-center">
                <div class="sec-title">
                    <h2>Registration Form / Personal Information</h2>
                    <p>Please enter your personal details</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 mx-auto form-blur p-0">
                <form id="student-register-form" class="form p-4" method="POST" action="{{ route('archer.register.submit') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="family_lineage" value="NON-SYED">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="register-alert" class="alert d-none" role="alert"></div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-full-name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="reg-full-name" name="first_name" autocomplete="name">
                                <div class="invalid-feedback" data-field="first_name"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-email" class="form-label">Email Id *</label>
                                <input type="email" class="form-control" id="reg-email" name="email" autocomplete="email">
                                <div class="invalid-feedback" data-field="email"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-gender" class="form-label">Gender *</label>
                                <select class="form-select" id="reg-gender" name="gender">
                                    <option value="Male" selected>Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback" data-field="gender"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="reg-password" name="password" autocomplete="new-password">
                                <div class="invalid-feedback" data-field="password"></div>
                                <div class="form-text mt-1">
                                    <ul class="list-unstyled mb-0 small" id="password-criteria">
                                        <li data-crit="length" class="text-muted">8–20 characters</li>
                                        <li data-crit="uppercase" class="text-muted">At least one uppercase letter</li>
                                        <li data-crit="lowercase" class="text-muted">At least one lowercase letter</li>
                                        <li data-crit="number" class="text-muted">At least one number</li>
                                        <li data-crit="special" class="text-muted">At least one special character</li>
                                        <li data-crit="match" class="text-muted">Passwords match</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-password-confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="reg-password-confirmation" name="password_confirmation" autocomplete="new-password">
                                <div class="invalid-feedback" data-field="password_confirmation"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-dob" class="form-label">Date of birth</label>
                                <input type="date" class="form-control" id="reg-dob" name="dob">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-phone" class="form-label">Phone *</label>
                                <input type="tel" class="form-control" id="reg-phone" name="phone">
                                <div class="invalid-feedback" data-field="phone"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-father-name" class="form-label">Father Name *</label>
                                <input type="text" class="form-control" id="reg-father-name" name="father_name">
                                <div class="invalid-feedback" data-field="father_name"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-mother-name" class="form-label">Mother Name *</label>
                                <input type="text" class="form-control" id="reg-mother-name" name="mother_name">
                                <div class="invalid-feedback" data-field="mother_name"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-alt-phone" class="form-label">Alternate Phone</label>
                                <input type="tel" class="form-control" id="reg-alt-phone" name="alternate_phone">
                                <div class="invalid-feedback" data-field="alternate_phone"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-whatsapp" class="form-label">WhatsApp Number *</label>
                                <input type="tel" class="form-control" id="reg-whatsapp" name="whatsapp_number">
                                <div class="invalid-feedback" data-field="whatsapp_number"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-marital-status" class="form-label">Marital Status *</label>
                                <select class="form-select" id="reg-marital-status" name="marital_status">
                                    <option value="">Select</option>
                                    <option value="Married">Married</option>
                                    <option value="Single">Single</option>
                                </select>
                                <div class="invalid-feedback" data-field="marital_status"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3 d-flex align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="reg-is-minor" name="is_minor" value="1">
                                    <label class="form-check-label" for="reg-is-minor">Is Minor</label>
                                </div>
                                <div class="invalid-feedback d-block" data-field="is_minor"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-aadhar-number" class="form-label">Aadhar Card Number *</label>
                                <input type="text" class="form-control" id="reg-aadhar-number" name="aadhar_card_number">
                                <div class="invalid-feedback" data-field="aadhar_card_number"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <label for="reg-aadhar-file" class="form-label">Upload Aadhar Card/ Masked Adhaar Card/ Driving License *</label>
                                <input class="form-control" type="file" id="reg-aadhar-file" name="aadhar_document" accept=".pdf,image/*">
                                <small class="text-muted">Allowed types: PDF, JPG, JPEG, PNG. Max size 5 MB.</small>
                                <div class="invalid-feedback d-block" data-field="aadhar_document"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="reg-terms" name="terms" value="1">
                                <label class="form-check-label" for="reg-terms">
                                    I agree to the Terms and Conditions &amp; Privacy Policy
                                </label>
                                <div class="invalid-feedback d-block" data-field="terms"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="mb-3">
                                <button type="submit" class="submit-btn btn common-btn rounded-pill" id="register-submit">
                                    <span class="btn-text">Register</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <p>Already have an account?
                                <a href="{{ route('archer.login') }}">Login</a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
@parent
<script>
    $(function () {
        var form = $('#student-register-form');
        var submitButton = $('#register-submit');
        function clearErrors() {
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        }
            function setCrit(name, state) {
                var el = $('#password-criteria').find('[data-crit="'+name+'"]');
                el.removeClass('text-muted text-danger text-success');
                if (state === null) {
                    el.addClass('text-muted');
                } else if (state) {
                    el.addClass('text-success');
                } else {
                    el.addClass('text-danger');
                }
            }
            function updatePasswordHints() {
                var pwd = $('#reg-password').val();
                var confirm = $('#reg-password-confirmation').val();
                var lengthOk = pwd.length >= 8 && pwd.length <= 20;
                var upperOk = /[A-Z]/.test(pwd);
                var lowerOk = /[a-z]/.test(pwd);
                var numberOk = /\d/.test(pwd);
                var specialOk = /[\W_]/.test(pwd);
                var matchOk = confirm.length ? pwd === confirm : null;
                setCrit('length', pwd.length ? lengthOk : null);
                setCrit('uppercase', pwd.length ? upperOk : null);
                setCrit('lowercase', pwd.length ? lowerOk : null);
                setCrit('number', pwd.length ? numberOk : null);
                setCrit('special', pwd.length ? specialOk : null);
                setCrit('match', matchOk);
            }
            $('#reg-password, #reg-password-confirmation').on('input', updatePasswordHints);
            updatePasswordHints();

        form.on('submit', function (e) {
            e.preventDefault();
            clearErrors();
                updatePasswordHints();

            submitButton.prop('disabled', true);
            submitButton.find('.btn-text').addClass('d-none');
            submitButton.find('.spinner-border').removeClass('d-none');

            var formData = new FormData(form[0]);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Registration successful.');
                        }
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'Registration failed.');
                        }
                    }
                },
                error: function (xhr) {
                    var data = xhr.responseJSON || {};
                    var message = data.message || 'An error occurred during registration. Please try again.';

                    if (xhr.status === 422 && data.errors) {
                        Object.keys(data.errors).forEach(function (field) {
                            var fieldErrors = data.errors[field];
                            var input = form.find('[name="' + field + '"]');
                            var feedback = form.find('.invalid-feedback[data-field="' + field + '"]');
                            if (input.length) {
                                input.addClass('is-invalid');
                            }
                            if (feedback.length) {
                                feedback.text(fieldErrors.join(' '));
                            }
                        });
                        message = 'Please correct the highlighted errors and try again.';
                    }

                    if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    }
                },
                complete: function () {
                    submitButton.prop('disabled', false);
                    submitButton.find('.btn-text').removeClass('d-none');
                    submitButton.find('.spinner-border').addClass('d-none');
                }
            });
        });
    });
</script>
@endsection
