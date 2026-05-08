<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glass Bangles, Fancy Lights & Glassware Supplier | Firozabad Mart</title>
    <meta name="description" content="Manufacturer and wholesaler of glass bangles, chandeliers, decorative lighting and glassware in India.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
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