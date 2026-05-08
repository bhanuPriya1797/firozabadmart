<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Circular;
use App\Helpers\CustomHelper;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Validator;

class CircularController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Circular::select('*')->orderBy('sort_order','asc')->orderBy('id','desc');
            return DataTables::of($data)
                ->addColumn('status_badge', function ($row) {
                    return $row->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
                })
                ->addColumn('featured_badge', function ($row) {
                    return $row->featured ? '<span class="badge bg-warning">Featured</span>' : '<span class="badge bg-light text-muted">Normal</span>';
                })
                ->addColumn('actions', function ($row) {
                    $eid = CustomHelper::encrypt($row->id);
                    $editUrl = route($this->ADMIN_ROUTE_NAME . '.circulars.edit', $eid);
                    $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.circulars.delete', $eid);
                    $btn = '<div class="d-flex gap-2">';
                    if (auth()->user()->can('circulars.edit')) {
                        $btn .= '<a href="'.$editUrl.'" class="btn btn-primary btn-sm"><i class="ti tabler-edit"></i></a>';
                    }
                    if (auth()->user()->can('circulars.delete')) {
                        $btn .= '<button type="button" onclick="deleteCircular(\''.$deleteUrl.'\')" class="btn btn-danger btn-sm"><i class="ti tabler-trash"></i></button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge','featured_badge','actions'])
                ->make(true);
        }
        return view('admin.circulars.index');
    }

    public function add()
    {
        $page_heading = 'Add Circular';
        return view('admin.circulars.form', compact('page_heading'));
    }

    public function edit($id)
    {
        $id = CustomHelper::decrypt($id);
        $circular = Circular::findOrFail($id);
        $page_heading = 'Edit Circular';
        return view('admin.circulars.form', compact('circular','page_heading'));
    }

    public function save(Request $request, $id = null)
    {
        if ($id) {
            $id = CustomHelper::decrypt($id);
        }

        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|string|max:255',
            'banner_image' => 'nullable|string|max:255',
            'document_path' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'title','slug','description','image','banner_image','document_path','status','sort_order'
        ]);

        $data['status'] = !empty($data['status']) ? 1 : 0;
        $data['featured'] = $request->boolean('featured');

        // SEO pack
        $seo = [];
        if ($request->filled('meta_title')) { $seo['meta_title'] = $request->input('meta_title'); }
        if ($request->filled('meta_keywords')) { $seo['meta_keywords'] = $request->input('meta_keywords'); }
        if ($request->filled('meta_description')) { $seo['meta_description'] = $request->input('meta_description'); }
        if (!empty($seo)) { $data['seo'] = $seo; }

        // Generate unique slug
        $slugSource = $data['slug'] ?: $data['title'];
        $data['slug'] = CustomHelper::GetSlug('circulars', 'id', $id ?: '', $slugSource);

        if ($id) {
            $circular = Circular::findOrFail($id);
            $circular->update($data);
            $msg = 'Circular updated successfully.';
        } else {
            $data['created_by'] = auth()->id() ?: null;
            $circular = Circular::create($data);
            $msg = 'Circular created successfully.';
        }

        return redirect()->route($this->ADMIN_ROUTE_NAME . '.circulars.index')->with('success', $msg);
    }

    public function delete(Request $request, $id)
    {
        $id = CustomHelper::decrypt($id);
        $circular = Circular::find($id);
        if (!$circular) {
            return back()->with('error', 'Circular not found.');
        }
        $circular->delete();
        return back()->with('success', 'Circular deleted successfully.');
    }
}
