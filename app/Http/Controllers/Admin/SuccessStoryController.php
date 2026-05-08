<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\CustomHelper;

class SuccessStoryController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $stories = SuccessStory::query()->latest();

                if ($request->filled('status')) {
                    $stories->where('status', $request->status);
                }
                
                if ($request->filled('featured')) {
                    $stories->where('featured', $request->featured);
                }

                return DataTables::of($stories)
                    ->addColumn('image', function ($row) {
                        if ($row->image) {
                            return '<img src="' . $row->image_url . '" alt="' . $row->title . '" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">';
                        }
                        return '<div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;"><i class="fas fa-image text-muted"></i></div>';
                    })
                    ->addColumn('featured_status', function ($row) {
                        return $row->featured 
                            ? '<span class="badge bg-label-success">Featured</span>' 
                            : '<span class="badge bg-label-secondary">Regular</span>';
                    })
                    ->addColumn('status_label', function ($row) {
                        return $row->status 
                            ? '<span class="badge bg-label-primary">Active</span>' 
                            : '<span class="badge bg-label-danger">Inactive</span>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.success-stories.edit', $row->encrypted_id);
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.success-stories.show', $row->encrypted_id);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.success-stories.destroy', $row->encrypted_id);
                        
                        $actions = '';
                        $hasAnyAction = false;

                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('success_stories.view')) || 
                            (auth()->user()->can('success_stories.edit')) || 
                            (auth()->user()->can('success_stories.delete'))) {
                            
                            $actions = '<div class="btn-group" role="group">';

                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('success_stories.view'))) {
                                $actions .= '<a href="' . $viewUrl . '" class="btn btn-label-info btn-sm" title="View">
                                    <i class="ti tabler-eye"></i>
                                </a>';
                                $hasAnyAction = true;
                            }

                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('success_stories.edit'))) {
                                $actions .= '<a href="' . $editUrl . '" class="btn btn-label-primary btn-sm" title="Edit">
                                    <i class="ti tabler-edit"></i>
                                </a>';
                                $hasAnyAction = true;
                            }

                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('success_stories.delete'))) {
                                $actions .= '<button type="button" class="btn btn-label-danger btn-sm btn-delete-story" data-url="' . $deleteUrl . '" title="Delete">
                                    <i class="ti tabler-trash"></i>
                                </button>';
                                $hasAnyAction = true;
                            }

                            $actions .= '</div>';
                        }

                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['image', 'featured_status', 'status_label', 'action'])
                    ->make(true);
            } catch (\Throwable $e) {
                \Log::error('SuccessStoryController@index: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return response()->json(['error' => 'Failed to load testimonials'], 500);
            }
        }

        return view('admin.success-stories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.success-stories.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'brief' => 'required|string|max:500',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'sort_order' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $request->all();
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('success-stories', $imageName, 'public');
                $data['image'] = $imagePath;
            }

            $data['featured'] = $request->has('featured');
            $data['status'] = $request->has('status');

            $successStory = SuccessStory::create($data);

            CustomHelper::recordActionLog(
                url()->current(),
                'success_stories',
                $successStory->id,
                'Create Success Story',
                'Created success story: ' . $successStory->title,
                json_encode($data)
            );

            return redirect()->route($this->ADMIN_ROUTE_NAME . '.success-stories.index')
                ->with('success', 'Success story created successfully.');
        } catch (\Throwable $e) {
            \Log::error('SuccessStoryController@store: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Failed to create testimonial. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $decryptedId = SuccessStory::decryptId($id);
            $story = SuccessStory::findOrFail($decryptedId);
            return view('admin.success-stories.show', compact('story'));
        } catch (\Throwable $e) {
            \Log::error('SuccessStoryController@show: ' . $e->getMessage());
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.success-stories.index')
                ->with('error', 'Unable to load testimonial.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $decryptedId = SuccessStory::decryptId($id);
            $story = SuccessStory::findOrFail($decryptedId);
            return view('admin.success-stories.form', compact('story'));
        } catch (\Throwable $e) {
            \Log::error('SuccessStoryController@edit: ' . $e->getMessage());
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.success-stories.index')
                ->with('error', 'Unable to load testimonial for editing.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $decryptedId = SuccessStory::decryptId($id);
            $story = SuccessStory::findOrFail($decryptedId);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'brief' => 'required|string|max:500',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'sort_order' => 'nullable|integer|min:0'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $request->only(['title','brief','description','sort_order']);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('success-stories', $imageName, 'public');
                $data['image'] = $imagePath;
            }

            $data['featured'] = $request->has('featured');
            $data['status'] = $request->has('status');

            $story->fill($data)->save();

            CustomHelper::recordActionLog(
                url()->current(),
                'success_stories',
                $decryptedId,
                'Update Success Story',
                'Updated success story: ' . $story->title,
                json_encode($data)
            );

            return redirect()->route($this->ADMIN_ROUTE_NAME . '.success-stories.index')
                ->with('success', 'Success story updated successfully.');
        } catch (\Throwable $e) {
            \Log::error('SuccessStoryController@update: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Failed to update testimonial. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $decryptedId = SuccessStory::decryptId($id);
            $story = SuccessStory::findOrFail($decryptedId);
            
            $story->delete();

            CustomHelper::recordActionLog(
                url()->current(),
                'success_stories',
                $decryptedId,
                'Delete Success Story',
                'Deleted success story: ' . $story->title,
                'Deleted success story: ' . $story->title . ' (ID: ' . $decryptedId . ')'
            );

            return response()->json(['success' => true, 'message' => 'Success story deleted successfully.']);
        } catch (\Throwable $e) {
            \Log::error('SuccessStoryController@destroy: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to delete success story.'], 500);
        }
    }
}
