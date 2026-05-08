<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index()
    {
        $items = Service::orderBy('sort_order')->orderByDesc('id')->get();
        return view('admin.services.index', compact('items'));
    }

    public function create()
    {
        $item = new Service();
        return view('admin.services.form', compact('item'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'brief' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
            'status' => 'required|in:0,1',
            'featured' => 'nullable|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['slug'] = CustomHelper::GetSlug('services', 'id', 0, $data['slug'] ?: $data['title']);
        $data['featured'] = $request->input('featured', 0) ? 1 : 0;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('uploads/services', "$filename.$extension", 'public');
        }

        $item = Service::create($data);

        CustomHelper::recordActionLog(
            url()->current(),
            'services',
            $item->id,
            'Create Service',
            'Created service: ' . $item->title,
            json_encode($data)
        );

        return redirect()->route($this->ADMIN_ROUTE_NAME . '.services.index')->with('success', 'Service created successfully');
    }

    public function edit($id)
    {
        $item = Service::findOrFail($id);
        return view('admin.services.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Service::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'brief' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
            'status' => 'required|in:0,1',
            'featured' => 'nullable|in:0,1',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (!empty($data['slug']) && $data['slug'] !== $item->slug) {
            $data['slug'] = CustomHelper::GetSlug('services', 'id', $id, $data['slug']);
        }

        $data['featured'] = $request->input('featured', 0) ? 1 : 0;

        if ($request->hasFile('image')) {
            /*
            // STOPPING physical deletion
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            */
            $file = $request->file('image');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $extension = $file->getClientOriginalExtension();
            $data['image'] = $file->storeAs('uploads/services', "$filename.$extension", 'public');
        }

        $item->fill($data)->save();

        CustomHelper::recordActionLog(
            url()->current(),
            'services',
            $item->id,
            'Update Service',
            'Updated service: ' . $item->title,
            json_encode($data)
        );

        return redirect()->route($this->ADMIN_ROUTE_NAME . '.services.index')->with('success', 'Service updated successfully');
    }

    public function destroy($id)
    {
        $item = Service::findOrFail($id);

        /*
        // STOPPING physical deletion
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }
        */
        $item->delete();

        CustomHelper::recordActionLog(
            url()->current(),
            'services',
            $id,
            'Delete Service',
            'Deleted service: ' . ($item->title ?? ''),
            ''
        );

        return redirect()->route($this->ADMIN_ROUTE_NAME . '.services.index')->with('success', 'Service deleted successfully');
    }

    public function deleteImage(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        $item = Service::findOrFail($request->id);
        $deleted = false;
        /*
        // STOPPING physical deletion
        if ($item->image && Storage::disk('public')->exists($item->image)) {
            $deleted = Storage::disk('public')->delete($item->image);
        }
        */
        $deleted = true; // Assume deleted for DB update
        if ($deleted) {
            $item->image = null;
            $item->save();
        }
        return response()->json(['success' => $deleted]);
    }
}

