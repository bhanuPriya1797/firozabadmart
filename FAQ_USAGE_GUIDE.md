# FAQ Component Usage Guide

## Overview

The FAQ component allows you to dynamically display FAQs on any page based on categories created in the admin panel.

## How to Use

### 1. Basic Usage

```blade
<!-- Show all FAQs -->
<x-faq-section />

<!-- Show FAQs from specific category -->
<x-faq-section category="contact-us" />

<!-- Show FAQs with custom title -->
<x-faq-section category="volunteer" title="Volunteer FAQs" />

<!-- Show limited number of FAQs -->
<x-faq-section category="general" title="General FAQs" :limit="5" />

<!-- Show FAQs without title section -->
<x-faq-section category="services" :showTitle="false" />
```

### 2. Available Props

-   `category` (string, optional): Category slug to filter FAQs
-   `title` (string, optional): Custom title for the FAQ section (default: "Frequently Asked Questions")
-   `limit` (integer, optional): Maximum number of FAQs to display
-   `showTitle` (boolean, optional): Whether to show the title section (default: true)

### 3. Pre-configured Categories

The following categories are automatically created with demo FAQs:

#### Contact Us (`contact-us`)

-   How can I contact IL Mission?
-   What are your office hours?
-   How quickly do you respond to inquiries?
-   Can I schedule a meeting with your team?

#### Volunteer (`volunteer`)

-   How can I become a volunteer?
-   What skills do I need to volunteer?
-   How much time do I need to commit?
-   Do you provide training for volunteers?
-   Can I volunteer remotely?

#### General (`general`)

-   What is IL Mission?
-   Where are you located?
-   How can I support your organization?
-   Are you a registered non-profit?

#### Services (`services`)

-   What educational services do you provide?
-   Who is eligible for your services?
-   How do I apply for a scholarship?
-   Do you offer online programs?

### 4. Adding to Templates

#### Contact Us Template

```blade
<!-- FAQ Section -->
<x-faq-section category="contact-us" title="Frequently Asked Questions" />
<!-- FAQ Section end -->
```

#### Volunteer Template

```blade
<!-- FAQ Section -->
<x-faq-section category="volunteer" title="Volunteer FAQs" />
<!-- FAQ Section end -->
```

#### Any Other Template

```blade
<!-- FAQ Section -->
<x-faq-section category="general" title="General Questions" />
<!-- FAQ Section end -->
```

### 5. Admin Panel Management

#### Adding New Categories

1. Go to Admin Panel → FAQ Management → FAQ Categories
2. Click "Add Category"
3. Fill in:
    - Category Name (e.g., "Student Portal")
    - Slug (auto-generated from name)
    - Description (optional)
    - Sort Order
    - Status (Active/Inactive)

#### Adding FAQs

1. Go to Admin Panel → FAQ Management → FAQs
2. Click "Add FAQ"
3. Fill in:
    - Question
    - Answer (supports rich text)
    - Category (select from dropdown)
    - Sort Order
    - Status (Active/Inactive)

### 6. Features

-   **Dynamic Loading**: FAQs are loaded based on category
-   **Rich Text Support**: Answers support HTML formatting
-   **Responsive Design**: Works on all device sizes
-   **Caching**: Performance optimized with caching
-   **Sorting**: FAQs are displayed in the order set in admin
-   **Status Control**: Only active FAQs are displayed

### 7. Styling

The component uses the same styling as the contact us page:

-   Light gray background (`#f8f9fa`)
-   White accordion cards with shadow
-   Theme color for active accordion buttons
-   Responsive design for mobile devices

### 8. Cache Management

FAQ data is automatically cached for performance. Cache is cleared when:

-   FAQ categories are updated
-   FAQs are added, edited, or deleted
-   Manual cache clearing is performed

### 9. Example Implementation

```blade
@extends('components.frontend.layout')

@section('content')
    <!-- Your page content here -->

    <!-- Dynamic FAQ Section -->
    @if($page->slug === 'contact')
        <x-faq-section category="contact-us" title="Contact FAQs" />
    @elseif($page->slug === 'volunteer')
        <x-faq-section category="volunteer" title="Volunteer FAQs" />
    @else
        <x-faq-section category="general" title="General FAQs" />
    @endif
@endsection
```

This component provides a flexible and maintainable way to display FAQs across your website while keeping the content manageable through the admin panel.
















