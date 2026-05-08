@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">Log-in</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item active text-white" aria-current="page">Login</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>


<section class="login-section pt-60 pb-60">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-12 col-12 text-center">
                <div class="sec-title">
                    <h2>Login To Your Account</h2>
                    <p>Enter Your Details to Login</p>
                </div>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-8 col-12 mx-auto">
                <div class="row">
                    <div class="col-lg-6 p-0 bg-brown d-flex align-items-center">
                        <div class="login-txt-area p-4">
                            <img src="{{ asset('frontend/assets/images/logo.png') }}" class="img-fluid mb-3" alt="Cuttack District Archery Association">
                            <h2 class="text-white">Cuttack District Archery Association</h2>
                            <p class="text-white">Archery Association of India came into existence in 1973, with the primary objective to organize, encourage, and promote the game of Archery in the country by providing proper training facilities to Indian Archers.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 form-blur p-0">
                        <form id="student-login-form" class="form p-4" method="POST" action="{{ route('archer.login.submit') }}">
                            @csrf
                            <div class="row">
                                <h2>Login To Your Account</h2>
                                <p>Enter Your Details to Login</p>
                                <div class="col-lg-12">
                                    <div id="login-alert" class="alert d-none" role="alert"></div>
                                </div>
                                <div class="col-lg-12 mb-3">
                                        <label for="login-email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="login-email" name="email" autocomplete="email">
                                        <div class="invalid-feedback" data-field="email"></div>
                                </div>
                                <div class="col-lg-12 mb-3">
                                        <label for="login-password" class="form-label">Password *</label>
                                        <input type="password" class="form-control" id="login-password" name="password" autocomplete="current-password">
                                        <div class="invalid-feedback" data-field="password"></div>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="form-label">Forgot Password?</span>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-3">
                                        <button type="submit" class="btn common-btn rounded-pill login-btn" id="login-submit">
                                            <span class="btn-text">Login</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                </div>
                                <div class="col-lg-12">
                                        <label class="form-label">Don't have an account?
                                            <a href="{{ route('archer.register') }}">Register</a>
                                        </label>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
@parent
<script>
    $(function () {
        var form = $('#student-login-form');
        var submitButton = $('#login-submit');
        function clearErrors() {
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
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Logged in successfully.');
                        }
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'Login failed.');
                        }
                    }
                },
                error: function (xhr) {
                    var data = xhr.responseJSON || {};
                    var message = data.message || 'An error occurred during login. Please try again.';

                    if (xhr.status === 422 && data.errors) {
                        Object.keys(data.errors).forEach(function (field) {
                            var fieldErrors = data.errors[field];
                            var input = form.find('[name="' + field + '"]');
                            var feedback = form.find('.invalid-feedback[data-field="' + field + '"]');
                            input.addClass('is-invalid');
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
