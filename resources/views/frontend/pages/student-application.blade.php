@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section">
    <div class="container">
        <div class="row breadcrumb-area m-0">
            <div class="col-lg-12 col-12 text-center">
                <h2 class="text-white breadcrumb-title">Application Form</h2>
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}" class="text-white">Dashboard</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Application</li>
                  </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="register-form-area pt-70 pb-70">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-12 text-center">
                <div class="sec-title">
                    <h2>Application Form</h2>
                    <p>The detailed online application form will be available here.</p>
                </div>
            </div>
            <div class="col-lg-12">
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <p class="text-center">Please contact the association for assistance with your application if this form is not yet enabled.</p>
            </div>
        </div>
    </div>
</section>
@endsection

