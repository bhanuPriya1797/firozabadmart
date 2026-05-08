@extends('frontend.layouts.main')

@section('content')
<section class="breadcrumb-section">
    <div class="container">
        <div class="row breadcrumb-area m-0">
            <div class="col-lg-12 col-12 text-center">
                <h2 class="text-white breadcrumb-title">My Applications</h2>
                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb justify-content-center">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('archer.dashboard') }}" class="text-white">Dashboard</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Applications</li>
                  </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="register-form-area pt-70 pb-70">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-12">
                @if(session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
            </div>
            <div class="col-lg-12 text-center">
                <div class="sec-title">
                    <h2>Your Applications</h2>
                    <p>Track the status of your applications</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                @if($applications->isEmpty())
                    <p class="text-center">No applications found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Application #</th>
                                    <th>Submitted On</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $application)
                                    <tr>
                                        <td>{{ $application->application_number }}</td>
                                        <td>{{ $application->submitted_at ? $application->submitted_at->format('d M Y') : '-' }}</td>
                                        <td>
                                            <span class="{{ $application->status_badge_class }}">
                                                {{ $application->status_text }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
