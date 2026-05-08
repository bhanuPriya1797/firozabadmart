<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VolunteerApplication;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class VolunteerApplicationController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    /**
     * Display a listing of volunteer applications
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Debug: Log the request data
                Log::info('VolunteerApplication AJAX Request:', [
                    'status' => $request->status,
                    'has_status' => $request->has('status'),
                    'all_data' => $request->all()
                ]);

                $query = VolunteerApplication::select('id', 'first_name', 'last_name', 'email', 'phone', 'interests', 'availability', 'hours', 'status', 'ip_address', 'created_at')->orderByDesc('id');

                // Filter by status
                if ($request->has('status') && $request->status !== '' && $request->status !== null) {
                    $query->where('status', $request->status);
                }

                return DataTables::of($query)
                    ->addColumn('full_name', function ($row) {
                        return $row->first_name . ' ' . $row->last_name;
                    })
                    ->addColumn('interests_display', function ($row) {
                        if (is_array($row->interests)) {
                            return implode(', ', $row->interests);
                        }
                        return $row->interests ?? 'N/A';
                    })
                    ->addColumn('availability_display', function ($row) {
                        return $row->availability . ' (' . $row->hours . ')';
                    })
                    ->addColumn('status_badge', function ($row) {
                        if ($row->status == 'pending') {
                            return '<span class="badge bg-warning">Pending</span>';
                        } elseif ($row->status == 'approved') {
                            return '<span class="badge bg-success">Approved</span>';
                        } else {
                            return '<span class="badge bg-danger">Rejected</span>';
                        }
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.volunteer-applications.show', ['id' => $row->id]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.volunteer-applications.destroy', ['id' => $row->id]);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('volunteer_applications.view')) || 
                            (auth()->user()->can('volunteer_applications.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // View permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('volunteer_applications.view'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect" 
                                    href="' . $viewUrl . '"
                                >
                                    <i class="icon-base ti tabler-eye me-1"></i> View
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('volunteer_applications.delete'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-volunteer"
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
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('VolunteerApplicationController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        $data['page_title'] = 'Volunteer Applications';
        return view('admin.volunteer-applications.index', $data);
    }

    /**
     * Display the specified volunteer application
     */
    public function show($id)
    {
        $application = VolunteerApplication::findOrFail($id);
        return view('admin.volunteer-applications.show', compact('application'));
    }

    /**
     * Update the status of a volunteer application
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $application = VolunteerApplication::findOrFail($id);
            $application->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'volunteer_applications',
                $id,
                'Update Volunteer Application Status',
                'Updated volunteer application status to: ' . $request->status,
                json_encode($request->all())
            );

            return redirect()->back()->with('success', 'Volunteer application status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating volunteer application status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating volunteer application status.');
        }
    }

    /**
     * Remove the specified volunteer application
     */
    public function destroy($id)
    {
        try {
            $application = VolunteerApplication::findOrFail($id);
            $application->delete();

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'volunteer_applications',
                $id,
                'Delete Volunteer Application',
                'Deleted volunteer application: ' . $application->name,
                'Deleted volunteer application: ' . $application->name . ' (ID: ' . $id . ')'
            );

            if (request()->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Volunteer application deleted successfully.'
                ]);
            }

            return redirect()->route($this->ADMIN_ROUTE_NAME . '.volunteer-applications.index')->with('success', 'Volunteer application deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting volunteer application: ' . $e->getMessage());
            
            if (request()->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error deleting volunteer application.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error deleting volunteer application.');
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total' => VolunteerApplication::count(),
            'pending' => VolunteerApplication::pending()->count(),
            'approved' => VolunteerApplication::approved()->count(),
            'rejected' => VolunteerApplication::rejected()->count(),
        ];

        return response()->json($stats);
    }
}
