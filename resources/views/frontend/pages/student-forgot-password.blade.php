@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section">
    <div class="container">
        <div class="row breadcrumb-area m-0">
            <div class="col-lg-12 col-12 text-center">
                <h2 class="text-white breadcrumb-title">Forgot Password</h2>
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Forgot Password</li>
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
                    <h2>Reset Your Password</h2>
                    <p>Enter your registered email to receive a reset link</p>
                </div>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-8 col-12 mx-auto form-blur">
                <form id="student-forgot-form" class="form p-4" method="POST" action="{{ route('student.password.email') }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="forgot-alert" class="alert d-none" role="alert"></div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="forgot-email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="forgot-email" name="email" autocomplete="email">
                                <div class="invalid-feedback" data-field="email"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <button type="submit" class="btn login-btn w-100" id="forgot-submit">
                                    <span class="btn-text">Send Reset Link</span>
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
        var form = $('#student-forgot-form');
        var submitButton = $('#forgot-submit');
        var alertBox = $('#forgot-alert');

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
                        alertBox.removeClass('d-none alert-danger').addClass('alert-success').text(response.message || 'Reset link sent to your email.');
                    } else {
                        alertBox.removeClass('d-none alert-success').addClass('alert-danger').text(response.message || 'Failed to send reset link.');
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

