<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class HomepageSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Join section defaults based on screenshot
            'home_join_title' => 'CDAA Archery Club',
            'home_join_subtitle' => '',
            'home_join_description' => 'We have a variety of membership options at Cuttack District Archery Association. Learn about our options, fees and key requests.',
            'home_join_link' => '/register',
            'home_join_button_text' => 'Become a Member',
            // Become Member CTA (new)
            'home_member_title' => 'CDAA Archery Club',
            'home_member_description' => '',
            'home_member_button_text' => 'Become a Member',
            'home_member_button_link' => '/register',
            'home_member_image' => '', // media manager path, stored as text
            // About section stub values (optional)
            'home_about_title' => 'About Us',
            'home_about_brief' => '',
            'home_about_readmore_link' => '/about',
            // Partners & Sponsors section (new)
            'home_partners_title' => 'Our Partner & Sponsors',
            'home_partners_description' => '',
            // Marquee default items (optional)
            'home_marquee_items' => json_encode([
                'India finishes No.1 at the 24th Asian Archery Championships 2025 with 10 medals'
            ]),
        ];

        foreach ($defaults as $key => $value) {
            $type = 'text';
            if (in_array($key, ['home_join_description','home_about_brief','home_member_description','home_partners_description','home_marquee_items'])) {
                $type = 'textarea';
            }
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'label' => ucwords(str_replace('_', ' ', $key)),
                    'type' => $type,
                    'group_name' => 'Homepage',
                    'class' => null,
                    'validation' => null,
                    'file_constraints' => null,
                    'options' => null,
                    'is_fixed' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'value' => $value,
                ]
            );
        }
    }
}
