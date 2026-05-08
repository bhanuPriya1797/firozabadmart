<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\TourPackageItinerary;
use App\Models\TourPackageImage;
use App\Models\TempImage;
use App\Models\Destination;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class TourPackageController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = TourPackage::with('destination')->orderByDesc('id');
                if ($request->filled('status') && $request->status !== '') {
                    $query->where('status', (int)$request->status);
                }
                if ($request->filled('name')) {
                    $search = $request->name;
                    $query->where('name', 'like', '%' . $search . '%');
                }
                return DataTables::of($query)
                    ->editColumn('name', function ($row) {
                        try {
                            $url = route('tour-packages.show', $row->slug);
                            $name = e($row->name);
                            $slug = e($row->slug);
                            return '<div><div class="fw-600">' . $name . '</div><div class="small"><a href="' . $url . '" target="_blank">' . $slug . '</a></div></div>';
                        } catch (\Throwable $e) {
                            return e($row->name);
                        }
                    })
                    ->addColumn('type_label', function ($row) {
                        return $row->type === 'individual' ? 'Individual' : 'Group';
                    })
                    ->addColumn('destination_name', function ($row) {
                        return optional($row->destination)->destination_name ?: '-';
                    })
                    ->addColumn('date_range', function ($row) {
                        $s = $row->start_date ? $row->start_date->format('d M Y') : '';
                        $e = $row->end_date ? $row->end_date->format('d M Y') : '';
                        if ($s && $e) return $s . ' - ' . $e;
                        return $s ?: $e;
                    })
                    ->addColumn('seats', function ($row) {
                        return (string)$row->remaining_seats . '/' . (string)$row->max_seats;
                    })
                    ->addColumn('price_display', function ($row) {
                        return number_format((float)$row->price, 2);
                    })
                    ->addColumn('status_badge', function ($row) {
                        $cls = $row->status ? 'bg-label-success' : 'bg-label-secondary';
                        $txt = $row->status ? 'Active' : 'Inactive';
                        return '<span class="badge ' . $cls . '">' . $txt . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $routeName = CustomHelper::getAdminRouteName();
                        $actions = '';
                        $has = false;
                        if (auth()->user()->hasRole('SuperAdmin') ||
                            auth()->user()->can('tour_packages.create') ||
                            auth()->user()->can('tour_packages.edit') ||
                            auth()->user()->can('tour_packages.delete')) {
                            $editUrl = route($routeName . '.tour-packages.edit', $row->id);
                            $deleteUrl = route($routeName . '.tour-packages.destroy', $row->id);
                            $duplicateUrl = route($routeName . '.tour-packages.duplicate', $row->id);
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('tour_packages.edit')) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '">
                                    <i class="icon-base ti tabler-edit me-1"></i> Edit
                                </a>';
                                $has = true;
                            }
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('tour_packages.create')) {
                                $actions .= '<a class="dropdown-item waves-effect btn-duplicate-tour-package" href="javascript:void(0);" data-url="' . $duplicateUrl . '">
                                    <i class="icon-base ti tabler-copy me-1"></i> Duplicate
                                </a>';
                                $has = true;
                            }
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('tour_packages.delete')) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-tour-package" href="javascript:void(0);" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $has = true;
                            }
                            $actions .= '</div></div>';
                        }
                        return $has ? $actions : '';
                    })
                    ->rawColumns(['name', 'status_badge', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('TourPackageController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }
        return view('admin.tour_packages.index');
    }

    public function create()
    {
        $item = new TourPackage();
        $destinations = Destination::orderBy('destination_name')->get();
        return view('admin.tour_packages.form', compact('item', 'destinations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'description' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_seats' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|max:8',
            'status' => 'required|in:0,1',
            'included' => 'nullable|array',
            'included.*' => 'nullable|string|max:500',
            'excluded' => 'nullable|array',
            'excluded.*' => 'nullable|string|max:500',
            'main_banner' => 'nullable|image',
            'additional_banners' => 'nullable|array',
            'additional_banners.*' => 'image',
            'itineraries' => 'nullable|array',
            'itineraries.*.day_number' => 'nullable|integer|min:1',
            'itineraries.*.title' => 'nullable|string|max:255',
            'itineraries.*.content' => 'nullable|string',
            'slug' => 'nullable|string|max:255',
            'discount_type' => 'nullable|in:flat,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string',
            'is_sukoon' => 'nullable|in:0,1',
            'sukoon_featured' => 'nullable|in:0,1',
            'badge_label' => 'nullable|string|max:64',
        ]);

        $slugInput = $request->input('slug');
        if ($slugInput) {
            $base = Str::slug($slugInput);
            $data['slug'] = CustomHelper::GetSlug('tour_packages', 'id', 0, $base);
        } else {
            $base = Str::slug($data['name']);
            $data['slug'] = CustomHelper::GetSlug('tour_packages', 'id', 0, $base);
        }
        $data['included'] = !empty($data['included']) ? $data['included'] : [];
        $data['excluded'] = !empty($data['excluded']) ? $data['excluded'] : [];

        $originalPrice = (float)($data['price'] ?? 0);
        $discounted = null;
        if (!empty($data['discount_type']) && $data['discount_value'] !== null) {
            $dv = (float)$data['discount_value'];
            if ($data['discount_type'] === 'flat') {
                $discounted = $originalPrice - $dv;
            } elseif ($data['discount_type'] === 'percent') {
                $discounted = $originalPrice - ($originalPrice * ($dv / 100));
            }
            if ($discounted !== null && $discounted < 0) {
                $discounted = 0.0;
            }
        }

        $item = TourPackage::create([
            'destination_id' => $data['destination_id'] ?? null,
            'name' => $data['name'],
            'type' => 'group',
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'additional_info' => $data['additional_info'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'max_seats' => (int)$data['max_seats'],
            'booked_seats' => 0,
            'price' => $data['price'] ?? 0,
            'currency_code' => $data['currency_code'] ?? 'INR',
            'status' => (int)$data['status'],
            'included' => $data['included'],
            'excluded' => $data['excluded'],
            'discount_type' => $data['discount_type'] ?? null,
            'discount_value' => $data['discount_value'] ?? null,
            'discounted_price' => $discounted,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_keywords' => $data['meta_keywords'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'is_sukoon' => (int)($data['is_sukoon'] ?? 0),
            'sukoon_featured' => (int)($data['sukoon_featured'] ?? 0),
            'badge_label' => $data['badge_label'] ?? null,
        ]);

        $this->handleImages($request, $item->id);
        $this->attachTempImages($request, $item->id);
        $this->handleItineraries($request, $item->id);

        CustomHelper::recordActionLog(
            url()->current(),
            'tour_packages',
            $item->id,
            'Create Tour Package',
            'Created tour package: ' . $item->name,
            json_encode($data)
        );

        if ($request->input('stay_on_page')) {
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.tour-packages.edit', $item->id)->with('success', 'Tour package created successfully');
        }
        return redirect()->route($this->ADMIN_ROUTE_NAME . '.tour-packages.index')->with('success', 'Tour package created successfully');
    }

    public function edit($id)
    {
        $item = TourPackage::with(['itineraries', 'images'])->findOrFail($id);
        $destinations = Destination::orderBy('destination_name')->get();
        return view('admin.tour_packages.form', compact('item', 'destinations'));
    }

    public function update(Request $request, $id)
    {
        $item = TourPackage::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'description' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_seats' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|max:8',
            'status' => 'required|in:0,1',
            'included' => 'nullable|array',
            'included.*' => 'nullable|string|max:500',
            'excluded' => 'nullable|array',
            'excluded.*' => 'nullable|string|max:500',
            'main_banner' => 'nullable|image',
            'additional_banners' => 'nullable|array',
            'additional_banners.*' => 'image',
            'itineraries' => 'nullable|array',
            'itineraries.*.day_number' => 'nullable|integer|min:1',
            'itineraries.*.title' => 'nullable|string|max:255',
            'itineraries.*.content' => 'nullable|string',
            'slug' => 'nullable|string|max:255',
            'discount_type' => 'nullable|in:flat,percent',
            'discount_value' => 'nullable|numeric|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string',
            'is_sukoon' => 'nullable|in:0,1',
            'sukoon_featured' => 'nullable|in:0,1',
            'badge_label' => 'nullable|string|max:64',
        ]);

        $slugInput = $request->input('slug');
        if ($slugInput && $slugInput !== $item->slug) {
            $base = Str::slug($slugInput);
            $data['slug'] = CustomHelper::GetSlug('tour_packages', 'id', $item->id, $base);
        } elseif ($item->name !== $data['name']) {
            $base = Str::slug($data['name']);
            $data['slug'] = CustomHelper::GetSlug('tour_packages', 'id', $item->id, $base);
        }
        $data['included'] = !empty($data['included']) ? $data['included'] : [];
        $data['excluded'] = !empty($data['excluded']) ? $data['excluded'] : [];

        $originalPrice = (float)($data['price'] ?? $item->price ?? 0);
        $discounted = null;
        if (!empty($data['discount_type']) && $data['discount_value'] !== null) {
            $dv = (float)$data['discount_value'];
            if ($data['discount_type'] === 'flat') {
                $discounted = $originalPrice - $dv;
            } elseif ($data['discount_type'] === 'percent') {
                $discounted = $originalPrice - ($originalPrice * ($dv / 100));
            }
            if ($discounted !== null && $discounted < 0) {
                $discounted = 0.0;
            }
        }

        $item->fill([
            'destination_id' => $data['destination_id'] ?? null,
            'name' => $data['name'],
            'type' => $item->type ?? 'group',
            'slug' => $data['slug'] ?? $item->slug,
            'description' => $data['description'] ?? null,
            'additional_info' => $data['additional_info'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'max_seats' => (int)$data['max_seats'],
            'price' => $data['price'] ?? 0,
            'currency_code' => $data['currency_code'] ?? $item->currency_code,
            'status' => (int)$data['status'],
            'included' => $data['included'],
            'excluded' => $data['excluded'],
            'discount_type' => $data['discount_type'] ?? $item->discount_type,
            'discount_value' => $data['discount_value'] ?? $item->discount_value,
            'discounted_price' => $discounted ?? $item->discounted_price,
            'meta_title' => $data['meta_title'] ?? $item->meta_title,
            'meta_keywords' => $data['meta_keywords'] ?? $item->meta_keywords,
            'meta_description' => $data['meta_description'] ?? $item->meta_description,
            'is_sukoon' => (int)($data['is_sukoon'] ?? $item->is_sukoon),
            'sukoon_featured' => (int)($data['sukoon_featured'] ?? $item->sukoon_featured),
            'badge_label' => $data['badge_label'] ?? $item->badge_label,
        ])->save();

        $this->handleImages($request, $item->id);
        $this->attachTempImages($request, $item->id);
        $this->replaceItineraries($request, $item->id);

        CustomHelper::recordActionLog(
            url()->current(),
            'tour_packages',
            $item->id,
            'Update Tour Package',
            'Updated tour package: ' . $item->name,
            json_encode($data)
        );

        if ($request->input('stay_on_page')) {
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.tour-packages.edit', $item->id)->with('success', 'Tour package updated successfully');
        }
        return redirect()->route($this->ADMIN_ROUTE_NAME . '.tour-packages.index')->with('success', 'Tour package updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $item = TourPackage::with('images')->findOrFail($id);

        if ($item->main_banner) {
            $this->deleteImageFiles($item->main_banner);
        }
        foreach ($item->images as $img) {
            $this->deleteImageFiles($img->file_name);
        }

        $item->delete();

        CustomHelper::recordActionLog(
            url()->current(),
            'tour_packages',
            $id,
            'Delete Tour Package',
            'Deleted tour package: ' . ($item->name ?? ''),
            ''
        );

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Tour package deleted successfully']);
        }
        return redirect()->route($this->ADMIN_ROUTE_NAME . '.tour-packages.index')->with('success', 'Tour package deleted successfully');
    }

    public function duplicate(Request $request, $id)
    {
        $src = TourPackage::with(['itineraries', 'images'])->findOrFail($id);
        $baseSlug = $src->slug ?: Str::slug($src->name);
        $newSlug = $this->nextSlug('tour_packages', $baseSlug);
        $copy = $src->replicate();
        $copy->slug = $newSlug;
        $copy->booked_seats = 0;
        $copy->name = $this->nextCopyName($src->name);
        $copy->main_banner = $this->copyImageVariants($src->main_banner);
        $copy->push();
        foreach ($src->images as $img) {
            TourPackageImage::create([
                'tour_package_id' => $copy->id,
                'file_name' => $this->copyImageVariants($img->file_name),
                'sort_order' => (int)$img->sort_order,
            ]);
        }
        foreach ($src->itineraries as $it) {
            $row = $it->replicate();
            $row->tour_package_id = $copy->id;
            $row->save();
        }
        CustomHelper::recordActionLog(
            url()->current(),
            'tour_packages',
            $copy->id,
            'Duplicate Tour Package',
            'Duplicated tour package: ' . ($src->name ?? ''),
            json_encode($copy->toArray())
        );
        return response()->json([
            'success' => true,
            'message' => 'Tour package duplicated successfully',
            'id' => $copy->id,
            'redirect' => route($this->ADMIN_ROUTE_NAME . '.tour-packages.edit', $copy->id),
        ]);
    }

    private function nextCopyName(string $original): string
    {
        $root = preg_replace('/^Copy\s+\d+\s*-\s*/i', '', $original);
        $existing = TourPackage::where('name', 'like', 'Copy% - ' . $root)->pluck('name')->all();
        $nums = [];
        foreach ($existing as $n) {
            if (preg_match('/^Copy\s+(\d+)\s*-\s*/i', $n, $m)) {
                $nums[] = (int)$m[1];
            }
        }
        $next = empty($nums) ? 1 : (max($nums) + 1);
        return 'Copy ' . $next . ' - ' . $root;
    }

    private function copyImageVariants(?string $fileName): ?string
    {
        if (empty($fileName)) return null;
        $disk = Storage::disk('public');
        $base = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $safe = CustomHelper::sanitizeUploadFile($base . '.' . $ext);
        $newName = date('YmdHis') . '-dup-' . $safe;
        $paths = [
            ['from' => 'tour-packages/' . $fileName, 'to' => 'tour-packages/' . $newName],
            ['from' => 'tour-packages/thumb/' . $fileName, 'to' => 'tour-packages/thumb/' . $newName],
            ['from' => 'tour-packages/medium/' . $fileName, 'to' => 'tour-packages/medium/' . $newName],
        ];
        $copiedAny = false;
        foreach ($paths as $p) {
            if ($disk->exists($p['from'])) {
                $disk->copy($p['from'], $p['to']);
                $copiedAny = true;
            }
        }
        return $copiedAny ? $newName : $fileName;
    }

    private function nextSlug(string $table, string $base): string
    {
        $root = preg_replace('/-\d+$/', '', $base);
        $existing = \DB::table($table)->where('slug', 'like', $root . '%')->pluck('slug')->all();
        $nums = [];
        foreach ($existing as $s) {
            if ($s === $root) {
                $nums[] = 0;
                continue;
            }
            if (preg_match('/^' . preg_quote($root, '/') . '\-(\d+)$/', $s, $m)) {
                $nums[] = (int)$m[1];
            }
        }
        $next = (empty($nums) ? 1 : (max($nums) + 1));
        return $root . '-' . $next;
    }

    public function deleteMainBanner(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        $item = TourPackage::findOrFail($request->id);
        $deleted = false;
        if ($item->main_banner) {
            $deleted = $this->deleteImageFiles($item->main_banner);
            if ($deleted) {
                $item->main_banner = null;
                $item->save();
            }
        }
        return response()->json(['success' => $deleted]);
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        $img = TourPackageImage::findOrFail($request->id);
        $deleted = $this->deleteImageFiles($img->file_name);
        if ($deleted) {
            $img->delete();
        }
        return response()->json(['success' => $deleted]);
    }

    private function handleImages(Request $request, int $id): void
    {
        $path = 'tour-packages/';
        $thumbPath = 'tour-packages/thumb/';
        $width = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_WIDTH') ?: 1920;
        $height = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_HEIGHT') ?: 1920;
        $thumbWidth = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_THUMB_WIDTH') ?: 336;
        $thumbHeight = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_THUMB_HEIGHT') ?: 336;
        $mediumPath = 'tour-packages/medium/';
        $mediumWidth = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_MEDIUM_WIDTH') ?: 450;
        $mediumHeight = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_MEDIUM_HEIGHT') ?: 450;

        $item = TourPackage::find($id);
        if (!$item) {
            return;
        }

        if ($request->hasFile('main_banner')) {
            $file = $request->file('main_banner');
            $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
            if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                $old = $item->main_banner;
                $item->main_banner = $uploaded['file_name'];
                $item->save();
                if ($old) {
                    $this->deleteImageFiles($old);
                }
            }
        }

        if ($request->hasFile('additional_banners')) {
            foreach ($request->file('additional_banners') as $file) {
                $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
                if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                    TourPackageImage::create([
                        'tour_package_id' => $item->id,
                        'file_name' => $uploaded['file_name'],
                        'sort_order' => 0,
                    ]);
                }
            }
        }
    }

    private function attachTempImages(Request $request, int $id): void
    {
        $item = TourPackage::find($id);
        if (!$item) return;
        $ids = (array)$request->input('temp_images', []);
        if (empty($ids)) return;
        foreach ($ids as $tid) {
            $temp = TempImage::find($tid);
            if (!$temp) continue;
            if (!empty($temp->file_name)) {
                TourPackageImage::create([
                    'tour_package_id' => $item->id,
                    'file_name' => $temp->file_name,
                    'sort_order' => 0,
                ]);
            }
            $temp->delete();
        }
    }

    private function deleteImageFiles(?string $file): bool
    {
        if (!$file) return false;
        $path = 'tour-packages/' . $file;
        $thumb = 'tour-packages/thumb/' . $file;
        $medium = 'tour-packages/medium/' . $file;
        $disk = Storage::disk('public');
        $ok = true;
        if ($disk->exists($path)) {
            $ok = $disk->delete($path) && $ok;
        }
        if ($disk->exists($thumb)) {
            $ok = $disk->delete($thumb) && $ok;
        }
        if ($disk->exists($medium)) {
            $ok = $disk->delete($medium) && $ok;
        }
        return $ok;
    }

    private function handleItineraries(Request $request, int $id): void
    {
        $item = TourPackage::find($id);
        if (!$item) return;
        $rows = (array)$request->input('itineraries', []);
        foreach ($rows as $row) {
            $dn = isset($row['day_number']) ? (int)$row['day_number'] : null;
            $title = isset($row['title']) ? (string)$row['title'] : null;
            $content = isset($row['content']) ? (string)$row['content'] : null;
            if (!$dn) continue;
            TourPackageItinerary::create([
                'tour_package_id' => $item->id,
                'day_number' => $dn,
                'title' => $title,
                'content' => $content,
                'sort_order' => 0,
            ]);
        }
    }

    private function replaceItineraries(Request $request, int $id): void
    {
        $item = TourPackage::find($id);
        if (!$item) return;
        TourPackageItinerary::where('tour_package_id', $item->id)->delete();
        $this->handleItineraries($request, $item->id);
    }

    public function uploadTempImageChunk(Request $request)
    {
        $request->validate([
            'chunk' => 'required',
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
            'upload_key' => 'required|string',
            'file_name' => 'required|string',
        ]);
        $uploadKey = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->upload_key);
        $total = (int)$request->total_chunks;
        $index = (int)$request->chunk_index;
        $baseName = basename($request->file_name);
        $tmpBase = storage_path('app/tmp_uploads/' . $uploadKey);
        if (!File::exists($tmpBase)) {
            File::makeDirectory($tmpBase, 0777, true, true);
        }
        $chunkFile = $tmpBase . '/part_' . $index;
        $chunk = $request->file('chunk');
        if ($chunk && $chunk->isValid()) {
            File::put($chunkFile, File::get($chunk->getPathname()));
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid chunk'], 422);
        }
        if ($index < $total - 1) {
            return response()->json(['success' => true, 'partial' => true]);
        }
        $assembledPath = $tmpBase . '/' . $baseName;
        $out = fopen($assembledPath, 'wb');
        for ($i = 0; $i < $total; $i++) {
            $part = $tmpBase . '/part_' . $i;
            $in = fopen($part, 'rb');
            stream_copy_to_stream($in, $out);
            fclose($in);
        }
        fclose($out);
        $path = 'tour-packages/';
        $thumbPath = 'tour-packages/thumb/';
        $mediumPath = 'tour-packages/medium/';
        $width = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_WIDTH') ?: 1024;
        $height = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_HEIGHT') ?: 1024;
        $thumbWidth = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_THUMB_WIDTH') ?: 336;
        $thumbHeight = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_THUMB_HEIGHT') ?: 336;
        $mediumWidth = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_MEDIUM_WIDTH') ?: 450;
        $mediumHeight = (int)CustomHelper::getSetting('TOUR_PACKAGE_IMG_MEDIUM_HEIGHT') ?: 450;
        $uploaded = CustomHelper::UploadImage($assembledPath, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
        foreach (glob($tmpBase . '/part_*') as $p) { @unlink($p); }
        @unlink($assembledPath);
        @rmdir($tmpBase);
        if (empty($uploaded['success']) || empty($uploaded['file_name'])) {
            return response()->json(['success' => false, 'message' => 'Failed to process image']);
        }
        $size = 0;
        $mime = null;
        $disk = Storage::disk('public');
        $fullPath = 'tour-packages/' . $uploaded['file_name'];
        if ($disk->exists($fullPath)) {
            $size = $disk->size($fullPath);
        }
        $temp = TempImage::create([
            'upload_key' => $uploadKey,
            'file_name' => $uploaded['file_name'],
            'mime' => $mime,
            'size' => $size,
            'created_by' => optional(auth()->user())->id,
        ]);
        return response()->json([
            'success' => true,
            'id' => $temp->id,
            'file_name' => $uploaded['file_name'],
            'preview_url' => asset('storage/tour-packages/thumb/' . $uploaded['file_name']),
        ]);
    }

    public function deleteTempImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        $temp = TempImage::find($request->id);
        if (!$temp) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $deleted = $this->deleteImageFiles($temp->file_name);
        if ($deleted) {
            $temp->delete();
        }
        return response()->json(['success' => $deleted]);
    }

    public function cleanupTempImages(Request $request)
    {
        $days = (int)($request->input('days', 7));
        $threshold = now()->subDays($days);
        $stales = TempImage::where('created_at', '<', $threshold)->get();
        $deletedCount = 0;
        foreach ($stales as $temp) {
            if ($this->deleteImageFiles($temp->file_name)) {
                $temp->delete();
                $deletedCount++;
            }
        }
        return response()->json(['success' => true, 'deleted' => $deletedCount]);
    }
}
