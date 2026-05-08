<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            // Frontend listing per-page settings and pagination type
            [
                'label' => 'Tours Per Page',
                'key' => 'FRONT_TOUR_PACKAGES_PER_PAGE',
                'type' => 'number',
                'group_name' => 'Frontend',
                'value' => 9,
            ],
            [
                'label' => 'Destinations Per Page',
                'key' => 'FRONT_DESTINATIONS_PER_PAGE',
                'type' => 'number',
                'group_name' => 'Frontend',
                'value' => 12,
            ],
            [
                'label' => 'Blogs Per Page',
                'key' => 'FRONT_BLOGS_PER_PAGE',
                'type' => 'number',
                'group_name' => 'Frontend',
                'value' => 9,
            ],
            [
                'label' => 'Gallery Images Per Page',
                'key' => 'FRONT_GALLERY_PER_PAGE',
                'type' => 'number',
                'group_name' => 'Frontend',
                'value' => 24,
            ],
            [
                'label' => 'Services Per Page',
                'key' => 'FRONT_SERVICES_PER_PAGE',
                'type' => 'number',
                'group_name' => 'Frontend',
                'value' => 12,
            ],
            [
                'label' => 'News Per Page',
                'key' => 'FRONT_NEWS_PER_PAGE',
                'type' => 'number',
                'group_name' => 'Frontend',
                'value' => 9,
            ],
            [
                'label' => 'Appeals Per Page',
                'key' => 'FRONT_APPEALS_PER_PAGE',
                'type' => 'number',
                'group_name' => 'Frontend',
                'value' => 12,
            ],
            [
                'label' => 'Tour Packages Pagination Type',
                'key' => 'FRONT_TOUR_PACKAGES_PAGINATION_TYPE',
                'type' => 'select',
                'group_name' => 'Frontend',
                'options' => ['normal', 'ajax'],
                'value' => 'normal',
            ],
            // Image sizing defaults: Tour Packages
            [
                'label' => 'Tour Package Image Width',
                'key' => 'TOUR_PACKAGE_IMG_WIDTH',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 1920,
            ],
            [
                'label' => 'Tour Package Image Height',
                'key' => 'TOUR_PACKAGE_IMG_HEIGHT',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 1024,
            ],
            [
                'label' => 'Tour Package Thumb Width',
                'key' => 'TOUR_PACKAGE_IMG_THUMB_WIDTH',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 336,
            ],
            [
                'label' => 'Tour Package Thumb Height',
                'key' => 'TOUR_PACKAGE_IMG_THUMB_HEIGHT',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 336,
            ],
            [
                'label' => 'Tour Package Medium Width',
                'key' => 'TOUR_PACKAGE_IMG_MEDIUM_WIDTH',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 450,
            ],
            [
                'label' => 'Tour Package Medium Height',
                'key' => 'TOUR_PACKAGE_IMG_MEDIUM_HEIGHT',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 450,
            ],
            // Image sizing defaults: Destinations
            [
                'label' => 'Destination Image Width',
                'key' => 'DESTINATION_IMG_WIDTH',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 1920,
            ],
            [
                'label' => 'Destination Image Height',
                'key' => 'DESTINATION_IMG_HEIGHT',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 1024,
            ],
            [
                'label' => 'Destination Thumb Width',
                'key' => 'DESTINATION_IMG_THUMB_WIDTH',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 336,
            ],
            [
                'label' => 'Destination Thumb Height',
                'key' => 'DESTINATION_IMG_THUMB_HEIGHT',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 336,
            ],
            [
                'label' => 'Destination Medium Width',
                'key' => 'DESTINATION_IMG_MEDIUM_WIDTH',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 450,
            ],
            [
                'label' => 'Destination Medium Height',
                'key' => 'DESTINATION_IMG_MEDIUM_HEIGHT',
                'type' => 'number',
                'group_name' => 'Images',
                'value' => 450,
            ],
            [
                'label' => 'Website Name',
                'key' => 'website_name',
                'type' => 'text',
                'class' => 'form-control',
                'validation' => ['required' => true, 'max' => 100],
            ],
            [
                'label' => 'Short Title',
                'key' => 'short_title',
                'type' => 'text',
                'class' => 'form-control',
                'validation' => ['max' => 60],
            ],
            [
                'label' => 'Header Logo',
                'key' => 'logo_header',
                'type' => 'file',
                'file_constraints' => [
                    'max_size' => 2048,
                    'types' => ['jpg', 'jpeg', 'png', 'webp'],
                    'width' => 300,
                    'height' => 100
                ],
            ],
            [
                'label' => 'Footer Logo',
                'key' => 'logo_footer',
                'type' => 'file',
                'file_constraints' => [
                    'max_size' => 2048,
                    'types' => ['jpg', 'jpeg', 'png', 'webp'],
                    'width' => 300,
                    'height' => 100
                ],
            ],
            [
                'label' => 'Favicon',
                'key' => 'favicon',
                'type' => 'file',
                'file_constraints' => [
                    'max_size' => 1024,
                    'types' => ['ico', 'png'],
                    'width' => 64,
                    'height' => 64
                ],
            ],
            [
                'label' => 'Meta Title',
                'key' => 'meta_title',
                'type' => 'text',
                'validation' => ['max' => 100]
            ],
            [
                'label' => 'Meta Description',
                'key' => 'meta_description',
                'type' => 'textarea',
                'validation' => ['max' => 255]
            ],
            [
                'label' => 'Meta Keywords',
                'key' => 'meta_keywords',
                'type' => 'textarea',
                'validation' => ['max' => 255]
            ],
            [
                'label' => 'Contact Email',
                'key' => 'contact_email',
                'type' => 'email',
                'validation' => ['required' => true, 'email' => true]
            ],
            [
                'label' => 'Contact Phone',
                'key' => 'contact_phone',
                'type' => 'text',
                'validation' => ['max' => 15]
            ],
            [
                'label' => 'Site Address',
                'key' => 'site_address',
                'type' => 'textarea',
                'validation' => ['max' => 300]
            ],
            [
                'label' => 'Facebook URL',
                'key' => 'facebook',
                'type' => 'text',
                'validation' => ['url' => true]
            ],
            [
                'label' => 'Twitter URL',
                'key' => 'twitter',
                'type' => 'text',
                'validation' => ['url' => true]
            ],
            [
                'label' => 'Instagram URL',
                'key' => 'instagram',
                'type' => 'text',
                'validation' => ['url' => true]
            ],
            [
                'label' => 'LinkedIn URL',
                'key' => 'linkedin',
                'type' => 'text',
                'validation' => ['url' => true]
            ],
            [
                'label' => 'YouTube URL',
                'key' => 'youtube',
                'type' => 'text',
                'validation' => ['url' => true]
            ],
            [
                'label' => 'TikTok URL',
                'key' => 'tiktok',
                'type' => 'text',
                'validation' => ['url' => true]
            ],
            [
                'label' => 'Other Social URL',
                'key' => 'other_social',
                'type' => 'text',
                'validation' => ['url' => true]
            ],
            [
                'label' => 'Home Banner Size (W x H)',
                'key' => 'home_banner_size',
                'type' => 'text',
                'validation' => ['regex' => '^[0-9]+x[0-9]+$']
            ],
            [
                'label' => 'Inner Banner Size (W x H)',
                'key' => 'inner_banner_size',
                'type' => 'text',
                'validation' => ['regex' => '^[0-9]+x[0-9]+$']
            ],
            // Homepage dynamic content
            [
                'label' => 'Industries Section Title',
                'key' => 'home_industries_section_title',
                'type' => 'text',
            ],
            [
                'label' => 'Industries Items (Title, Description, Icon) JSON',
                'key' => 'home_industries_items',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home Industries (JSON)',
                'key' => 'home_industries',
                'type' => 'textarea',
            ],
            [
                'label' => 'What We Do Section Title',
                'key' => 'home_whatwedo_section_title',
                'type' => 'text',
            ],
            [
                'label' => 'What We Do Items (Image, Box Title, Short Description) JSON',
                'key' => 'home_whatwedo_items',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home About Title',
                'key' => 'home_about_title',
                'type' => 'text',
            ],
            [
                'label' => 'Home About Brief (HTML)',
                'key' => 'home_about_brief',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home About Top Image (storage/uploads or assets path)',
                'key' => 'home_about_image_top',
                'type' => 'text',
            ],
            [
                'label' => 'Home About Bottom Image (storage/uploads or assets path)',
                'key' => 'home_about_image_bottom',
                'type' => 'text',
            ],
            [
                'label' => 'Home Experience Cards (JSON)',
                'key' => 'home_experience_cards',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home Why CalcuNext Heading',
                'key' => 'home_why_heading',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home Why CalcuNext Stats (JSON)',
                'key' => 'home_why_stats',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home Working Process Steps (JSON)',
                'key' => 'home_process_steps',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home Technology Index Bars (JSON)',
                'key' => 'home_tech_index_bars',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home Technology Features (JSON)',
                'key' => 'home_tech_features',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home Trust & Quality Cards (JSON)',
                'key' => 'home_trust_quality_cards',
                'type' => 'textarea',
            ],
            [
                'label' => 'Home FAQ Category Slug',
                'key' => 'home_faq_category',
                'type' => 'text',
            ],
            [
                'label' => 'Home FAQ Limit',
                'key' => 'home_faq_limit',
                'type' => 'text',
            ],
            [
                'label' => 'Home Featured Blogs Count',
                'key' => 'home_blog_featured_count',
                'type' => 'text',
            ],
            [
                'label' => 'Homepage Menu Slug',
                'key' => 'home_menu_slug',
                'type' => 'text',
            ],
            [
                'label' => 'Homepage Slider Autoplay',
                'key' => 'home_slider_autoplay',
                'type' => 'checkbox',
            ],
            [
                'label' => 'Homepage Slider Speed (ms)',
                'key' => 'home_slider_speed',
                'type' => 'number',
            ],
        ];

        foreach ($defaultSettings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'label'            => $setting['label'],
                    'type'             => $setting['type'],
                    'group_name'       => (isset($setting['key']) && str_starts_with($setting['key'], 'home_')) ? 'Homepage' : ($setting['group_name'] ?? null),
                    'class'            => $setting['class'] ?? 'form-control',
                    'validation'       => isset($setting['validation']) ? json_encode($setting['validation']) : null,
                    'file_constraints' => isset($setting['file_constraints']) ? json_encode($setting['file_constraints']) : null,
                    'options'          => isset($setting['options']) ? json_encode($setting['options']) : null,
                    'value'            => isset($setting['value']) ? (string)$setting['value'] : null,
                    'is_fixed'         => true,
                    'created_by'       => 1,
                    'updated_by'       => 1,
                ]
            );
        }
    }
}
