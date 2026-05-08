<!DOCTYPE html>
<html lang="en" data-x="html" data-x-toggle="html-overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($seo['title'] ?? null) ?: config('app.name', '') }}</title>
    <meta name="description" content="{{ ($seo['description'] ?? null) ?: '' }}">
    <meta name="keywords" content="{{ ($seo['keywords'] ?? null) ?: '' }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('frontend/css/vendors.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/main.css') }}">
    @stack('styles')
</head>
<body>

    <main>
        @include('frontend.partials.header')
        <div class="header-margin"></div>
        
        @yield('content')
        
        @include('frontend.partials.footer')
    </main>
    
    <script src="{{ asset('frontend/js/vendors.js') }}"></script>
    <script src="{{ asset('frontend/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
