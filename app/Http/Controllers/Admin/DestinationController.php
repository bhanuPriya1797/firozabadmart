<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\DestinationImage;
use App\Models\DestinationType;
use App\Models\DestinationInfo;
use App\Models\TempImage;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class DestinationController extends Controller
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
                $query = Destination::query()
                    ->withCount('children')
                    ->orderBy('sort_order')
                    ->orderByDesc('id');

                if ($request->filled('parent_id')) {
                    $query->where('parent_id', (int)$request->parent_id);
                }
                if ($request->filled('destination_name')) {
                    $search = $request->destination_name;
                    $query->where('destination_name', 'like', '%' . $search . '%');
                }
                if ($request->filled('status') && $request->status !== '') {
                    $query->where('status', (int)$request->status);
                }

                return DataTables::of($query)
                    ->addColumn('title', function ($row) {
                        $routeName = CustomHelper::getAdminRouteName();
                        if ($row->children_count > 0) {
                            $url = route($routeName . '.destinations.index', ['parent_id' => $row->id]);
                            return '<a href="' . $url . '">' . e($row->destination_name) . '</a>';
                        }
                        return e($row->destination_name);
                    })
                    ->addColumn('slug', function ($row) {
                        return e($row->slug);
                    })
                    ->addColumn('status_badge', function ($row) {
                        $cls = $row->status ? 'bg-label-success' : 'bg-label-secondary';
                        $txt = $row->status ? 'Active' : 'Inactive';
                        return '<span class="badge ' . $cls . '">' . $txt . '</span>';
                    })
                    ->addColumn('featured_badge', function ($row) {
                        $cls = $row->featured ? 'bg-label-warning' : 'bg-label-secondary';
                        $txt = $row->featured ? 'Yes' : 'No';
                        return '<span class="badge ' . $cls . '">' . $txt . '</span>';
                    })
                    ->addColumn('sort_order', function ($row) {
                        return (string)$row->sort_order;
                    })
                    ->addColumn('action', function ($row) {
                        $routeName = CustomHelper::getAdminRouteName();
                        $actions = '';
                        $has = false;
                        if (
                            auth()->user()->hasRole('SuperAdmin') ||
                            auth()->user()->can('destinations.edit') ||
                            auth()->user()->can('destinations.delete') ||
                            auth()->user()->can('destinations.view')
                        ) {
                            $editUrl = route($routeName . '.destinations.edit', $row->id);
                            $deleteUrl = route($routeName . '.destinations.destroy', $row->id);
                            // $infoUrl = route($routeName . '.destinations.info.index', $row->id);
                            $duplicateUrl = route($routeName . '.destinations.duplicate', $row->id);
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('destinations.edit')) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '">
                                    <i class="icon-base ti tabler-edit me-1"></i> Edit
                                </a>';
                                $has = true;
                            }
                            // if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('destinations.view')) {
                            //     $actions .= '<a class="dropdown-item waves-effect" href="' . $infoUrl . '">
                            //         <i class="icon-base ti tabler-info-circle me-1"></i> Info
                            //     </a>';
                            //     $has = true;
                            // }
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('destinations.create')) {
                                $actions .= '<a class="dropdown-item waves-effect btn-duplicate-destination" href="javascript:void(0);" data-url="' . $duplicateUrl . '">
                                    <i class="icon-base ti tabler-copy me-1"></i> Duplicate
                                </a>';
                                $has = true;
                            }
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('destinations.delete')) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-destination" href="javascript:void(0);" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $has = true;
                            }
                            $actions .= '</div></div>';
                        }
                        return $has ? $actions : '';
                    })
                    ->rawColumns(['title', 'status_badge', 'featured_badge', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('DestinationController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        return view('admin.destinations.index');
    }

    public function create()
    {
        $item = new Destination();
        $destinations = Destination::orderBy('destination_name')->get();
        $destination_types = DestinationType::orderBy('name')->get();
        return view('admin.destinations.form', compact('item', 'destinations', 'destination_types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'destination_name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
            'destination_type' => 'nullable|integer',
            'brief' => 'nullable|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'latitude' => 'nullable|string|max:255',
            'longtitude' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'featured' => 'nullable|in:0,1',
            'best_months' => 'nullable|array',
            'image' => 'nullable|image',
            'feature_image' => 'nullable|image',
            'banner_image' => 'nullable|image',
        ]);

        $slugInput = $request->input('slug');
        if ($slugInput) {
            $base = Str::slug($slugInput);
            $data['slug'] = CustomHelper::GetSlug('destinations', 'id', 0, $base);
        } else {
            $base = Str::slug($data['destination_name']);
            $data['slug'] = CustomHelper::GetSlug('destinations', 'id', 0, $base);
        }
        $data['featured'] = $request->input('featured', 0) ? 1 : 0;
        $data['parent_id'] = (int)($data['parent_id'] ?? 0);
        $data['destination_type'] = (int)($data['destination_type'] ?? 0);
        $data['best_months'] = !empty($data['best_months']) ? json_encode($data['best_months']) : json_encode([]);

        $item = Destination::create($data);

        $this->handleImages($request, $item->id);
        $this->attachTempImages($request, $item->id);

        CustomHelper::recordActionLog(
            url()->current(),
            'destinations',
            $item->id,
            'Create Destination',
            'Created destination: ' . $item->destination_name,
            json_encode($data)
        );

        if ($request->input('stay_on_page')) {
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.edit', $item->id)->with('success', 'Destination created successfully');
        }
        return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.index')->with('success', 'Destination created successfully');
    }

    public function edit($id)
    {
        $item = Destination::findOrFail($id);
        $destinations = Destination::orderBy('destination_name')->get();
        $destination_types = DestinationType::orderBy('name')->get();
        return view('admin.destinations.form', compact('item', 'destinations', 'destination_types'));
    }

    public function update(Request $request, $id)
    {
        $item = Destination::findOrFail($id);
        $data = $request->validate([
            'destination_name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
            'destination_type' => 'nullable|integer',
            'brief' => 'nullable|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'latitude' => 'nullable|string|max:255',
            'longtitude' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:0,1',
            'featured' => 'nullable|in:0,1',
            'best_months' => 'nullable|array',
            'image' => 'nullable|image',
            'feature_image' => 'nullable|image',
            'banner_image' => 'nullable|image',
        ]);

        $slugInput = $request->input('slug');
        if ($slugInput && $slugInput !== $item->slug) {
            $base = Str::slug($slugInput);
            $data['slug'] = CustomHelper::GetSlug('destinations', 'id', $item->id, $base);
        } elseif ($item->destination_name !== $data['destination_name']) {
            $base = Str::slug($data['destination_name']);
            $data['slug'] = CustomHelper::GetSlug('destinations', 'id', $item->id, $base);
        }

        $data['featured'] = $request->input('featured', 0) ? 1 : 0;
        $data['parent_id'] = (int)($data['parent_id'] ?? 0);
        $data['destination_type'] = (int)($data['destination_type'] ?? 0);
        $data['best_months'] = !empty($data['best_months']) ? json_encode($data['best_months']) : json_encode([]);

        $item->fill($data)->save();

        $this->handleImages($request, $item->id);
        $this->attachTempImages($request, $item->id);

        CustomHelper::recordActionLog(
            url()->current(),
            'destinations',
            $item->id,
            'Update Destination',
            'Updated destination: ' . $item->destination_name,
            json_encode($data)
        );

        if ($request->input('stay_on_page')) {
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.edit', $item->id)->with('success', 'Destination updated successfully');
        }
        return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.index')->with('success', 'Destination updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $item = Destination::findOrFail($id);

        $this->deleteImageFiles($item->image);
        $this->deleteImageFiles($item->feature_image);
        $this->deleteImageFiles($item->banner_image);

        $item->delete();

        CustomHelper::recordActionLog(
            url()->current(),
            'destinations',
            $id,
            'Delete Destination',
            'Deleted destination: ' . ($item->destination_name ?? ''),
            ''
        );

        if ($request->ajax()) {
            return response()->json(['status' => true, 'message' => 'Destination deleted successfully']);
        }
        return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.index')->with('success', 'Destination deleted successfully');
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:image,feature_image,banner_image'
        ]);
        $item = Destination::findOrFail($request->id);
        $field = $request->type;
        $file = $item->{$field};
        $deleted = false;
        if ($file) {
            $deleted = $this->deleteImageFiles($file);
            if ($deleted) {
                $item->{$field} = null;
                $item->save();
            }
        }
        return response()->json(['success' => $deleted]);
    }

    public function duplicate(Request $request, $id)
    {
        $src = Destination::with(['images', 'infos'])->findOrFail($id);
        $baseSlug = $src->slug ?: Str::slug($src->destination_name);
        $newSlug = $this->nextSlug('destinations', $baseSlug);
        $copy = $src->replicate();
        $copy->slug = $newSlug;
        $copy->image = $this->copyImageVariants($src->image);
        $copy->feature_image = $this->copyImageVariants($src->feature_image);
        $copy->banner_image = $this->copyImageVariants($src->banner_image);
        $copy->push();
        foreach ($src->images as $img) {
            DestinationImage::create([
                'destination_id' => $copy->id,
                'file_name' => $this->copyImageVariants($img->file_name),
                'sort_order' => (int)$img->sort_order,
            ]);
        }
        foreach ($src->infos as $info) {
            $row = $info->replicate();
            $row->destination_id = $copy->id;
            $row->save();
        }
        return response()->json([
            'success' => true,
            'message' => 'Destination duplicated successfully',
            'id' => $copy->id,
            'redirect' => route($this->ADMIN_ROUTE_NAME . '.destinations.edit', $copy->id),
        ]);
    }

    private function handleImages(Request $request, int $id): void
    {
        $path = 'destinations/';
        $thumbPath = 'destinations/thumb/';
        $mediumPath = 'destinations/medium/';
        $width = (int)CustomHelper::getSetting('DESTINATION_IMG_WIDTH') ?: 1920;
        $height = (int)CustomHelper::getSetting('DESTINATION_IMG_HEIGHT') ?: 1920;
        $thumbWidth = (int)CustomHelper::getSetting('DESTINATION_IMG_THUMB_WIDTH') ?: 336;
        $thumbHeight = (int)CustomHelper::getSetting('DESTINATION_IMG_THUMB_HEIGHT') ?: 336;
        $mediumWidth = (int)CustomHelper::getSetting('DESTINATION_IMG_MEDIUM_WIDTH') ?: 450;
        $mediumHeight = (int)CustomHelper::getSetting('DESTINATION_IMG_MEDIUM_HEIGHT') ?: 450;

        $item = Destination::find($id);
        if (!$item) {
            return;
        }

        foreach (['image', 'feature_image', 'banner_image'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $uploaded = CustomHelper::UploadImage($file, $path, '', $width, $height, true, $thumbPath, $thumbWidth, $thumbHeight, true, $mediumPath, $mediumWidth, $mediumHeight);
                if (!empty($uploaded['success']) && !empty($uploaded['file_name'])) {
                    $old = $item->{$field};
                    $item->{$field} = $uploaded['file_name'];
                    $item->save();
                    if ($old) {
                        $this->deleteImageFiles($old);
                    }
                }
            }
        }
    }

    private function attachTempImages(Request $request, int $id): void
    {
        $item = Destination::find($id);
        if (!$item) return;
        $ids = (array)$request->input('temp_images', []);
        if (empty($ids)) return;
        foreach ($ids as $tid) {
            $temp = TempImage::find($tid);
            if (!$temp) continue;
            if (!empty($temp->file_name)) {
                DestinationImage::create([
                    'destination_id' => $item->id,
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
        $path = 'destinations/' . $file;
        $thumb = 'destinations/thumb/' . $file;
        $medium = 'destinations/medium/' . $file;
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

    private function copyImageVariants(?string $fileName): ?string
    {
        if (empty($fileName)) return null;
        $disk = Storage::disk('public');
        $base = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $safe = CustomHelper::sanitizeUploadFile($base . '.' . $ext);
        $newName = date('YmdHis') . '-dup-' . $safe;
        $paths = [
            ['from' => 'destinations/' . $fileName, 'to' => 'destinations/' . $newName],
            ['from' => 'destinations/thumb/' . $fileName, 'to' => 'destinations/thumb/' . $newName],
            ['from' => 'destinations/medium/' . $fileName, 'to' => 'destinations/medium/' . $newName],
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

    public function deleteGalleryImage(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        $img = DestinationImage::find($request->id);
        if (!$img) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $deleted = $this->deleteImageFiles($img->file_name);
        if ($deleted) {
            $img->delete();
        }
        return response()->json(['success' => $deleted]);
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
        $path = 'destinations/';
        $thumbPath = 'destinations/thumb/';
        $width = (int)CustomHelper::getSetting('DESTINATION_IMG_WIDTH') ?: 768;
        $height = (int)CustomHelper::getSetting('DESTINATION_IMG_HEIGHT') ?: 768;
        $thumbWidth = (int)CustomHelper::getSetting('DESTINATION_IMG_THUMB_WIDTH') ?: 336;
        $thumbHeight = (int)CustomHelper::getSetting('DESTINATION_IMG_THUMB_HEIGHT') ?: 336;
        $mediumPath = 'destinations/medium/';
        $mediumWidth = (int)CustomHelper::getSetting('DESTINATION_IMG_MEDIUM_WIDTH') ?: 450;
        $mediumHeight = (int)CustomHelper::getSetting('DESTINATION_IMG_MEDIUM_HEIGHT') ?: 450;
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
        $fullPath = 'destinations/' . $uploaded['file_name'];
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
            'preview_url' => asset('storage/destinations/thumb/' . $uploaded['file_name']),
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

    public function infoIndex($id)
    {
        $item = Destination::findOrFail($id);
        $infos = DestinationInfo::where('destination_id', $id)->orderBy('sort_order')->paginate(20);
        return view('admin.destinations.info_index', compact('item', 'infos'));
    }

    public function infoCreate($id)
    {
        $item = Destination::findOrFail($id);
        $info = new DestinationInfo();
        return view('admin.destinations.info_form', compact('item', 'info'));
    }

    public function infoStore(Request $request, $id)
    {
        try {
            $item = Destination::findOrFail($id);
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'required|in:0,1'
            ]);
            $data['destination_id'] = $item->id;
            $info = DestinationInfo::create($data);
            CustomHelper::recordActionLog(
                url()->current(),
                'destination_infos',
                $info->id,
                'Create Destination Info',
                'Created destination info for: ' . $item->destination_name,
                json_encode($data)
            );
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.info.index', $item->id)->with('success', 'Additional info created');
        } catch (\Exception $e) {
            \Log::error('DestinationController@infoStore: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create additional info.');
        }
    }

    public function infoEdit($id, $infoId)
    {
        $item = Destination::findOrFail($id);
        $info = DestinationInfo::where('destination_id', $id)->findOrFail($infoId);
        return view('admin.destinations.info_form', compact('item', 'info'));
    }

    public function infoUpdate(Request $request, $id, $infoId)
    {
        try {
            $item = Destination::findOrFail($id);
            $info = DestinationInfo::where('destination_id', $id)->findOrFail($infoId);
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'required|in:0,1'
            ]);
            $info->fill($data)->save();
            CustomHelper::recordActionLog(
                url()->current(),
                'destination_infos',
                $info->id,
                'Update Destination Info',
                'Updated destination info for: ' . $item->destination_name,
                json_encode($data)
            );
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.info.index', $item->id)->with('success', 'Additional info updated');
        } catch (\Exception $e) {
            \Log::error('DestinationController@infoUpdate: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update additional info.');
        }
    }

    public function infoDestroy($id, $infoId)
    {
        try {
            $item = Destination::findOrFail($id);
            $info = DestinationInfo::where('destination_id', $id)->findOrFail($infoId);
            $info->delete();
            CustomHelper::recordActionLog(
                url()->current(),
                'destination_infos',
                $infoId,
                'Delete Destination Info',
                'Deleted destination info for: ' . $item->destination_name,
                ''
            );
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.info.index', $item->id)->with('success', 'Additional info deleted');
        } catch (\Exception $e) {
            \Log::error('DestinationController@infoDestroy: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete additional info.');
        }
    }

    public function typesIndex(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = DestinationType::query()->orderByDesc('id');
                if ($request->filled('status') && $request->status !== '') {
                    $query->where('status', (int)$request->status);
                }
                return DataTables::of($query)
                    ->addColumn('status_badge', function ($row) {
                        $cls = $row->status ? 'bg-label-primary' : 'bg-label-danger';
                        $txt = $row->status ? 'Active' : 'Inactive';
                        return '<span class="badge ' . $cls . '">' . $txt . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $routeName = CustomHelper::getAdminRouteName();
                        $actions = '';
                        $has = false;
                        if (auth()->user()->hasRole('SuperAdmin') ||
                            auth()->user()->can('destinations.edit') ||
                            auth()->user()->can('destinations.delete')) {
                            $editUrl = route($routeName . '.destinations.types.edit', $row->id);
                            $deleteUrl = route($routeName . '.destinations.types.destroy', $row->id);
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('destinations.edit')) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '">
                                    <i class="icon-base ti tabler-edit me-1"></i> Edit
                                </a>';
                                $has = true;
                            }
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('destinations.delete')) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-destination-type" href="javascript:void(0);" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $has = true;
                            }
                            $actions .= '</div></div>';
                        }
                        return $has ? $actions : '';
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('DestinationController@typesIndex: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }
        return view('admin.destination_types.index');
    }

    public function typesCreate()
    {
        $item = new DestinationType();
        return view('admin.destination_types.form', compact('item'));
    }

    public function typesStore(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|in:0,1'
            ]);
            $type = DestinationType::create($data);
            CustomHelper::recordActionLog(
                url()->current(),
                'destination_types',
                $type->id,
                'Create Destination Type',
                'Created destination type: ' . $type->name,
                json_encode($data)
            );
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.types.index')->with('success', 'Destination type created');
        } catch (\Exception $e) {
            \Log::error('DestinationController@typesStore: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create destination type.');
        }
    }

    public function typesEdit($id)
    {
        $item = DestinationType::findOrFail($id);
        return view('admin.destination_types.form', compact('item'));
    }

    public function typesUpdate(Request $request, $id)
    {
        try {
            $item = DestinationType::findOrFail($id);
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|in:0,1'
            ]);
            $item->fill($data)->save();
            CustomHelper::recordActionLog(
                url()->current(),
                'destination_types',
                $item->id,
                'Update Destination Type',
                'Updated destination type: ' . $item->name,
                json_encode($data)
            );
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.types.index')->with('success', 'Destination type updated');
        } catch (\Exception $e) {
            \Log::error('DestinationController@typesUpdate: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update destination type.');
        }
    }

    public function typesDestroy(Request $request, $id)
    {
        try {
            $item = DestinationType::findOrFail($id);
            $item->delete();
            CustomHelper::recordActionLog(
                url()->current(),
                'destination_types',
                $id,
                'Delete Destination Type',
                'Deleted destination type: ' . ($item->name ?? ''),
                ''
            );
            if ($request->ajax()) {
                return response()->json(['status' => true, 'message' => 'Destination type deleted']);
            }
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.destinations.types.index')->with('success', 'Destination type deleted');
        } catch (\Exception $e) {
            \Log::error('DestinationController@typesDestroy: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['status' => false, 'message' => 'Failed to delete destination type'], 500);
            }
            return back()->with('error', 'Failed to delete destination type.');
        }
    }
}
