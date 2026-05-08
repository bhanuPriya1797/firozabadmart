@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section">
    <div class="container">
        <div class="row breadcrumb-area m-0">
            <div class="col-lg-12 col-12 text-center">
                <h2 class="text-white breadcrumb-title">My Dashboard</h2>
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Dashboard</li>
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
                    <h2>Welcome, {{ auth('archer')->user()->first_name ?? 'Archer' }}</h2>
                    <p>Manage your applications and download your pass</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Application Status</h5>
                        <p class="card-text">View the status of your submitted applications.</p>
                        <a href="{{ route('archer.applications.list') }}" class="btn common-btn">View Applications</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Download Pass</h5>
                        <p class="card-text">Download the PDF pass for your latest application.</p>
                        <span class="btn common-btn disabled">Download Pass</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Logout</h5>
                        <p class="card-text">Securely logout from your account.</p>
                        <form method="POST" action="{{ route('archer.logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
