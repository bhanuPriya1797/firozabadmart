<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms;
use App\Models\Media; // Added for Media Manager integration
use App\Models\CustomField;
use App\Helpers\CustomFieldHelper;
use App\Helpers\CustomHelper;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class CmsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Cms::select('id', 'title', 'slug', 'template', 'status', 'featured', 'created_at')->orderByDesc('id');

                return DataTables::of($query)
                    ->addColumn('status_badge', function ($row) {
                        $badgeClass = $row->status ? 'bg-label-success' : 'bg-label-danger';
                        $statusText = $row->status ? 'Active' : 'Inactive';
                        return '<span class="badge ' . $badgeClass . ' me-1">' . $statusText . '</span>';
                    })
                    ->addColumn('featured_badge', function ($row) {
                        $badgeClass = $row->featured ? 'bg-label-warning' : 'bg-label-secondary';
                        $featuredText = $row->featured ? 'Featured' : 'Normal';
                        return '<span class="badge ' . $badgeClass . ' me-1">' . $featuredText . '</span>';
                    })
                    ->addColumn('template_badge', function ($row) {
                        return '<span class="badge bg-label-info me-1">' . ucfirst($row->template) . '</span>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '';
                    })
                ->addColumn('action', function ($row) {
                        $routeName = CustomHelper::getAdminRouteName();
                        $editUrl = route($routeName . '.cms.edit', ['id' => $row->id]);
                        $deleteUrl = route($routeName . '.cms.destroy', ['id' => $row->id]);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('cms.edit')) || 
                            (auth()->user()->can('cms.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // Edit permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('cms.edit'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect" 
                                    href="' . $editUrl . '"
                                >
                                        <i class="icon-base ti tabler-edit me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('cms.delete'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-cms"
                                    href="javascript:void(0);"
                                    data-url="' . $deleteUrl . '"
                                >
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }

                            $actions .= '</div></div>';
                        }

                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                })
                    ->rawColumns(['action', 'status_badge', 'featured_badge', 'template_badge'])
                ->make(true);
            } catch (\Exception $e) {
                \Log::error('CmsController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        $data['page_title'] = 'CMS Pages';
        return view('admin.cms.index', $data);
    }

    public function create()
    {
        $data['page_title'] = 'Create CMS Page';
        $data['templates'] = $this->getAvailableTemplates();
        
        // Get custom fields for new pages (ref_id = null)
        $data['customFieldsGrouped'] = CustomFieldHelper::getCustomFieldsGrouped('cms', null) ?? collect();
        $data['customFieldValues'] = [];
        
        return view('admin.cms.form', $data);
    }

    public function store(Request $request)
    {
        try {
            \Log::info('CMS store method called with data:', $request->all());
            
            // Generate slug from title if not provided
            $slug = $request->input('slug');
            if (empty($slug)) {
                $slug = $this->generateSlug($request->input('title'));
            } else {
                $slug = $this->generateSlug($slug);
            }

            // Define basic CMS validation rules
            $basicRules = [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
                'template' => 'nullable|string|max:255',
                'brief' => 'nullable|string|max:500',
                'heading' => 'nullable|string|max:500',
                'description' => 'nullable|string',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'page_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'boolean',
                'featured' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ];

            // Get custom field validation rules
            // For new pages, pass null to get global fields (ref_id = null)
            $customFieldValidation = CustomFieldHelper::generateValidationRules('cms', null);
            
            \Log::info('Custom field validation rules for new page:', $customFieldValidation);
            \Log::info('Custom fields being validated:', array_keys($customFieldValidation['rules']));
            
            // Merge custom field validation with basic validation
            $allRules = array_merge($basicRules, $customFieldValidation['rules']);
            $allMessages = $customFieldValidation['messages'];
            
            \Log::info('All validation rules:', $allRules);
            
            // Test with basic validation only first
            $testValidator = \Validator::make($request->all(), $basicRules);
            if ($testValidator->fails()) {
                \Log::error('Basic validation failed:', $testValidator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($testValidator->errors())
                    ->withInput();
            }

            // Validate all fields including custom fields
            $validator = \Validator::make($request->all(), $allRules, $allMessages);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator->errors())
                    ->withInput();
            }

            // Get validated data
            $validated = $validator->validated();
            
            // Add the generated slug
            $validated['slug'] = $slug;
            
            // Set default template if none is selected
            if (empty($validated['template'])) {
                $validated['template'] = 'default';
            }

            // Handle SEO data
            $seoData = [];
            if ($request->filled('meta_title')) {
                $seoData['meta_title'] = $request->input('meta_title');
            }
            if ($request->filled('meta_keywords')) {
                $seoData['meta_keywords'] = $request->input('meta_keywords');
            }
            if ($request->filled('meta_description')) {
                $seoData['meta_description'] = $request->input('meta_description');
            }
            
            if (!empty($seoData)) {
                $validated['seo'] = $seoData;
            }

            // Handle file uploads
            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $path = 'cms/banners/';
                $thumbPath = 'cms/banners/thumb/';
                $mediumPath = 'cms/banners/medium/';
                $width = 1600; $height = 600;
                $thumbWidth = 400; $thumbHeight = 300;
                $mediumWidth = 800; $mediumHeight = 600;
                $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
                if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                    $validated['banner'] = $path . $uploaded['file_name'];
                    
                    // START: Media Manager Integration
                    try {
                        $mediaPath = $path . $uploaded['file_name'];
                        if(Storage::disk('public')->exists($mediaPath)){
                            $mime = Storage::disk('public')->mimeType($mediaPath);
                            $size = Storage::disk('public')->size($mediaPath);
                            
                            Media::create([
                                'name' => $uploaded['file_name'],
                                'file_name' => $uploaded['file_name'],
                                'path' => $mediaPath,
                                'disk' => 'public',
                                'mime_type' => $mime,
                                'size' => $size,
                                'folder_id' => null,
                                'user_id' => auth()->id() ?? null
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Failed to register CMS Banner upload to Media Manager: " . $e->getMessage());
                    }
                    // END: Media Manager Integration
                }
            } elseif ($request->filled('banner_media_path')) {
                $validated['banner'] = $request->input('banner_media_path');
            }
            
            if ($request->hasFile('page_image')) {
                $file = $request->file('page_image');
                $path = 'cms/page-images/';
                $thumbPath = 'cms/page-images/thumb/';
                $mediumPath = 'cms/page-images/medium/';
                $width = 1200; $height = 800;
                $thumbWidth = 400; $thumbHeight = 300;
                $mediumWidth = 800; $mediumHeight = 600;
                $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
                if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                    $validated['page_image'] = $path . $uploaded['file_name'];

                    // START: Media Manager Integration
                    try {
                        $mediaPath = $path . $uploaded['file_name'];
                        if(Storage::disk('public')->exists($mediaPath)){
                            $mime = Storage::disk('public')->mimeType($mediaPath);
                            $size = Storage::disk('public')->size($mediaPath);
                            
                            Media::create([
                                'name' => $uploaded['file_name'],
                                'file_name' => $uploaded['file_name'],
                                'path' => $mediaPath,
                                'disk' => 'public',
                                'mime_type' => $mime,
                                'size' => $size,
                                'folder_id' => null,
                                'user_id' => auth()->id() ?? null
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Failed to register CMS Page Image upload to Media Manager: " . $e->getMessage());
                    }
                    // END: Media Manager Integration
                }
            } elseif ($request->filled('page_image_media_path')) {
                $validated['page_image'] = $request->input('page_image_media_path');
            }

            // Create CMS page
            $cms = Cms::create($validated);

            if ($cms->template === 'about') {
                $this->ensureAboutFields($cms->id);
            }

            // Handle custom fields
            $payload = $request->all();
            if ($request->has('features_titles') || $request->has('features_descs') || $request->has('features_existing_icons') || $request->hasFile('features_files')) {
                $features = [];
                $titles = $request->input('features_titles', []);
                $descs = $request->input('features_descs', []);
                $existing = $request->input('features_existing_icons', []);
                $files = $request->file('features_files', []);
                $max = max(count($titles), count($descs), count($existing), is_array($files) ? count($files) : 0);
                for ($i = 0; $i < $max; $i++) {
                    $icon = $existing[$i] ?? '';
                    if (is_array($files) && isset($files[$i]) && $files[$i] && $files[$i]->isValid()) {
                        $stored = $files[$i]->store('cms/features', 'public');
                        $icon = 'storage/' . $stored;
                    }
                    $title = $titles[$i] ?? '';
                    $desc = $descs[$i] ?? '';
                    if ($title !== '' || $desc !== '' || $icon !== '') {
                        $features[] = ['icon' => $icon, 'title' => $title, 'desc' => $desc];
                    }
                }
                $payload['features'] = json_encode($features);
            }
            if ($request->has('counters_values') || $request->has('counters_labels')) {
                $values = $request->input('counters_values', []);
                $labels = $request->input('counters_labels', []);
                $max = max(count($values), count($labels));
                $counters = [];
                for ($i = 0; $i < $max; $i++) {
                    $val = $values[$i] ?? '';
                    $lab = $labels[$i] ?? '';
                    if ($val !== '' || $lab !== '') {
                        $counters[] = ['value' => $val, 'label' => $lab];
                    }
                }
                $payload['counters'] = json_encode($counters);
            }
            if ($request->has('team_members_names') || $request->has('team_members_roles') || $request->has('team_members_existing_images') || $request->hasFile('team_members_files')) {
                $names = $request->input('team_members_names', []);
                $roles = $request->input('team_members_roles', []);
                $existing = $request->input('team_members_existing_images', []);
                $files = $request->file('team_members_files', []);
                $max = max(count($names), count($roles), count($existing), is_array($files) ? count($files) : 0);
                $team = [];
                for ($i = 0; $i < $max; $i++) {
                    $img = $existing[$i] ?? '';
                    if (is_array($files) && isset($files[$i]) && $files[$i] && $files[$i]->isValid()) {
                        $stored = $files[$i]->store('cms/team-members', 'public');
                        $img = 'storage/' . $stored;

                        // Register to Media Manager
                        try {
                            $mediaPath = $stored;
                            if(Storage::disk('public')->exists($mediaPath)){
                                $mime = Storage::disk('public')->mimeType($mediaPath);
                                $size = Storage::disk('public')->size($mediaPath);
                                Media::create([
                                    'name' => basename($stored),
                                    'file_name' => basename($stored),
                                    'path' => $mediaPath,
                                    'disk' => 'public',
                                    'mime_type' => $mime,
                                    'size' => $size,
                                    'folder_id' => null,
                                    'user_id' => auth()->id() ?? null
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to register CMS Team Member Image to Media Manager: " . $e->getMessage());
                        }
                    }
                    $name = $names[$i] ?? '';
                    $role = $roles[$i] ?? '';
                    if ($name !== '' || $role !== '' || $img !== '') {
                        $team[] = ['image' => $img, 'name' => $name, 'role' => $role];
                    }
                }
                $payload['team_members'] = json_encode($team);
            }
            CustomFieldHelper::saveCustomFieldValues('cms', $cms->id, $payload);

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'cms_pages',
                $cms->id,
                'Create CMS Page',
                'Created CMS page: ' . $cms->title,
                json_encode($validated)
            );

            return redirect()->route(CustomHelper::getAdminRouteName() . '.cms.index')
                ->with('success', 'CMS page created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $cms = Cms::findOrFail($id);
            $data['page_title'] = 'Edit CMS Page';
            $data['cms'] = $cms;
            $data['templates'] = $this->getAvailableTemplates();
            
            if ($cms->template === 'about') {
                $this->ensureAboutFields($cms->id);
            }
            
            // Get custom field values
            $data['customFieldValues'] = CustomFieldHelper::getCustomFieldValues('cms', $id) ?? [];
            
            // Get custom fields grouped by group name for this specific page
            $data['customFieldsGrouped'] = CustomFieldHelper::getCustomFieldsGrouped('cms', $id) ?? collect();
            
            return view('admin.cms.form', $data);
        } catch (Exception $e) {
            return redirect()->route(CustomHelper::getAdminRouteName() . '.cms.index')
                ->with('error', 'CMS page not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            //prd($request->all());
            $cms = Cms::findOrFail($id);

            // Generate slug logic for update
            $slug = $request->input('slug');
            // Only update slug if slug field is provided and different from current
            if (!empty($slug) && $slug !== $cms->slug) {
                $slug = $this->generateSlug($slug, $id);
            } else {
                // Keep existing slug
                $slug = $cms->slug;
            }

            // Define basic CMS validation rules
            $basicRules = [
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $id,
                'template' => 'nullable|string|max:255',
                'brief' => 'nullable|string|max:500',
                'heading' => 'nullable|string|max:500',
                'description' => 'nullable|string',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'page_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'boolean',
                'featured' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
            ];

            // Get custom field validation rules
            // For existing pages, pass the page ID to get fields for that specific page
            $customFieldValidation = CustomFieldHelper::generateValidationRules('cms', $id);
            
            // Merge custom field validation with basic validation
            $allRules = array_merge($basicRules, $customFieldValidation['rules']);
            $allMessages = $customFieldValidation['messages'];

            // Validate all fields including custom fields
            $validator = \Validator::make($request->all(), $allRules, $allMessages);
            
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator->errors())
                    ->withInput();
            }

            // Get validated data
            $validated = $validator->validated();
            
            // Add the generated slug
            $validated['slug'] = $slug;
            
            // Set default template if none is selected
            if (empty($validated['template'])) {
                $validated['template'] = 'default';
            }
            
            // Handle SEO data
            $seoData = $cms->seo ?? []; // Get existing SEO data
            if ($request->filled('meta_title')) {
                $seoData['meta_title'] = $request->input('meta_title');
            }
            if ($request->filled('meta_keywords')) {
                $seoData['meta_keywords'] = $request->input('meta_keywords');
            }
            if ($request->filled('meta_description')) {
                $seoData['meta_description'] = $request->input('meta_description');
            }
            
            if (!empty($seoData)) {
                $validated['seo'] = $seoData;
            }
            
            // Handle file uploads
            if ($request->hasFile('banner')) {
                if ($cms->banner) {
                    $this->deleteCmsImageFiles($cms->banner);
                }
                $file = $request->file('banner');
                $path = 'cms/banners/';
                $thumbPath = 'cms/banners/thumb/';
                $mediumPath = 'cms/banners/medium/';
                $width = 1600; $height = 600;
                $thumbWidth = 400; $thumbHeight = 300;
                $mediumWidth = 800; $mediumHeight = 600;
                $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
                if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                    $validated['banner'] = $path . $uploaded['file_name'];

                    // START: Media Manager Integration
                    try {
                        $mediaPath = $path . $uploaded['file_name'];
                        if(Storage::disk('public')->exists($mediaPath)){
                            $mime = Storage::disk('public')->mimeType($mediaPath);
                            $size = Storage::disk('public')->size($mediaPath);
                            
                            Media::create([
                                'name' => $uploaded['file_name'],
                                'file_name' => $uploaded['file_name'],
                                'path' => $mediaPath,
                                'disk' => 'public',
                                'mime_type' => $mime,
                                'size' => $size,
                                'folder_id' => null,
                                'user_id' => auth()->id() ?? null
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Failed to register CMS Banner upload to Media Manager: " . $e->getMessage());
                    }
                    // END: Media Manager Integration
                }
            } elseif ($request->filled('banner_media_path')) {
                if ($cms->banner && $cms->banner != $request->input('banner_media_path')) {
                    $this->deleteCmsImageFiles($cms->banner);
                }
                $validated['banner'] = $request->input('banner_media_path');
            }
            
            if ($request->hasFile('page_image')) {
                if ($cms->page_image) {
                    $this->deleteCmsImageFiles($cms->page_image);
                }
                $file = $request->file('page_image');
                $path = 'cms/page-images/';
                $thumbPath = 'cms/page-images/thumb/';
                $mediumPath = 'cms/page-images/medium/';
                $width = 1200; $height = 800;
                $thumbWidth = 400; $thumbHeight = 300;
                $mediumWidth = 800; $mediumHeight = 600;
                $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
                if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                    $validated['page_image'] = $path . $uploaded['file_name'];
                    
                    // START: Media Manager Integration
                    try {
                        $mediaPath = $path . $uploaded['file_name'];
                        if(Storage::disk('public')->exists($mediaPath)){
                            $mime = Storage::disk('public')->mimeType($mediaPath);
                            $size = Storage::disk('public')->size($mediaPath);
                            
                            Media::create([
                                'name' => $uploaded['file_name'],
                                'file_name' => $uploaded['file_name'],
                                'path' => $mediaPath,
                                'disk' => 'public',
                                'mime_type' => $mime,
                                'size' => $size,
                                'folder_id' => null,
                                'user_id' => auth()->id() ?? null
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Failed to register CMS Page Image upload to Media Manager: " . $e->getMessage());
                    }
                    // END: Media Manager Integration
                }
            } elseif ($request->filled('page_image_media_path')) {
                if ($cms->page_image && $cms->page_image != $request->input('page_image_media_path')) {
                    $this->deleteCmsImageFiles($cms->page_image);
                }
                $validated['page_image'] = $request->input('page_image_media_path');
            }
            
            // Update CMS page
            $cms->update($validated);

            if ($cms->template === 'about') {
                $this->ensureAboutFields($id);
            }

            // Handle custom fields
            $payload = $request->all();
            if ($request->has('features_titles') || $request->has('features_descs') || $request->has('features_existing_icons') || $request->hasFile('features_files')) {
                $features = [];
                $titles = $request->input('features_titles', []);
                $descs = $request->input('features_descs', []);
                $existing = $request->input('features_existing_icons', []);
                $files = $request->file('features_files', []);
                $max = max(count($titles), count($descs), count($existing), is_array($files) ? count($files) : 0);
                for ($i = 0; $i < $max; $i++) {
                    $icon = $existing[$i] ?? '';
                    if (is_array($files) && isset($files[$i]) && $files[$i] && $files[$i]->isValid()) {
                        $stored = $files[$i]->store('cms/features', 'public');
                        $icon = 'storage/' . $stored;
                    }
                    $title = $titles[$i] ?? '';
                    $desc = $descs[$i] ?? '';
                    if ($title !== '' || $desc !== '' || $icon !== '') {
                        $features[] = ['icon' => $icon, 'title' => $title, 'desc' => $desc];
                    }
                }
                $payload['features'] = json_encode($features);
            }
            if ($request->has('counters_values') || $request->has('counters_labels')) {
                $values = $request->input('counters_values', []);
                $labels = $request->input('counters_labels', []);
                $max = max(count($values), count($labels));
                $counters = [];
                for ($i = 0; $i < $max; $i++) {
                    $val = $values[$i] ?? '';
                    $lab = $labels[$i] ?? '';
                    if ($val !== '' || $lab !== '') {
                        $counters[] = ['value' => $val, 'label' => $lab];
                    }
                }
                $payload['counters'] = json_encode($counters);
            }
            if ($request->has('team_members_names') || $request->has('team_members_roles') || $request->has('team_members_existing_images') || $request->hasFile('team_members_files')) {
                $names = $request->input('team_members_names', []);
                $roles = $request->input('team_members_roles', []);
                $existing = $request->input('team_members_existing_images', []);
                $files = $request->file('team_members_files', []);
                $max = max(count($names), count($roles), count($existing), is_array($files) ? count($files) : 0);
                $team = [];
                for ($i = 0; $i < $max; $i++) {
                    $img = $existing[$i] ?? '';
                    if (is_array($files) && isset($files[$i]) && $files[$i] && $files[$i]->isValid()) {
                        $stored = $files[$i]->store('cms/team-members', 'public');
                        $img = 'storage/' . $stored;

                        // Register to Media Manager
                        try {
                            $mediaPath = $stored;
                            if(Storage::disk('public')->exists($mediaPath)){
                                $mime = Storage::disk('public')->mimeType($mediaPath);
                                $size = Storage::disk('public')->size($mediaPath);
                                Media::create([
                                    'name' => basename($stored),
                                    'file_name' => basename($stored),
                                    'path' => $mediaPath,
                                    'disk' => 'public',
                                    'mime_type' => $mime,
                                    'size' => $size,
                                    'folder_id' => null,
                                    'user_id' => auth()->id() ?? null
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to register CMS Team Member Image to Media Manager: " . $e->getMessage());
                        }
                    }
                    $name = $names[$i] ?? '';
                    $role = $roles[$i] ?? '';
                    if ($name !== '' || $role !== '' || $img !== '') {
                        $team[] = ['image' => $img, 'name' => $name, 'role' => $role];
                    }
                }
                $payload['team_members'] = json_encode($team);
            }
            CustomFieldHelper::saveCustomFieldValues('cms', $id, $payload);

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'cms_pages',
                $cms->id,
                'Update CMS Page',
                'Updated CMS page: ' . $cms->title,
                json_encode($validated)
            );

            return redirect()->route(CustomHelper::getAdminRouteName() . '.cms.index')
                ->with('success', 'CMS page updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $cms = Cms::findOrFail($id);
            
            // Delete associated images
            /* 
            // STOPPING physical deletion
            if ($cms->banner) {
                \Storage::disk('public')->delete($cms->banner);
            }
            if ($cms->page_image) {
                \Storage::disk('public')->delete($cms->page_image);
            }
            */
            
            $cms->delete();

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'cms_pages',
                $id,
                'Delete CMS Page',
                'Deleted CMS page: ' . $cms->title,
                'Deleted CMS page: ' . $cms->title . ' (ID: ' . $id . ')'
            );

            return response()->json([
                'status' => true,
                'message' => 'CMS page deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting CMS page.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteImage(Request $request, $id)
    {
        try {
            $cms = Cms::findOrFail($id);
            $fieldName = $request->input('field_name');
            
            if (!in_array($fieldName, ['banner', 'page_image'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid field name.',
                ], 400);
            }
            
            $imagePath = $cms->$fieldName;
            if ($imagePath) {
                $this->deleteCmsImageFiles($imagePath);
            }
            $cms->update([$fieldName => null]);
            
            return response()->json([
                'status' => true,
                'message' => 'Image deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting image.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function deleteCmsImageFiles($storedPath)
    {
        if (empty($storedPath)) return false;
        
        // Prevent deleting files managed by Media Library
        if (\Illuminate\Support\Str::startsWith($storedPath, 'media/')) {
            return false;
        }

        /* 
        // STOPPING physical deletion as per user request to preserve Media Library files
        // We only want to remove the reference from the database
        
        $disk = \Storage::disk('public');
        if (\Illuminate\Support\Str::startsWith($storedPath, 'uploads/')) {
            return $disk->exists($storedPath) ? $disk->delete($storedPath) : false;
        }
        $file = basename($storedPath);
        if (\Illuminate\Support\Str::startsWith($storedPath, 'cms/banners/')) {
            $base = 'cms/banners/';
        } elseif (\Illuminate\Support\Str::startsWith($storedPath, 'cms/page-images/')) {
            $base = 'cms/page-images/';
        } else {
            $base = trim(dirname($storedPath), '/') . '/';
        }
        $large = $base . $file;
        $thumb = $base . 'thumb/' . $file;
        $medium = $base . 'medium/' . $file;
        $ok = true;
        foreach ([$large, $thumb, $medium] as $p) {
            if ($disk->exists($p)) {
                $ok = $disk->delete($p) && $ok;
            }
        }
        return $ok;
        */
        return true;
    }

    public function deleteCustomFieldImage(Request $request, $id)
    {
        try {
            $fieldKey = $request->input('field_key');
            $index = (int) $request->input('index');
            if (!in_array($fieldKey, ['features', 'team_members'])) {
                return response()->json(['status' => false, 'message' => 'Invalid field key.'], 400);
            }
            if ($index < 0) {
                return response()->json(['status' => false, 'message' => 'Invalid index.'], 400);
            }
            $customField = CustomField::where('module', 'cms')->where('ref_id', $id)->where('key', $fieldKey)->first();
            if (!$customField) {
                return response()->json(['status' => false, 'message' => 'Custom field not found.'], 404);
            }
            $fieldValue = \App\Models\CustomFieldValue::where('custom_field_id', $customField->id)->where('module_type', 'cms')->where('module_id', $id)->first();
            if (!$fieldValue || !is_string($fieldValue->value) || !strlen($fieldValue->value)) {
                return response()->json(['status' => false, 'message' => 'No data found.'], 404);
            }
            $data = json_decode($fieldValue->value, true);
            if (!is_array($data) || !array_key_exists($index, $data)) {
                return response()->json(['status' => false, 'message' => 'Item not found.'], 404);
            }
            $prop = $fieldKey === 'features' ? 'icon' : 'image';
            $img = $data[$index][$prop] ?? '';
            /*
            // STOPPING physical deletion
            if (is_string($img) && Str::startsWith($img, 'storage/')) {
                $storagePath = Str::replaceFirst('storage/', '', $img);
                if (!Str::startsWith($storagePath, 'media/') && \Storage::disk('public')->exists($storagePath)) {
                    \Storage::disk('public')->delete($storagePath);
                }
            }
            */
            $data[$index][$prop] = '';
            $fieldValue->update(['value' => json_encode($data)]);
            return response()->json(['status' => true, 'message' => 'Image deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting image.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getAvailableTemplates()
    {
        // Scan CMS templates from the frontend folder
        $templatesPath = resource_path('views/frontend/pages/cms');
        $templates = [];

        // Default template should always be first
        $templates['default'] = 'Default Template';

        if (is_dir($templatesPath)) {
            $files = scandir($templatesPath);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && str_ends_with($file, '.blade.php')) {
                    $templateName = str_replace('.blade.php', '', $file);

                    // Skip default as it's already added
                    if ($templateName !== 'default') {
                        // Convert template name to display name (e.g., about-us -> About Us)
                        $displayName = str_replace(['-', '_'], ' ', $templateName);
                        $displayName = ucwords($displayName);
                        $displayName .= ' Template';

                        $templates[$templateName] = $displayName;
                    }
                }
            }
        }

        return $templates;
    }

    /**
     * Generate a unique slug from the given text
     */
    private function generateSlug($text, $excludeId = null)
    {
        // Convert to lowercase and replace spaces/special characters with hyphens
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug); // Remove special characters except spaces and hyphens
        $slug = preg_replace('/[\s-]+/', '-', $slug); // Replace multiple spaces/hyphens with single hyphen
        $slug = trim($slug, '-'); // Remove leading/trailing hyphens
        
        // If slug is empty, use a default
        if (empty($slug)) {
            $slug = 'page-' . time();
        }
        
        // Check if slug already exists
        $query = Cms::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $counter = 1;
        $originalSlug = $slug;
        
        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            // Update query for next iteration
            $query = Cms::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }
        
        return $slug;
    }

    private function ensureAboutFields(int $pageId): void
    {
        $required = [
            ['label' => 'Features JSON', 'key' => 'features', 'type' => 'textarea', 'group' => 'Features'],
            ['label' => 'Counters JSON', 'key' => 'counters', 'type' => 'textarea', 'group' => 'Stats'],
            ['label' => 'Team Members JSON', 'key' => 'team_members', 'type' => 'textarea', 'group' => 'Team'],
        ];
        foreach ($required as $f) {
            $exists = CustomField::where('module', 'cms')
                ->where('ref_id', $pageId)
                ->where('key', $f['key'])
                ->first();
            if (!$exists) {
                CustomField::create([
                    'label' => $f['label'],
                    'key' => $f['key'],
                    'type' => $f['type'],
                    'group_name' => $f['group'],
                    'module' => 'cms',
                    'ref_id' => $pageId,
                ]);
            }
        }
    }
}
