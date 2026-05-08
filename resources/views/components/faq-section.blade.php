@props(['category' => null, 'title' => 'Frequently Asked Questions', 'limit' => null, 'showTitle' => true])

@php
use App\Helpers\FaqHelper;

if ($category) {
    $faqs = FaqHelper::getFaqsByCategory($category, $limit);
} else {
    $faqs = FaqHelper::getAllFaqs();
    if ($limit) {
        $faqs = $faqs->take($limit);
    }
}
@endphp

@if($faqs->count() > 0)
<div class="faq-area py-120 bg-light">
    <div class="container">
        @if($showTitle)
        <div class="row">
            <div class="col-lg-12">
                <div class="site-heading text-center">
                    <h2 class="site-title">{{ $title }}</h2>
                    <p class="site-desc">Find answers to common questions about our organization and services</p>
                </div>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="faq-wrapper">
                    <div class="accordion" id="faqAccordion{{ $category ? '-' . $category : '' }}">
                        @foreach($faqs as $index => $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq-heading-{{ $faq->id }}">
                                <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#faq-collapse-{{ $faq->id }}" 
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                        aria-controls="faq-collapse-{{ $faq->id }}">
                                    {{ $faq->question }}
                                </button>
                            </h2>
                            <div id="faq-collapse-{{ $faq->id }}" 
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                 aria-labelledby="faq-heading-{{ $faq->id }}" 
                                 data-bs-parent="#faqAccordion{{ $category ? '-' . $category : '' }}">
                                <div class="accordion-body">
                                    {!! $faq->answer !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* FAQ Section Styles */
.faq-area {
    background: #f8f9fa;
    width: 100%;
    max-width: 100%;
}

.faq-area .container {
    max-width: 100%;
    padding: 0 30px;
}

.site-heading {
    margin-bottom: 60px;
}

.site-title {
    color: #333;
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 20px;
}

.site-desc {
    color: #666;
    font-size: 18px;
    line-height: 1.6;
    margin: 0;
}

.faq-wrapper {
    background: #fff;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.accordion-item {
    border: none;
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
}

.accordion-button {
    background: #fff;
    border: none;
    padding: 25px 30px;
    font-weight: 600;
    color: #333;
    box-shadow: none;
    transition: all 0.3s ease;
    font-size: 18px;
    text-align: left;
    width: 100%;
}

.accordion-button:not(.collapsed) {
    background: var(--theme-color);
    color: #fff;
}

.accordion-button:focus {
    box-shadow: none;
    border: none;
}

.accordion-button:hover {
    background: var(--theme-color);
    color: #fff;
}

.accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23333'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.accordion-button:not(.collapsed)::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.accordion-body {
    background: #fff;
    padding: 25px 30px;
    color: #666;
    line-height: 1.6;
    font-size: 16px;
}

.accordion-body p {
    margin-bottom: 10px;
}

.accordion-body p:last-child {
    margin-bottom: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .site-title {
        font-size: 32px;
    }
    
    .faq-wrapper {
        padding: 30px 20px;
    }
    
    .faq-area .container {
        padding: 0 15px;
    }
}

@media (max-width: 576px) {
    .site-title {
        font-size: 28px;
    }
    
    .accordion-button {
        padding: 20px 25px;
        font-size: 16px;
    }
    
    .accordion-body {
        padding: 20px 25px;
    }
}
</style>
@endif
