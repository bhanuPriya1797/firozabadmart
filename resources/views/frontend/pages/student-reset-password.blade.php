@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section">
    <div class="container">
        <div class="row breadcrumb-area m-0">
            <div class="col-lg-12 col-12 text-center">
                <h2 class="text-white breadcrumb-title">Reset Password</h2>
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Reset Password</li>
                  </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="login-form-area pt-70 pb-70">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-12 col-12 text-center">
                <div class="sec-title">
                    <h2>Create New Password</h2>
                    <p>Enter a strong password for your account</p>
                </div>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-8 col-12 mx-auto form-blur">
                <form id="student-reset-form" class="form p-4" method="POST" action="{{ route('student.password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="reset-alert" class="alert d-none" role="alert"></div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" value="{{ $email }}" disabled>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="reset-password" class="form-label">New Password *</label>
                                <input type="password" class="form-control" id="reset-password" name="password" autocomplete="new-password">
                                <div class="invalid-feedback" data-field="password"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="reset-password-confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="reset-password-confirmation" name="password_confirmation" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <button type="submit" class="btn login-btn w-100" id="reset-submit">
                                    <span class="btn-text">Reset Password</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <a href="{{ route('student.login') }}">Back to Login</a>
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
        var form = $('#student-reset-form');
        var submitButton = $('#reset-submit');
        var alertBox = $('#reset-alert');

        function clearErrors() {
            alertBox.addClass('d-none').removeClass('alert-danger alert-success').text('');
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        }

        form.on('submit', function (e) {
            e.preventDefault();
            clearErrors();

            submitButton.prop('disabled', true);
            submitButton.find('.btn-text').addClass('d-none');
            submitButton.find('.spinner-border').removeClass('d-none');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.success) {
                        alertBox.removeClass('d-none alert-danger').addClass('alert-success').text(response.message || 'Password reset successfully.');
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        alertBox.removeClass('d-none alert-success').addClass('alert-danger').text(response.message || 'Failed to reset password.');
                    }
                },
                error: function (xhr) {
                    var data = xhr.responseJSON || {};
                    var message = data.message || 'An error occurred. Please try again.';

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

                    alertBox.removeClass('d-none alert-success').addClass('alert-danger').text(message);
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

