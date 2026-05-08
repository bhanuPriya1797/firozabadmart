<?php
use App\Models\Setting;

$keys = ['frontend_logo', 'footer_logo', 'middle_logo'];
$settings = Setting::whereIn('key', $keys)->get();

foreach ($keys as $key) {
    $s = $settings->firstWhere('key', $key);
    if ($s) {
        echo "Found $key: " . $s->type . "\n";
    } else {
        echo "Missing $key\n";
        if ($key === 'middle_logo') {
            Setting::create([
                'key' => 'middle_logo',
                'label' => 'Header Middle Logo',
                'group_name' => 'General',
                'type' => 'file',
                'is_fixed' => 0
            ]);
            echo "Created middle_logo\n";
        }
    }
}
