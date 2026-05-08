@extends('layouts.front')

@section('content')

<!-- Header Section (Hero Carousel only, Navbar is in layout) -->
<header class="hero-header position-relative">
    <!-- HERO CAROUSEL -->
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

            <!-- SLIDE 1 -->
            <div class="carousel-item active">
                <div class="hero-slide" style="background-image: url('{{ asset('assets/image/h3-slider3.jpg') }}');">
                    
                    <!-- Overlay Image -->
                    <img src="{{ asset('assets/image/slider-overlay-img.png') }}" class="overlay-shape">

                    <div class="container">
                        <div class="col-lg-6 text-white pt-5">

                            <span class="small fw-semibold text-uppercase">TRUSTED BY INDUSTRY LEADERS</span>

                            <h1 class="display-4 fw-bold my-4">
                                Web & App Development <br> Digital Transformation
                            </h1>

                            <p class="lead mb-4">
                                We engineer scalable products and AI-powered solutions that drive growth.
                            </p>

                            <a href="{{ url('/services') }}" class="btn btn-primary gradient-btn px-4 py-2">
                                EXPLORE SERVICES <i class="bi bi-arrow-up-right"></i>
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2 -->
            <div class="carousel-item">
                <div class="hero-slide" style="background-image: url('{{ asset('assets/image/h1-banner8.jpg') }}');">
                    
                    <!-- Overlay Image -->
                    <img src="{{ asset('assets/image/slider-overlay-img.png') }}" class="overlay-shape">

                    <div class="container">
                        <div class="col-lg-6 text-white pt-5">

                            <span class="small fw-semibold text-uppercase">INNOVATE WITH CALCUNEXT</span>

                            <h1 class="display-4 fw-bold my-4">
                                Product Engineering <br> Application Development
                            </h1>

                            <p class="lead mb-4">
                                Modern web, native and cross‑platform apps with enterprise-grade quality.
                            </p>

                            <a href="{{ url('/services') }}" class="btn btn-primary gradient-btn px-4 py-2">
                                EXPLORE SERVICES <i class="bi bi-arrow-up-right"></i>
                            </a>

                        </div>
                    </div>
                </div>
            </div>

        </div>


        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        
        <!-- Pagination (Indicators) -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        </div>
        
    </div>
</header>

<!-- Industry We Serve Section -->
<section class="position-relative" data-aos="fade-up"  data-aos="fade-up" data-aos-easing="ease-out-cubic">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary small fw-semibold gradient">
                <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                INDUSTRIES
            </span>
            <h2 class="fw-bold display-5 mt-3">Industries We Serve</h2>
        </div>
        <div class="row g-4">
            <div class="col-sm-6 col-lg-4">
                <div class="p-4 border rounded-4 h-100 d-flex">
                    <i class="bi bi-bag-check gradient-icon pe-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Ecommerce</h6>
                        <small class="text-muted">Stores, marketplaces, loyalty and personalization.</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="p-4 border rounded-4 h-100 d-flex">
                    <i class="bi bi-heart-pulse gradient-icon pe-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Healthcare</h6>
                        <small class="text-muted">Telemedicine, patient portals, data security.</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="p-4 border rounded-4 h-100 d-flex">
                    <i class="bi bi-buildings gradient-icon pe-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Real Estate</h6>
                        <small class="text-muted">Listings, CRMs, virtual tours, lead-gen.</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="p-4 border rounded-4 h-100 d-flex">
                    <i class="bi bi-currency-bitcoin gradient-icon pe-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">FinTech</h6>
                        <small class="text-muted">Payments, KYC, analytics and dashboards.</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="p-4 border rounded-4 h-100 d-flex">
                    <i class="bi bi-globe2 gradient-icon pe-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Logistics</h6>
                        <small class="text-muted">Supply chain, tracking, fleet management.</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="p-4 border rounded-4 h-100 d-flex">
                    <i class="bi bi-mortarboard gradient-icon pe-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">EdTech</h6>
                        <small class="text-muted">LMS, virtual classrooms, student engagement.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section mb-5" data-aos="fade-up" data-aos-easing="ease-out-cubic">
    <div class="container">
        
        <div class="row align-items-center">

            <!-- Left Text -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <span class="text-primary small fw-semibold gradient">
                    <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                    CALCUNEXT STATS
                </span>
                <h2 class="fw-bold display-5 mt-3">
                    We Are Professional <br> And Experienced
                </h2>
                <p class="text-muted mt-3">
                    Our track record speaks for itself. We deliver high-performance 
                    solutions that scale with your business.
                </p>
                
                <a href="{{ url('/about') }}" class="btn btn-primary gradient-btn px-4 py-2 mt-3">
                    LEARN MORE <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>

            <!-- Right Stats Grid -->
            <div class="col-lg-7">
                
                <div class="position-relative p-4">
                    
                    <!-- Grid Layout for Stats -->
                    <div class="table-responsive">
                        <table class="table table-borderless m-0">
                            <tbody>
                                <tr>
                                    <td class="pb-4">
                                        <div class="stat-flex">
                                            <span class="stat-icon-bg"><i class="bi bi-people"></i></span>
                                            <span class="stat-info">
                                                <h3>200+ Members</h3>
                                                <p>Expert Team</p>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="pb-4">
                                        <div class="stat-flex">
                                            <span class="stat-icon-bg"><i class="bi bi-emoji-smile"></i></span>
                                            <span class="stat-info">
                                                <h3>100% Satisfaction</h3>
                                                <p>Client Feedback</p>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="stat-flex">
                                            <span class="stat-icon-bg"><i class="bi bi-globe2"></i></span>
                                            <span class="stat-info">
                                                <h3>21+ Countries</h3>
                                                <p>Global Presence</p>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stat-flex">
                                            <span class="stat-icon-bg"><i class="bi bi-card-checklist"></i></span>
                                            <span class="stat-info">
                                                <h3>500+ Solutions</h3>
                                                <p>Delivered</p>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Right Decorative Gradient Shape -->
                    <div class="shape-right"></div>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- Portfolio Section -->
<section class="portfolio-section py-5" data-aos="fade-up" data-aos-easing="ease-out-cubic">
    <div class="container section-padding">

        <div class="text-center mb-5">
            <span class="text-primary small fw-semibold gradient">
                <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                OUR PORTFOLIO
            </span>

            <h2 class="fw-bold display-5 mt-3 text-white">
                Our Selected Work
            </h2>
        </div>

        <div class="row g-4">

            <!-- Portfolio Card 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="portfolio-card">
                    <a href="#" target="_blank">
                        <img src="{{ asset('assets/image/portfolio-08-1024x574.jpg') }}" class="portfolio-img" alt="">
                        <i class="bi bi-arrow-up-right-circle-fill"></i>
                        <div class="portfolio-content text-center">
                            <!-- <img src="{{ asset('assets/image/icon1.png') }}" class="portfolio-icon"> -->
                            <h4>Digital experience platforms</h4>
                             <p>We continually align technology to IT standards and best practices.</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Portfolio Card 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="portfolio-card">
                    <a href="#" target="_blank">
                    <img src="{{ asset('assets/image/portfolio-06-1024x574.jpg') }}" class="portfolio-img" alt="">
                    <i class="bi bi-arrow-up-right-circle-fill"></i>
                    <div class="portfolio-content text-center">
                        <!-- <img src="{{ asset('assets/image/icon2.png') }}" class="portfolio-icon"> -->
                        <h4>Business process solutions</h4>
                        <p>We continually align technology to IT standards and best practices.</p>
                    </div>
                    </a>
                </div>
            </div>

            <!-- Portfolio Card 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="portfolio-card">
                    <a href="#" target="_blank">
                    <img src="{{ asset('assets/image/portfolio-03-1024x574.jpg') }}" class="portfolio-img" alt="">
                    <i class="bi bi-arrow-up-right-circle-fill"></i>
                    <div class="portfolio-content text-center">
                        <!-- <img src="{{ asset('assets/image/icon3.png') }}" class="portfolio-icon"> -->
                        <h4>Disaster Recovery & Backup</h4>
                         <p>We continually align technology to IT standards and best practices.</p>
                    </div>
                    </a>
                </div>
            </div>

        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('tour-packages') }}" class="btn btn-primary gradient-btn px-4 py-2 mt-4">
                VIEW MORE <i class="bi bi-arrow-up-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Working Process Section -->
<section class="working-process-section position-relative" data-aos="fade-up" data-aos-easing="ease-out-cubic">

    <!-- Background World Map -->
    <div class="process-bg"></div>

    <div class="container position-relative section-padding">

        <!-- Section Heading -->
        <div class="text-center mb-5">
            <span class="text-primary small fw-semibold gradient">
                <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                WORKING PROCESS
            </span>

            <h2 class="fw-bold display-5 mt-3">
                Get your it solutions in <br> 3 easy steps
            </h2>
        </div>

        <div class="row text-center justify-content-center gx-5 position-relative">

            <!-- Step 1 -->
            <div class="col-lg-4 col-md-6 mb-5 position-relative">

                <div class="process-img-wrapper mx-auto mb-4">
                    <img src="{{ asset('assets/image/process-image-1.jpg') }}" class="process-img">
                    <span class="step-badge">01</span>
                </div>

                <h5 class="fw-bold">Discover & Plan</h5>
                <p class="text-muted mt-3">
                    We align on goals, conduct discovery, and define a roadmap to outcomes.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="col-lg-4 col-md-6 mb-5 position-relative">

                <div class="process-img-wrapper mx-auto mb-4">
                    <img src="{{ asset('assets/image/process-image-2.jpg') }}" class="process-img">
                    <span class="step-badge">02</span>
                </div>

                <h5 class="fw-bold">Design & Engineering</h5>
                <p class="text-muted mt-3">
                    We design experiences and build high‑quality products across web and mobile.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="col-lg-4 col-md-6 mb-5 position-relative">

                <div class="process-img-wrapper mx-auto mb-4">
                    <img src="{{ asset('assets/image/process-image-3.jpg') }}" class="process-img">
                    <span class="step-badge">03</span>
                </div>

                <h5 class="fw-bold">Launch & Support</h5>
                <p class="text-muted mt-3">
                    We deploy, monitor, and provide continuous improvement with SLA-backed support.
                </p>
            </div>

        </div>

    </div>
</section>


<!-- TECHNOLOGY SECTION -->
<section class="technology-section  position-relative" data-aos="fade-up" data-aos-easing="ease-out-cubic">
    <div class="container-fluid px-0 section-padding">
        <div class="row g-0 align-items-center">

            <!-- LEFT IMAGE (5 columns) -->
            <div class="col-lg-5 position-relative">
                <div class="tech-left-img"></div>

                <img src="{{ asset('assets/image/h1-bg6.png') }}" alt="" class="tech-overlay-img">

                <div class="tech-color-overlay"></div>
            </div>

            <!-- RIGHT CONTENT (7 columns) -->
            <div class="col-lg-7 p-5 ">

                <span class="text-primary small fw-semibold gradient">
                    <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                    TECHNOLOGY INDEX
                </span>

                <h2 class="fw-bold display-5 mt-3 mb-4">
                    We Are Always Best For <br> Technology Solution
                </h2>

                <!-- Icons Row -->
                <div class="row mb-4">

                    <div class="col-md-6 d-flex align-items-start mb-4">
                        <div class="icon-box me-3">
                            <i class="bi bi-gear-fill text-primary fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Experience</h6>
                            <small class="text-muted">We gives you bests service to your project.</small>
                        </div>
                    </div>

                    <div class="col-md-6 d-flex align-items-start mb-4">
                        <div class="icon-box me-3">
                            <i class="bi bi-chat-left-quote-fill text-primary fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Quick Support</h6>
                            <small class="text-muted">We Always Here at your support and help.</small>
                        </div>
                    </div>

                </div>

                <!-- PROGRESS BARS -->
                <div class="mb-4">

                <div class="d-flex justify-content-between mb-2">
                    <small class="fw-semibold">IT Management</small>
                    <span class="badge bg-dark">95%</span>
                </div>
                <div class="progress tech-progress mb-4">
                    <div class="progress-bar" data-progress="95"></div>
                </div>
            
                <div class="d-flex justify-content-between mb-2">
                    <small class="fw-semibold">Data Security</small>
                    <span class="badge bg-dark">80%</span>
                </div>
                <div class="progress tech-progress mb-4">
                    <div class="progress-bar" data-progress="80"></div>
                </div>
            
                <div class="d-flex justify-content-between mb-2">
                    <small class="fw-semibold">Technology Solution</small>
                    <span class="badge bg-dark">90%</span>
                </div>
                <div class="progress tech-progress mb-4">
                    <div class="progress-bar" data-progress="90"></div>
                </div>
            
            </div>


                <p class="text-muted">
                    We help businesses achieve strategic technology transformation, 
                    minimising business risk while maximising infrastructure value.
                </p>

            </div>

        </div>
    </div>
</section>


<!-- Testimonial Section -->
<section class="testimonial-section py-5 mb-5" data-aos="fade-up" data-aos-easing="ease-out-cubic">
    <!-- Background Image -->
    <div class="testimonial-bg"></div>

    <!-- Color Overlay -->
    <div class="testimonial-overlay"></div>
    <div class="container section-padding">

        <!-- Section Title -->
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold small gradient">
                <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                WHAT CLIENTS SAY
            </span>
            <h2 class="fw-bold display-6 text-dark mt-2">
                Hear What Our Global Clients Say
            </h2>
        </div>

        <!-- Swiper Wrapper -->
        <div class="position-relative">
            <div class="swiper testimonialSwiper">
                <div class="swiper-wrapper">

                    <!-- Slide 1 -->
                    <div class="swiper-slide">
                        <div class="testimonial-card p-4 rounded-4 shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('assets/image/avatar-2.png') }}" class="rounded-circle me-3" width="55" height="55" alt="">
                                    <div>
                                        <h6 class="fw-bold mb-0">Makhaia Antitni</h6>
                                        <small class="text-muted">Digital Marketer</small>
                                    </div>
                                </div>
                                <i class="bi bi-quote fs-2 text-primary"></i>
                            </div>
                            <p class="text-muted mb-3">
                                "Working with the team has been a real pleasure and a real experience…"
                            </p>
                            <div class="text-primary">★★★★★</div>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="swiper-slide">
                        <div class="testimonial-card p-4 rounded-4 shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('assets/image/avatar-3.png') }}" class="rounded-circle me-3" width="55" height="55" alt="">
                                    <div>
                                        <h6 class="fw-bold mb-0">Mike Hardson</h6>
                                        <small class="text-muted">UI/UX Designer</small>
                                    </div>
                                </div>
                                <i class="bi bi-quote fs-2 text-primary"></i>
                            </div>
                            <p class="text-muted mb-3">
                                “Very well thought out and articulate communication…”
                            </p>
                            <div class="text-primary">★★★★★</div>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div class="swiper-slide">
                        <div class="testimonial-card p-4 rounded-4 shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset('assets/image/avatar-2.png') }}" class="rounded-circle me-3" width="55" height="55" alt="">
                                    <div>
                                        <h6 class="fw-bold mb-0">Mike Hardson</h6>
                                        <small class="text-muted">SEO Expert</small>
                                    </div>
                                </div>
                                <i class="bi bi-quote fs-2 text-primary"></i>
                            </div>
                            <p class="text-muted mb-3">
                                “Extensive IT solutions delivered by highly capable teams…”
                            </p>
                            <div class="text-primary">★★★★★</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Pagination -->
            <div class="swiper-pagination pt-4"></div>

        </div>
    </div>
</section>

<!-- Why Trust CalcuNext Section -->
<section class="position-relative" data-aos="fade-up" data-aos-easing="ease-out-cubic">
    <div class="container section-padding">
        <div class="text-center mb-5">
            <span class="text-primary small fw-semibold gradient">
                <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                TRUST & QUALITY
            </span>
            <h2 class="fw-bold display-6 text-dark mt-2">Why Teams Trust CalcuNext</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="p-4 border rounded-4 h-100 text-center">
                    <div class="mb-2"><i class="bi bi-hand-thumbs-up fs-2 text-primary"></i></div>
                    <h6 class="fw-bold mb-1">4.9/5 Satisfaction</h6>
                    <small class="text-muted">Consistently rated by clients.</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 border rounded-4 h-100 text-center">
                    <div class="mb-2"><i class="bi bi-clock-history fs-2 text-primary"></i></div>
                    <h6 class="fw-bold mb-1">95% On‑Time Delivery</h6>
                    <small class="text-muted">Reliable execution at scale.</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 border rounded-4 h-100 text-center">
                    <div class="mb-2"><i class="bi bi-shield-lock fs-2 text-primary"></i></div>
                    <h6 class="fw-bold mb-1">Security & Compliance</h6>
                    <small class="text-muted">Best practices end‑to‑end.</small>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 border rounded-4 h-100 text-center">
                    <div class="mb-2"><i class="bi bi-people fs-2 text-primary"></i></div>
                    <h6 class="fw-bold mb-1">92% Retention</h6>
                    <small class="text-muted">Long‑term partnerships.</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ's Section-->
<section class="position-relative" data-aos="fade-up" data-aos-easing="ease-out-cubic">
    <div class="container section-padding">
        <div class="text-center mb-5">
            <span class="text-primary small fw-semibold gradient">
                <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                FAQS
            </span>
            <h2 class="fw-bold display-6 text-dark mt-2">Answers to common questions about our services.</h2>
        </div>
        <div class="row justify-content-center">
            <div class="faq-wrapper col-lg-10">
                            
                <!-- FAQ ITEM 1 (OPEN) -->
                <div class="faq-item active">
                    <button class="faq-question">
                        <span>1. How do projects typically start?</span>
                        <div class="icon">−</div>
                    </button>
                    <div class="faq-answer" style="display: block;">
                        <p>
                            We begin with discovery to align goals, define scope, and plan a roadmap.
                        </p>
                    </div>
                </div>

                <!-- FAQ ITEM 2 -->
                <div class="faq-item">
                    <button class="faq-question">
                        <span>2. What technologies do you use?</span>
                        <div class="icon">+</div>
                    </button>
                    <div class="faq-answer">
                        <p>
                            We apply fit‑for‑purpose stacks across web, mobile, cloud, data and AI.
                        </p>
                    </div>
                </div>

                <!-- FAQ ITEM 3 -->
                <div class="faq-item">
                    <button class="faq-question">
                        <span>3. How is quality ensured?</span>
                        <div class="icon">+</div>
                    </button>
                    <div class="faq-answer">
                        <p>
                            Strong engineering practices, automated checks, and clear acceptance criteria.
                        </p>
                    </div>
                </div>


            </div>
        </div>
    </div>
</section>


<!-- BLOG SECTION -->
<section class="blog-section" data-aos="fade-up" data-aos-easing="ease-out-cubic">
    <div class="container section-padding">

        <!-- Section Title -->
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold small gradient">
                <i class="bi bi-square-fill me-1" style="font-size: 10px;"></i>
                FROM OUR BLOG
            </span>
            <h2 class="fw-bold display-6 text-dark mt-2">
                Latest News And Insights
            </h2>
        </div>

        <!-- Blog Cards Row -->
        <div class="row g-4">

            <!-- Card 1 -->
            <div class="col-md-4">
                <div class="card border-0 blog-card h-100">
                    <img src="{{ asset('assets/image/blog-2.jpg') }}" class="card-img-top rounded-4" alt="Blog Image">
                    <div class="card-body px-0">

                        <div class="d-flex gap-3 small text-muted mb-2">
                            <span><i class="bi bi-calendar-week me-1"></i> OCTOBER 16, 2024</span>
                            <span><i class="bi bi-person-circle me-1"></i> BY ADMIN</span>
                        </div>

                        <h5 class="fw-semibold card-title">
                            The Art of Self-Care: Lessons from Health Coaching
                        </h5>

                        <a href="#" class="read-more mt-3 d-inline-flex align-items-center fw-semibold">
                            READ MORE <i class="bi bi-arrow-up-right ms-1"></i>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-4">
                <div class="card border-0 blog-card h-100">
                    <img src="{{ asset('assets/image/blog-5.jpg') }}" class="card-img-top rounded-4" alt="Blog Image">
                    <div class="card-body px-0">

                        <div class="d-flex gap-3 small text-muted mb-2">
                            <span><i class="bi bi-calendar-week me-1"></i> OCTOBER 16, 2024</span>
                            <span><i class="bi bi-person-circle me-1"></i> BY ADMIN</span>
                        </div>

                        <h5 class="fw-semibold card-title">
                            Tackling App Modernisation Challenges in Australia
                        </h5>

                        <a href="#" class="read-more mt-3 d-inline-flex align-items-center fw-semibold">
                            READ MORE <i class="bi bi-arrow-up-right ms-1"></i>
                        </a>

                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-4">
                <div class="card border-0 blog-card h-100">
                    <img src="{{ asset('assets/image/blog-6.jpg') }}" class="card-img-top rounded-4" alt="Blog Image">
                    <div class="card-body px-0">

                        <div class="d-flex gap-3 small text-muted mb-2">
                            <span><i class="bi bi-calendar-week me-1"></i> OCTOBER 16, 2024</span>
                            <span><i class="bi bi-person-circle me-1"></i> BY ADMIN</span>
                        </div>

                        <h5 class="fw-semibold card-title">
                            How to develop an analytics strategy
                        </h5>

                        <a href="#" class="read-more mt-3 d-inline-flex align-items-center fw-semibold">
                            READ MORE <i class="bi bi-arrow-up-right ms-1"></i>
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
