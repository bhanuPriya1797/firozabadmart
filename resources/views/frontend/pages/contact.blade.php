@extends('frontend.layouts.main')

@section('content')
@php
    $settings = \App\Helpers\CustomHelper::getSettings();
@endphp

	<section class="breadcrumb-section breadcrumb-area">
		<div class="container">
			<div class="row m-0">
				<div class="col-lg-12 col-12 text-center">
					<h2 class="text-white breadcrumb-title">Contact Us</h2>
					<nav aria-label="breadcrumb">
					  <ol class="breadcrumb justify-content-center">
					    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
					    <li class="breadcrumb-item active text-white" aria-current="page">Contact Us</li>
					  </ol>
					</nav>
				</div>
			</div>
		</div>
	</section>

<section class="contact-detail-area pt-60 pb-60">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="address-info">
                    <i class="fa-solid fa-location-dot"></i>
                    <h3>Address</h3>
                    <p>{{ $settings['site_address'] ?? 'Plot No.2/D 798, Markat Nagar, CDA, Sector-11, Cuttack-753014, Odisha' }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="address-info">
                    <i class="fa-solid fa-phone"></i>
                    <h3>Phone</h3>
                    <p>{{ $settings['contact_phone'] ?? '+91-9438553446' }}</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="address-info">
                    <i class="fa-solid fa-envelope"></i>
                    <h3>Email</h3>
                    <p>{{ $settings['contact_email'] ?? 'info@cdaa.in' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="contact-form-area pb-60">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12 col-12 mx-auto">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="map-box">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d59807.60511440884!2d85.81096189362555!3d20.466206838963604!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sPlot%20No.2%2FD%20798%2C%20Markat%20Nagar%2C%20CDA%2C%20Sector-11%2C%20Cuttack-753014%2C%20Odisha!5e0!3m2!1sen!2sin!4v1767177488198!5m2!1sen!2sin" width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>					
                    </div>
                    <div class="col-lg-7">					
                        <form action="{{ route('contact.submit') }}" method="POST" class="form contact-form p-4">
                            @csrf
                            <div class="row">
                                <h2 class="">Send Us Message</h2>
                                <p>Got a query, or just want to say Hi. Use contact us form below to connect with us.</p>
                                
                                @if(session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="col-lg-6 col-md-12 col-12">
                                    <div class="mb-3">
                                        <label for="Name" class="form-label">Name *</label>
                                        <input type="text" class="form-control" name="name" id="Name" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12 col-12">
                                    <div class="mb-3">
                                        <label for="Email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" id="Email" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12 col-12">
                                    <div class="mb-3">
                                        <label for="Phone" class="form-label">Phone No *</label>
                                        <input type="text" class="form-control" name="phone" id="Phone" value="{{ old('phone') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12 col-12">
                                    <div class="mb-3">
                                        <label for="Subject" class="form-label">Subject *</label>
                                        <input type="text" class="form-control" name="subject" id="Subject" value="{{ old('subject') }}">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-12">
                                    <div class="mb-3">
                                        <label for="Message" class="form-label">Message</label>
                                        <textarea class="form-control" name="message" id="Message" rows="3" required>{{ old('message') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12 col-12">
                                    <div class="mb-3">
                                        <button type="submit" class="btn common-btn text-white">
                                            Send Message
                                        </button>
                                    </div>
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
