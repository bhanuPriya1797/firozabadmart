<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\CustomHelper;

class AchievementController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Achievement::query();

            if ($request->filled('status') && $request->status !== '') {
                $query->where('status', (int) $request->status);
            }
            if ($request->filled('medal') && $request->medal !== '') {
                $query->where('medal', $request->medal);
            }
            if ($request->filled('category') && $request->category !== '') {
                $query->where('category', 'like', '%' . $request->category . '%');
            }
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('winner_name', 'like', "%{$s}%")
                      ->orWhere('event_name', 'like', "%{$s}%")
                      ->orWhere('location', 'like', "%{$s}%");
                });
            }

            return DataTables::of($query)
                ->addColumn('status_badge', function ($row) {
                    return $row->status
                        ? '<span class="badge bg-label-success">Active</span>'
                        : '<span class="badge bg-label-danger">Inactive</span>';
                })
                ->addColumn('medal_badge', function ($row) {
                    $map = [
                        'gold' => 'warning',
                        'silver' => 'secondary',
                        'bronze' => 'brown',
                    ];
                    $label = ucfirst($row->medal ?: 'N/A');
                    $class = 'bg-label-' . ($map[$row->medal] ?? 'info');
                    return '<span class="badge ' . $class . '">' . e($label) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $id = CustomHelper::encrypt($row->id);
                    $editUrl = route($this->ADMIN_ROUTE_NAME . '.achievements.edit', $id);
                    $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.achievements.destroy', $id);
                    $actions = '<div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="icon-base ti tabler-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="' . $editUrl . '">
                                <i class="icon-base ti tabler-pencil me-1"></i> Edit
                            </a>
                            <a class="dropdown-item text-danger btn-delete" href="javascript:void(0);" data-url="' . $deleteUrl . '">
                                <i class="icon-base ti tabler-trash me-1"></i> Delete
                            </a>
                        </div>
                    </div>';
                    return $actions;
                })
                ->rawColumns(['status_badge', 'medal_badge', 'action'])
                ->make(true);
        }

        return view('admin.achievements.index', [
            'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME,
        ]);
    }

    public function create()
    {
        $achievement = new Achievement();
        $page_heading = 'Add Achievement';
        return view('admin.achievements.form', compact('achievement', 'page_heading'));
    }

    public function edit($id)
    {
        $id = CustomHelper::decrypt($id);
        $achievement = Achievement::findOrFail($id);
        $page_heading = 'Edit Achievement';
        return view('admin.achievements.form', compact('achievement', 'page_heading'));
    }

    public function store(Request $request)
    {
        return $this->save($request);
    }

    public function update(Request $request, $id)
    {
        $id = CustomHelper::decrypt($id);
        return $this->save($request, $id);
    }

    private function save(Request $request, $id = null)
    {
        $rules = [
            'winner_name' => 'required|string|max:255',
            'medal' => 'nullable|string|max:50',
            'event_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'nullable|in:0,1',
        ];
        $validated = $request->validate($rules);
        $validated['status'] = isset($validated['status']) ? (int) $validated['status'] : 0;

        if ($id) {
            $achievement = Achievement::findOrFail($id);
            $achievement->update($validated);
            $msg = 'Achievement updated successfully.';
        } else {
            $achievement = Achievement::create($validated);
            $msg = 'Achievement created successfully.';
        }

        return redirect()->route($this->ADMIN_ROUTE_NAME . '.achievements.index')->with('success', $msg);
    }

    public function destroy($id)
    {
        $id = CustomHelper::decrypt($id);
        $achievement = Achievement::findOrFail($id);
        $achievement->delete();
        return response()->json(['success' => true, 'message' => 'Achievement deleted successfully.']);
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'status' => 'required|in:0,1',
        ]);
        $achievement = Achievement::findOrFail($request->id);
        $achievement->status = (int) $request->status;
        $achievement->save();
        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
