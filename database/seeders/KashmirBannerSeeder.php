<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;
use App\Models\BannerImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class KashmirBannerSeeder extends Seeder
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
            'video_type' => 0, // No video
            'status' => 1, // Active
            'sort_order' => 1
        ]);
        
        // Copy images from Kashmir directory to storage
        $sourceDir = public_path('assets/img/kashmir');
        $destDir = public_path('storage/banners');
        $thumbDir = public_path('storage/banners/thumb');
        
        // Create directories if they don't exist
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }
        
        if (!File::exists($thumbDir)) {
            File::makeDirectory($thumbDir, 0755, true);
        }
        
        // Kashmir-specific slider images with educational focus
        $sliderImages = [
            [
                'file' => 'kashmir-education-1.jpg',
                'title' => 'Education breaks the poverty cycle',
                'sub_title' => 'Let\'s break the link between family income and educational achievement',
                'link_text_1' => 'Start Donation',
                'link_1' => '/donate',
                'link_text_2' => 'Learn More',
                'link_2' => '/about',
                'sort_order' => 1
            ],
            [
                'file' => 'kashmir-education-2.jpg',
                'title' => 'Empowering Kashmir\'s Future',
                'sub_title' => 'Supporting meritorious students to achieve their educational dreams',
                'link_text_1' => 'Our Mission',
                'link_1' => '/about/mission',
                'link_text_2' => 'Get Involved',
                'link_2' => '/volunteer',
                'sort_order' => 2
            ],
            [
                'file' => 'kashmir-education-3.jpg',
                'title' => 'Building Brighter Tomorrows',
                'sub_title' => 'Join us in making education accessible to every deserving student in Kashmir',
                'link_text_1' => 'Donate Now',
                'link_1' => '/donate',
                'link_text_2' => 'Become a Volunteer',
                'link_2' => '/volunteer',
                'sort_order' => 3
            ]
        ];
        
        foreach ($sliderImages as $image) {
            // Copy image from Kashmir directory to storage
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


