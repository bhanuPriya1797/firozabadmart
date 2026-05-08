<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourPackage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class TourPackageSeeder extends Seeder
{
    public function run(): void
    {
        $url = 'https://yourtravelgenie.in/tours/';
        $html = null;
        try {
            $resp = Http::get($url);
            if ($resp->successful()) {
                $html = $resp->body();
            }
        } catch (\Throwable $e) {
        }
        $names = [];
        if ($html) {
            preg_match_all('/<a[^>]+href="https?:\\/\\/yourtravelgenie\\.in\\/tours\\/[^"]+"[^>]*>(.*?)<\\/a>/i', $html, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $raw) {
                    $name = trim(strip_tags($raw));
                    if (mb_strlen($name) >= 3) {
                        $names[] = $name;
                    }
                }
            }
            $names = array_values(array_unique($names));
        }
        if (empty($names)) {
            $names = [
                'Kashmir Delight',
                'Ladakh Adventure',
                'Goa Beach Retreat',
                'Rajasthan Heritage Trail',
                'Kerala Backwaters Escape',
            ];
        }
        $limit = 20;
        $created = 0;
        foreach ($names as $name) {
            $slugBase = Str::slug($name);
            $slug = $slugBase;
            $i = 1;
            while (TourPackage::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $i;
                $i++;
            }
            $start = now()->addDays(random_int(7, 120))->startOfDay();
            $end = $start->copy()->addDays(random_int(3, 10));
            $maxSeats = random_int(10, 40);
            $price = random_int(10000, 80000);
            TourPackage::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'type' => 'group',
                    'description' => null,
                    'start_date' => $start,
                    'end_date' => $end,
                    'max_seats' => $maxSeats,
                    'booked_seats' => 0,
                    'price' => $price,
                    'status' => true,
                    'currency_code' => 'INR',
                ]
            );
            $created++;
            if ($created >= $limit) {
                break;
            }
        }
    }
}
