<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archer;
use App\Models\ArcherApplication;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ArcherExport;

class ArcherController extends Controller
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
                $appsSub = DB::table('archer_applications as aa')
                    ->select('aa.archer_id', 'aa.status')
                    ->join(DB::raw('(SELECT archer_id, MAX(id) AS last_id FROM archer_applications GROUP BY archer_id) AS t'), 'aa.id', '=', 't.last_id');

                $query = Archer::query()
                    ->leftJoinSub($appsSub, 'apps', function ($join) {
                        $join->on('apps.archer_id', '=', 'archers.id');
                    })
                    ->select('archers.*', 'apps.status as application_status')
                    ->latest('archers.id');

                if ($request->filled('app_status') && $request->app_status !== '') {
                    $status = $request->app_status;
                    if ($status === 'affiliated') {
                        $status = 'approved';
                    }
                    $query->where('apps.status', $status);
                }

                if ($request->filled('status') && $request->status !== '') {
                    $query->where('status', (int)$request->status);
                }

                if ($request->filled('gender') && $request->gender !== '') {
                    $query->where('gender', $request->gender);
                }

                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('surname', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
                }

                return DataTables::of($query)
                    ->addColumn('checkbox', function ($row) {
                        return '<input type="checkbox" class="form-check-input archer-checkbox" value="' . $row->id . '">';
                    })
                    ->addColumn('name', function ($row) {
                        return trim($row->first_name . ' ' . $row->surname);
                    })
                    ->addColumn('contact', function ($row) {
                        return '<div>' . e($row->phone) . '</div><div class="text-muted small">' . e($row->email) . '</div>';
                    })
                    ->addColumn('app_status', function ($row) {
                        if (!$row->application_status) {
                            return '-';
                        }
                        $map = [
                            'approved' => 'Affiliated',
                            'rejected' => 'Rejected',
                            'pending' => 'Pending',
                            're_evaluate' => 'Re-evaluate',
                            'renewal_pending' => 'Renewal Pending',
                        ];
                        return $map[$row->application_status] ?? ucfirst(str_replace('_', ' ', $row->application_status));
                    })
                    ->addColumn('status', function ($row) {
                        $checked = $row->status ? 'checked' : '';
                        return '<label class="switch switch-primary mb-0">
                                    <input type="checkbox" class="switch-input archer-status-toggle" data-id="' . $row->id . '" ' . $checked . '>
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                </label>';
                    })
                    ->addColumn('created_at_formatted', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.archers.show', $encryptedId);
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.archers.edit', $encryptedId);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.archers.destroy', $encryptedId);

                        $actions = '';
                        $hasAnyAction = false;

                        if (
                            auth()->user()->hasRole('SuperAdmin') ||
                            auth()->user()->can('archers.view') ||
                            auth()->user()->can('archers.edit') ||
                            auth()->user()->can('archers.delete')
                        ) {
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('archers.view')) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $viewUrl . '">
                                    <i class="icon-base ti tabler-eye me-1"></i> View
                                </a>';
                                $hasAnyAction = true;
                            }

                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('archers.edit')) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '">
                                    <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;

                                $actions .= '<div class="dropdown-divider"></div>';
                                $actions .= '<a class="dropdown-item waves-effect btn-update-app-status" href="#" data-id="' . $row->id . '" data-status="approved" data-category="' . e($row->category) . '" data-member_id="' . e($row->member_id) . '" data-member_association="' . e($row->member_association) . '">
                                    <i class="icon-base ti tabler-check me-1"></i> Affiliate
                                </a>';
                                $actions .= '<a class="dropdown-item waves-effect btn-update-app-status" href="#" data-id="' . $row->id . '" data-status="pending">
                                    <i class="icon-base ti tabler-clock me-1"></i> Mark Pending
                                </a>';
                                $actions .= '<a class="dropdown-item waves-effect btn-update-app-status" href="#" data-id="' . $row->id . '" data-status="rejected">
                                    <i class="icon-base ti tabler-x me-1"></i> Reject
                                </a>';
                                $actions .= '<a class="dropdown-item waves-effect btn-update-app-status" href="#" data-id="' . $row->id . '" data-status="re_evaluate">
                                    <i class="icon-base ti tabler-refresh me-1"></i> Re-evaluate
                                </a>';
                                $actions .= '<a class="dropdown-item waves-effect btn-update-app-status" href="#" data-id="' . $row->id . '" data-status="renewal_pending">
                                    <i class="icon-base ti tabler-alert-circle me-1"></i> Renewal Pending
                                </a>';
                            }

                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('archers.delete')) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-archer" href="javascript:void(0);" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }

                            $actions .= '</div></div>';
                        }

                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['checkbox', 'contact', 'status', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('ArcherController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        return view('admin.archers.index', [
            'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME,
        ]);
    }

    public function show($id)
    {
        $id = CustomHelper::decrypt($id);
        $archer = Archer::findOrFail($id);
        return view('admin.archers.show', compact('archer'));
    }

    public function edit($id)
    {
        $id = CustomHelper::decrypt($id);
        $archer = Archer::findOrFail($id);
        return view('admin.archers.form', compact('archer'));
    }

    public function update(Request $request, $id)
    {
        $id = CustomHelper::decrypt($id);
        $archer = Archer::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:archers,email,' . $archer->id,
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'required|date',
            'marital_status' => 'required|in:Married,Single',
            'is_minor' => 'nullable|boolean',
            'category' => 'nullable|string|max:100',
            'member_id' => 'nullable|string|max:100',
            'member_association' => 'nullable|string|max:255',
            'aadhar_card_number' => 'required|string|max:50',
            'aadhar_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => 'required|in:0,1',
        ]);

        $data['is_minor'] = $request->boolean('is_minor');

        if ($request->hasFile('aadhar_document')) {
            if ($archer->aadhar_document && Storage::disk('public')->exists($archer->aadhar_document)) {
                Storage::disk('public')->delete($archer->aadhar_document);
            }
            $file = $request->file('aadhar_document');
            $path = $file->store('uploads/archers/aadhar', 'public');
            $data['aadhar_document'] = $path;
        }

        $archer->update($data);

        return redirect()->route($this->ADMIN_ROUTE_NAME . '.archers.index')
            ->with('success', 'Archer updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        try {
            $id = CustomHelper::decrypt($id);
            $archer = Archer::findOrFail($id);
            $archer->delete();

            return response()->json(['success' => true, 'message' => 'Archer deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('ArcherController@destroy: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete archer.'], 500);
        }
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'status' => 'required|in:0,1',
        ]);

        Archer::whereIn('id', $request->ids)->update(['status' => (int)$request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        Archer::whereIn('id', $request->ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected archers deleted successfully.']);
    }

    public function toggleStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:archers,id',
            'status' => 'required|in:0,1',
        ]);

        Archer::where('id', $request->id)->update(['status' => (int)$request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function export(Request $request)
    {
        try {
            $fileName = 'archers_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            $filters = [
                'status' => $request->status,
                'search' => $request->search,
                'gender' => $request->gender,
                'app_status' => $request->app_status,
            ];

            return Excel::download(new ArcherExport($filters), $fileName);
        } catch (\Exception $e) {
            Log::error('ArcherController@export: ' . $e->getMessage());
            return back()->with('error', 'Export failed.');
        }
    }

    public function updateAppStatus(Request $request)
    {
        $request->validate([
            'archer_id' => 'required|integer|exists:archers,id',
            'status' => 'required|in:pending,approved,rejected,re_evaluate,renewal_pending',
            'category' => 'nullable|string|max:100',
            'member_id' => 'nullable|string|max:100',
            'member_association' => 'nullable|string|max:255',
        ]);

        try {
            $archerId = (int) $request->archer_id;
            $status = $request->status;
            if ($status === 'approved') {
                if (!$request->filled('category') || !$request->filled('member_id') || !$request->filled('member_association')) {
                    return response()->json(['success' => false, 'message' => 'Category, Member ID and Member Association are required for affiliation.'], 422);
                }
            }
            if ($request->filled('category') || $request->filled('member_id') || $request->filled('member_association')) {
                Archer::where('id', $archerId)->update([
                    'category' => $request->category,
                    'member_id' => $request->member_id,
                    'member_association' => $request->member_association,
                ]);
            }
            $application = ArcherApplication::where('archer_id', $archerId)->orderByDesc('id')->first();
            if ($application) {
                $application->status = $status;
                $application->save();
            } else {
                ArcherApplication::create([
                    'archer_id' => $archerId,
                    'status' => $status,
                    'submitted_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Application status updated.']);
        } catch (\Throwable $e) {
            Log::error('ArcherController@updateAppStatus: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update application status.'], 500);
        }
    }
}

