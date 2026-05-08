<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\BannerImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create banner
        $banner = Banner::create([
            'title' => 'Homepage Slider',
            'slug' => 'homepage-slider',
            'type' => 1, // Image type
            'status' => 'active',
            'sort_order' => 1
        ]);
        
        // Copy images from electrow theme to storage
        $sourceDir = public_path('assets/img/slider');
        $destDir = public_path('storage/banners');
        $thumbDir = public_path('storage/banners/thumb');
        
        // Create directories if they don't exist
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }
        
        if (!File::exists($thumbDir)) {
            File::makeDirectory($thumbDir, 0755, true);
        }
        
        // Slider images
        $sliderImages = [
            [
                'file' => 'slider-1.jpg',
                'title' => 'Empowering Education',
                'sub_title' => 'Supporting students to achieve their dreams',
                'link_text_1' => 'Learn More',
                'link_1' => '/about',
                'link_text_2' => 'Donate Now',
                'link_2' => '/donate',
                'sort_order' => 1
            ],
            [
                'file' => 'slider-2.jpg',
                'title' => 'Building Futures',
                'sub_title' => 'Creating opportunities through education',
                'link_text_1' => 'Our Mission',
                'link_1' => '/about',
                'link_text_2' => 'Get Involved',
                'link_2' => '/volunteer',
                'sort_order' => 2
            ],
            [
                'file' => 'slider-3.jpg',
                'title' => 'Join Our Cause',
                'sub_title' => 'Help us make education accessible to all',
                'link_text_1' => 'Donate',
                'link_1' => '/donate',
                'link_text_2' => 'Volunteer',
                'link_2' => '/volunteer',
                'sort_order' => 3
            ]
        ];
        
        foreach ($sliderImages as $image) {
            // Copy image from theme to storage
            if (File::exists($sourceDir . '/' . $image['file'])) {
                File::copy(
                    $sourceDir . '/' . $image['file'],
                    $destDir . '/' . $image['file']
                );
                
                // Create thumbnail
                File::copy(
                    $sourceDir . '/' . $image['file'],
                    $thumbDir . '/' . $image['file']
                );
                
                // Create banner image record
                BannerImage::create([
                    'banner_id' => $banner->id,
                    'image_name' => $image['file'],
                    'title' => $image['title'],
                    'sub_title' => $image['sub_title'],
                    'link_text_1' => $image['link_text_1'],
                    'link_1' => $image['link_1'],
                    'link_text_2' => $image['link_text_2'],
                    'link_2' => $image['link_2'],
                    'sort_order' => $image['sort_order']
                ]);
            }
        }
    }
}