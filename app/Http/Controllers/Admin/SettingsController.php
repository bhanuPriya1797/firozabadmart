<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Models\TourPackage;
use App\Models\TourPackageImage;
use App\Models\Destination;
use App\Models\DestinationImage;
use App\Models\Blog;
use App\Models\Cms;
use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        try {
            $fixedSettings = Setting::where('is_fixed', true)->get();
            $customSettings = Setting::where('is_fixed', false)->get();

            $allSettings = $fixedSettings->merge($customSettings)->groupBy('group_name');
            return view('admin.settings.index', compact('allSettings'));
        } catch (\Exception $e) {
            Log::error('Settings index error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load settings. Please try again later.');
        }
    }

    public function regenerateImages(Request $request)
    {
        $module = $request->input('module'); // null = all, otherwise one of: blogs, events, destinations, tour-packages, cms
        $stats = [
            'tour_packages' => 0,
            'tour_package_images' => 0,
            'destinations' => 0,
            'destination_images' => 0,
            'blogs' => 0,
            'cms_banners' => 0,
            'cms_page_images' => 0,
        ];
        $errors = [];

        $mk = function ($relPath) {
            $dir = public_path('storage/' . trim($relPath, '/'));
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
        };
        $resize = function ($srcAbs, $dstRel, $w, $h) use ($mk, &$errors) {
            try {
                $dstRel = trim($dstRel, '/');
                $dstAbs = public_path('storage/' . $dstRel);
                $mk(dirname($dstRel));
                Image::read($srcAbs)->resize($w, $h, function ($c) { $c->aspectRatio(); })->save($dstAbs);
                return true;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
                return false;
            }
        };
        $gen = function ($base, $file, $tw, $th, $mw, $mh) use ($resize) {
            if (empty($file)) return false;
            $base = rtrim($base, '/') . '/';
            $srcAbs = public_path('storage/' . $base . $file);
            if (!file_exists($srcAbs)) return false;
            $thumbRel = $base . 'thumb/' . $file;
            $mediumRel = $base . 'medium/' . $file;
            $ok1 = $resize($srcAbs, $thumbRel, $tw, $th);
            $ok2 = $resize($srcAbs, $mediumRel, $mw, $mh);
            return $ok1 && $ok2;
        };
        $genFromPath = function ($storedPath, $fallbackBase, $tw, $th, $mw, $mh) use ($gen) {
            if (empty($storedPath)) return false;
            if (strpos($storedPath, '/') !== false) {
                $file = basename($storedPath);
                $base = trim(dirname($storedPath), '/') . '/';
                return $gen($base, $file, $tw, $th, $mw, $mh);
            }
            return $gen($fallbackBase, $storedPath, $tw, $th, $mw, $mh);
        };

        // Tour Packages
        if (!$module || $module === 'tour-packages') {
            $tw = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_THUMB_WIDTH') ?: 336;
            $th = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_THUMB_HEIGHT') ?: 336;
            $mw = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_MEDIUM_WIDTH') ?: 450;
            $mh = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_MEDIUM_HEIGHT') ?: 450;
            foreach (TourPackage::select('id', 'main_banner')->get() as $tp) {
                if ($gen('tour-packages', $tp->main_banner, $tw, $th, $mw, $mh)) { $stats['tour_packages']++; }
            }
            foreach (TourPackageImage::select('file_name')->get() as $img) {
                if ($gen('tour-packages', $img->file_name, $tw, $th, $mw, $mh)) { $stats['tour_package_images']++; }
            }
        }

        // Destinations
        if (!$module || $module === 'destinations') {
            $tw = (int)CustomHelper::getSetting('DESTINATION_IMG_THUMB_WIDTH') ?: 336;
            $th = (int)CustomHelper::getSetting('DESTINATION_IMG_THUMB_HEIGHT') ?: 336;
            $mw = (int)CustomHelper::getSetting('DESTINATION_IMG_MEDIUM_WIDTH') ?: 450;
            $mh = (int)CustomHelper::getSetting('DESTINATION_IMG_MEDIUM_HEIGHT') ?: 450;
            foreach (Destination::select('image', 'feature_image', 'banner_image')->get() as $d) {
                foreach (['image', 'feature_image', 'banner_image'] as $f) {
                    if ($genFromPath($d->{$f}, 'destinations', $tw, $th, $mw, $mh)) { $stats['destinations']++; }
                }
            }
            foreach (DestinationImage::select('file_name')->get() as $di) {
                if ($gen('destinations', $di->file_name, $tw, $th, $mw, $mh)) { $stats['destination_images']++; }
            }
        }

        // Blogs + Events (same table)
        if (!$module || $module === 'blogs' || $module === 'events') {
            $tw = (int)CustomHelper::getSetting('BLOG_IMG_THUMB_WIDTH') ?: 400;
            $th = (int)CustomHelper::getSetting('BLOG_IMG_THUMB_HEIGHT') ?: 300;
            $mw = (int)CustomHelper::getSetting('BLOG_IMG_MEDIUM_WIDTH') ?: 450;
            $mh = (int)CustomHelper::getSetting('BLOG_IMG_MEDIUM_HEIGHT') ?: 450;
            $query = Blog::select('image');
            if ($module === 'blogs') { $query->where('content_type', 'blog'); }
            if ($module === 'events') { $query->where('content_type', 'event'); }
            foreach ($query->get() as $b) {
                // If old path uploads/blogs/... keep derivatives under the same base
                $baseFallback = 'blogs';
                if (!empty($b->image) && strpos($b->image, 'uploads/blogs/') === 0) {
                    $baseFallback = 'uploads/blogs';
                }
                if ($genFromPath($b->image, $baseFallback, $tw, $th, $mw, $mh)) { $stats['blogs']++; }
            }
        }

        // CMS (banner + page_image)
        if (!$module || $module === 'cms') {
            foreach (Cms::select('banner', 'page_image')->get() as $page) {
                if (!empty($page->banner)) {
                    // banner sizes
                    $tw = 400; $th = 300; $mw = 800; $mh = 600;
                    if ($genFromPath($page->banner, 'cms/banners', $tw, $th, $mw, $mh)) { $stats['cms_banners']++; }
                }
                if (!empty($page->page_image)) {
                    // page image sizes
                    $tw = 400; $th = 300; $mw = 800; $mh = 600;
                    if ($genFromPath($page->page_image, 'cms/page-images', $tw, $th, $mw, $mh)) { $stats['cms_page_images']++; }
                }
            }
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'errors' => $errors,
        ]);
    }

    public function update(Request $request)
    {
        try {
            $settings = Setting::all();
            $existingKeys = $settings->pluck('key')->all();
            $posted = collect($request->except(['_token', 'delete_file']));

            foreach ($posted as $key => $val) {
                if (!in_array($key, $existingKeys)) {
                    if (preg_match('/_(items|steps)_images$/', $key)) { continue; }
                    if (preg_match('/_(items|steps)_delete$/', $key)) { continue; }
                    if (preg_match('/_image_(top|bottom)_delete$/', $key)) { continue; }
                    $type = 'text';
                    if (preg_match('/(_items|_cards|_stats|_steps|_features|_bars)$/', $key)) {
                        $type = 'textarea';
                    }
                    $label = ucwords(str_replace('_', ' ', $key));
                    $group = str_starts_with($key, 'home_') ? 'Homepage' : null;
                    Setting::create([
                        'label' => $label,
                        'key' => $key,
                        'type' => $type,
                        'group_name' => $group,
                        'class' => null,
                        'validation' => null,
                        'file_constraints' => null,
                        'options' => null,
                        'is_fixed' => true,
                        'created_by' => Auth::id() ?? 1,
                        'updated_by' => Auth::id() ?? 1,
                        'value' => is_array($val) ? json_encode($val) : $val,
                    ]);
                }
            }
            $settings = Setting::all();
            $rules = [];
            $inputs = $request->except(['_token', 'delete_file']);
            $inputKeys = array_keys($inputs);
            $fileKeys = [];
            foreach ($settings as $s) {
                if ($request->hasFile($s->key)) {
                    $fileKeys[] = $s->key;
                }
            }

            // Step 1: Build dynamic validation rules
            foreach ($settings as $setting) {
                $key = $setting->key;
                if (!in_array($key, $inputKeys) && !in_array($key, $fileKeys)) {
                    continue;
                }
                $validation = json_decode($setting->validation, true);

                if ($validation) {
                    $ruleSet = [];

                    foreach ($validation as $rule => $value) {
                        if (is_bool($value) && $value === true) {
                            $ruleSet[] = $rule;
                        } elseif ($rule === 'regex') {
                            $regex = trim($value, '/');
                            $ruleSet[] = "regex:/{$regex}/";
                        } else {
                            $ruleSet[] = "{$rule}:{$value}";
                        }
                    }

                    // Allow blanks when field is not explicitly required
                    if (!empty($ruleSet)) {
                        $hasRequired = in_array('required', $ruleSet, true);
                        if (!$hasRequired) {
                            array_unshift($ruleSet, 'nullable');
                        }
                        $rules[$key] = $ruleSet;
                    }
                }
            }

            // Step 2: Validate input
            $validated = Validator::make($request->all(), $rules)->validate();

            // Handle homepage repeater image uploads
            $whatJson = $request->input('home_whatwedo_items');
            if ($whatJson !== null) {
                $whatArr = json_decode($whatJson, true) ?: [];
                $whatFiles = $request->file('home_whatwedo_items_images', []);
                foreach ($whatArr as $i => $item) {
                    if (isset($whatFiles[$i]) && $whatFiles[$i]->isValid()) {
                        $file = $whatFiles[$i];
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('uploads/settings', $filename, 'public');
                        $whatArr[$i]['image'] = $path;
                    }
                }
                $request->merge(['home_whatwedo_items' => json_encode($whatArr)]);
            }

            $processJson = $request->input('home_process_steps');
            if ($processJson !== null) {
                $processArr = json_decode($processJson, true) ?: [];
                $processFiles = $request->file('home_process_steps_images', []);
                foreach ($processArr as $i => $item) {
                    if (isset($processFiles[$i]) && $processFiles[$i]->isValid()) {
                        $file = $processFiles[$i];
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('uploads/settings', $filename, 'public');
                        $processArr[$i]['image'] = $path;
                    }
                }
                $request->merge(['home_process_steps' => json_encode($processArr)]);
            }

            if ($request->hasFile('home_about_image_top')) {
                $file = $request->file('home_about_image_top');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads/settings', $filename, 'public');
                $request->merge(['home_about_image_top' => $path]);
            }
            if ($request->hasFile('home_about_image_bottom')) {
                $file = $request->file('home_about_image_bottom');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads/settings', $filename, 'public');
                $request->merge(['home_about_image_bottom' => $path]);
            }

            if ($request->hasFile('home_join_image')) {
                $file = $request->file('home_join_image');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads/settings', $filename, 'public');
                $request->merge(['home_join_image' => $path]);
            }

            $aboutTopSetting = $settings->firstWhere('key', 'home_about_image_top');
            if ($request->input('home_about_image_top_delete') == '1' && $aboutTopSetting && $aboutTopSetting->value) {
                $old = $aboutTopSetting->value;
                /*
                // STOPPING physical deletion
                if (strpos($old, 'uploads/') === 0 && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                */
                $request->merge(['home_about_image_top' => null]);
            }
            $aboutBottomSetting = $settings->firstWhere('key', 'home_about_image_bottom');
            if ($request->input('home_about_image_bottom_delete') == '1' && $aboutBottomSetting && $aboutBottomSetting->value) {
                $old = $aboutBottomSetting->value;
                /*
                // STOPPING physical deletion
                if (strpos($old, 'uploads/') === 0 && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                */
                $request->merge(['home_about_image_bottom' => null]);
            }

            $joinImgSetting = $settings->firstWhere('key', 'home_join_image');
            if ($request->input('home_join_image_delete') == '1' && $joinImgSetting && $joinImgSetting->value) {
                $request->merge(['home_join_image' => null]);
            }

            // Optional: Become a Member CTA image delete flag
            $memberImgSetting = $settings->firstWhere('key', 'home_member_image');
            if ($request->input('home_member_image_delete') == '1' && $memberImgSetting && $memberImgSetting->value) {
                $request->merge(['home_member_image' => null]);
            }

            // Step 3: Update settings
            $deleteKeysArr = array_keys($request->input('delete_file', []));
            $updateKeys = array_unique(array_merge($inputKeys, $fileKeys, $deleteKeysArr));
            foreach ($settings as $setting) {
                $key = $setting->key;
                if (!in_array($key, $updateKeys)) {
                    continue;
                }
                $setting->old_value = $setting->value;

                if ($setting->type === 'file') {
                    $shouldDelete = $request->input("delete_file.$key") == '1';

                    if ($shouldDelete && $setting->value && Storage::disk('public')->exists($setting->value)) {
                        // Storage::disk('public')->delete($setting->value); // STOPPING physical deletion
                        $setting->value = null;
                    }

                    if ($request->hasFile($key)) {
                        /*
                        // STOPPING physical deletion
                        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                            Storage::disk('public')->delete($setting->value);
                        }
                        */

                        $file = $request->file($key);
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('uploads/settings', $filename, 'public');
                        $setting->value = $path;
                    }
                } elseif ($request->has($key)) {
                    $value = $request->input($key);
                    $setting->value = is_array($value) ? json_encode($value) : $value;
                    if ($key === 'home_whatwedo_items') {
                        $oldArr = json_decode($setting->old_value ?: '[]', true) ?: [];
                        $newArr = json_decode($setting->value ?: '[]', true) ?: [];
                        $delFlags = $request->input('home_whatwedo_items_delete', []);
                        $max = max(count($oldArr), count($newArr));
                        for ($i = 0; $i < $max; $i++) {
                            $oldImg = $oldArr[$i]['image'] ?? '';
                            $newImg = $newArr[$i]['image'] ?? '';
                            $del = isset($delFlags[$i]) && (string)$delFlags[$i] === '1';
                            if ($del) {
                                /*
                                // STOPPING physical deletion
                                if ($oldImg && strpos($oldImg, 'uploads/') === 0 && Storage::disk('public')->exists($oldImg)) {
                                    Storage::disk('public')->delete($oldImg);
                                }
                                */
                                if (isset($newArr[$i])) { $newArr[$i]['image'] = ''; }
                            } elseif (empty($newImg) && !empty($oldImg)) {
                                if (!isset($newArr[$i])) { $newArr[$i] = []; }
                                $newArr[$i]['image'] = $oldImg;
                            } elseif ($oldImg && $newImg && $oldImg !== $newImg) {
                                /*
                                // STOPPING physical deletion
                                if (strpos($oldImg, 'uploads/') === 0 && Storage::disk('public')->exists($oldImg)) {
                                    Storage::disk('public')->delete($oldImg);
                                }
                                */
                            }
                        }
                        $setting->value = json_encode($newArr);
                    } elseif ($key === 'home_process_steps') {
                        $oldArr = json_decode($setting->old_value ?: '[]', true) ?: [];
                        $newArr = json_decode($setting->value ?: '[]', true) ?: [];
                        $delFlags = $request->input('home_process_steps_delete', []);
                        $max = max(count($oldArr), count($newArr));
                        for ($i = 0; $i < $max; $i++) {
                            $oldImg = $oldArr[$i]['image'] ?? '';
                            $newImg = $newArr[$i]['image'] ?? '';
                            $del = isset($delFlags[$i]) && (string)$delFlags[$i] === '1';
                            if ($del) {
                                /*
                                // STOPPING physical deletion
                                if ($oldImg && strpos($oldImg, 'uploads/') === 0 && Storage::disk('public')->exists($oldImg)) {
                                    Storage::disk('public')->delete($oldImg);
                                }
                                */
                                if (isset($newArr[$i])) { $newArr[$i]['image'] = ''; }
                            } elseif (empty($newImg) && !empty($oldImg)) {
                                if (!isset($newArr[$i])) { $newArr[$i] = []; }
                                $newArr[$i]['image'] = $oldImg;
                            } elseif ($oldImg && $newImg && $oldImg !== $newImg) {
                                /*
                                // STOPPING physical deletion
                                if (strpos($oldImg, 'uploads/') === 0 && Storage::disk('public')->exists($oldImg)) {
                                    Storage::disk('public')->delete($oldImg);
                                }
                                */
                            }
                        }
                        $setting->value = json_encode($newArr);
                    }
                }

                $setting->save();

                //Delete Cache
                Cache::forget("website_setting_{$setting->key}");
                Cache::forget("website_settings_all");
            }

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'settings',
                0,
                'bulk_update',
                'Update Settings',
                json_encode($validated)
            );

            return back()->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Settings update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while updating settings.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'label' => 'required|string',
                'key' => 'required|string|unique:website_settings,key',
                'type' => 'required|in:text,textarea,file,select,checkbox,radio,email,phone',
                'options' => 'nullable|string'
            ]);

            Setting::create([
                'label' => $request->label,
                'key' => $request->key,
                'type' => $request->type,
                'group_name' => $request->group_name ?? null,
                'class' => $request->class ?? null,
                'validation' => $request->validation ?? null,
                'file_constraints' => $request->file_constraints ?? null,
                'options' => in_array($request->type, ['select', 'checkbox', 'radio']) ? explode(',', $request->options) : null,
                'is_fixed' => false,
                'created_by' => Auth::id() ?? 1,
                'updated_by' => Auth::id() ?? 1,
            ]);

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'settings',
                'new_field',
                'Create Setting Field',
                'Created new setting field: ' . $request->label,
                json_encode($request->all())
            );

            return redirect()->back()->with('success', 'Custom setting field added successfully!');
        } catch (\Exception $e) {
            Log::error('Settings store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Unable to add setting. Please try again.');
        }
    }
}
