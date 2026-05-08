<?php

return [

    'app_name' => env('APP_NAME'),

    'ADMIN_ROUTE_NAME' => 'administrator',

    'admin_email' => env('ADMIN_EMAIL'),
    'order_prefix' => 'CNXT',

    'img_extension' => ['jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'],
    
    'compare_scope' => [
        '=' => '=',
        '>' => '>',
        '<' => '<',
        '>=' => '>=',
        '<=' => '<='
    ],

    'currency_arr' => [
        '1' => 'USD',
        '2' => 'EUR',
        '3' => 'INR',
        '4' => 'AUD',
        '5' => 'GBP'
    ],

    'currency_symbol_arr' => [
        '1' => "&#36;",
        '2' => "&#128;",
        '3' => "&#x20B9;",
        '4' => "A&#36;",
        '5' => "&#163;"
    ],

    'device_types_arr' => [
        'desktop' => "Desktop",
        'mobile' => "Mobile"
    ],

    'input_types_arr' => [
        'text' => 'Text',
        'textarea' => 'Textarea',
        'checkbox' => 'Checkbox',
        'radio' => 'Radio',
        'file' => 'File',
        'email' => 'Email',
    ],

    'setting_types_arr' => [
        'website' => 'Website',
        'seo' => 'SEO',
        'social_links' => 'Social Links',
    ],

    'blog_type_arr' => [
        'blogs' => 'Blogs',
        'news' => 'News',
    ],

    'months_arr' => [
        '1' => 'January',
        '2' => 'February',
        '3' => 'March',
        '4' => 'April',
        '5' => 'May',
        '6' => 'June',
        '7' => 'July',
        '8' => 'August',
        '9' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ],

    'days_arr' => [
        'sunday' => 'Sunday',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
    ],

    'activity_level_arr' => [
        '1' => 'Light',
        '2' => 'Moderate',
        '3' => 'Strenuous',
        '4' => 'Extreme/Difficult',
    ],

    'service_level_arr' => [
        '1' => 'Standard',
        '2' => 'Deluxe',
        '3' => 'Premium',
        '4' => 'Superior',
    ],

    'fixed_service_level_arr' => [
        '1','2','3','4'
    ],

    'menu_link_type_arr' => [
        'internal' => 'Internal',
        'external' => 'External',
        'service' => 'Service',
        'cms' => 'CMS Page',
        'blog' => 'Blog Post',
        'news' => 'News Article',
        'event' => 'Event',
        'category' => 'Category',
        'custom' => 'Custom URL',
    ],

    'education_stages' => [
        'School : pre-matriculation',
        'School : Matric',
        'Higher Secondary (11th Class)',
        'Higher Secondary (12th Class)',
        "Bachelor's Degree (3 Year's)",
        "Bachelor's Degree (4 Year's)",
        "Bachelor's Degree (5 Year's)",
        "Master's Degree > Research Degree (M.phil, Phd.)",
        "Vocational Education",
        "Professional Certifications",
        "Training",
        "Internships",
        "Other",
    ],
    
];
