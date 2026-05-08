<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FaqCategory;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create FAQ Categories
        $categories = [
            [
                'name' => 'Contact Us',
                'slug' => 'contact-us',
                'description' => 'Frequently asked questions about contacting us',
                'status' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'Volunteer',
                'slug' => 'volunteer',
                'description' => 'Frequently asked questions about volunteering',
                'status' => 1,
                'sort_order' => 2
            ],
            [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'General frequently asked questions',
                'status' => 1,
                'sort_order' => 3
            ],
            [
                'name' => 'Services',
                'slug' => 'services',
                'description' => 'Frequently asked questions about our services',
                'status' => 1,
                'sort_order' => 4
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = FaqCategory::create($categoryData);
            
            // Add FAQs for each category
            $this->addFaqsForCategory($category);
        }
    }

    private function addFaqsForCategory($category)
    {
        $faqs = [];

        switch ($category->slug) {
            case 'contact-us':
                $faqs = [
                    [
                        'question' => 'How can I contact IL Mission?',
                        'answer' => 'You can contact us through our contact form on the website, by phone at +91 6005021882, or by email at info@ilmission.org. We also have an office in Jammu and Kashmir.',
                        'sort_order' => 1
                    ],
                    [
                        'question' => 'What are your office hours?',
                        'answer' => 'Our office is open Monday to Friday from 9:00 AM to 6:00 PM. We are closed on weekends and public holidays.',
                        'sort_order' => 2
                    ],
                    [
                        'question' => 'How quickly do you respond to inquiries?',
                        'answer' => 'We typically respond to all inquiries within 24-48 hours during business days. For urgent matters, please call us directly.',
                        'sort_order' => 3
                    ],
                    [
                        'question' => 'Can I schedule a meeting with your team?',
                        'answer' => 'Yes, you can schedule a meeting with our team. Please contact us through our contact form or call us to arrange a convenient time.',
                        'sort_order' => 4
                    ]
                ];
                break;

            case 'volunteer':
                $faqs = [
                    [
                        'question' => 'How can I become a volunteer?',
                        'answer' => 'To become a volunteer, you can fill out our volunteer application form on the website. We will review your application and contact you within a few days to discuss opportunities.',
                        'sort_order' => 1
                    ],
                    [
                        'question' => 'What skills do I need to volunteer?',
                        'answer' => 'We welcome volunteers with various skills and backgrounds. Whether you have teaching experience, administrative skills, or just a passion for helping others, there\'s a place for you in our organization.',
                        'sort_order' => 2
                    ],
                    [
                        'question' => 'How much time do I need to commit?',
                        'answer' => 'We offer flexible volunteering opportunities. You can commit as little as a few hours per week or as much as you\'re comfortable with. We work with your schedule.',
                        'sort_order' => 3
                    ],
                    [
                        'question' => 'Do you provide training for volunteers?',
                        'answer' => 'Yes, we provide comprehensive training for all our volunteers. This includes orientation about our mission, specific role training, and ongoing support.',
                        'sort_order' => 4
                    ],
                    [
                        'question' => 'Can I volunteer remotely?',
                        'answer' => 'Yes, we offer remote volunteering opportunities for certain roles. Please specify your preference in your application form.',
                        'sort_order' => 5
                    ]
                ];
                break;

            case 'general':
                $faqs = [
                    [
                        'question' => 'What is IL Mission?',
                        'answer' => 'IL Mission is a non-profit organization dedicated to providing educational opportunities and support to students in need. We focus on empowering communities through education and skill development.',
                        'sort_order' => 1
                    ],
                    [
                        'question' => 'Where are you located?',
                        'answer' => 'Our main office is located in Jammu and Kashmir, India. We serve communities across the region and also have remote programs.',
                        'sort_order' => 2
                    ],
                    [
                        'question' => 'How can I support your organization?',
                        'answer' => 'You can support us by volunteering, making a donation, spreading awareness about our work, or partnering with us on educational initiatives.',
                        'sort_order' => 3
                    ],
                    [
                        'question' => 'Are you a registered non-profit?',
                        'answer' => 'Yes, IL Mission is a registered non-profit organization. We operate with full transparency and accountability.',
                        'sort_order' => 4
                    ]
                ];
                break;

            case 'services':
                $faqs = [
                    [
                        'question' => 'What educational services do you provide?',
                        'answer' => 'We provide a range of educational services including scholarships, tutoring, career counseling, skill development programs, and educational resources.',
                        'sort_order' => 1
                    ],
                    [
                        'question' => 'Who is eligible for your services?',
                        'answer' => 'Our services are primarily aimed at students from underprivileged backgrounds, but we also serve communities in need. Eligibility varies by program.',
                        'sort_order' => 2
                    ],
                    [
                        'question' => 'How do I apply for a scholarship?',
                        'answer' => 'Scholarship applications are available on our website. The process includes filling out an application form and providing necessary documentation.',
                        'sort_order' => 3
                    ],
                    [
                        'question' => 'Do you offer online programs?',
                        'answer' => 'Yes, we offer various online programs and resources to make our services accessible to more students.',
                        'sort_order' => 4
                    ]
                ];
                break;
        }

        foreach ($faqs as $faqData) {
            $faqData['category_id'] = $category->id;
            $faqData['status'] = 1;
            Faq::create($faqData);
        }
    }
}
