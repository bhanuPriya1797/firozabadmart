<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Carbon;

class ActivityLogController extends Controller
{
    protected $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    /**
     * Show the activity logs page or return DataTables JSON.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = ActivityLog::query();

                if ($request->filled('action_type')) {
                    $query->where('action_type', 'like', '%' . $request->action_type . '%');
                }

                return DataTables::of($query->orderByDesc('created_at'))
                    ->addColumn('action_date', function ($row) {
                        return Carbon::parse($row->created_at)->format('d M Y, h:i A');
                    })
                    ->addColumn('action_by', function ($row) {
                        return $row->activityLogadmin?->name ?? 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $viewUrl = route($this->ADMIN_ROUTE_NAME.'.activities.view', ['id' => $encryptedId]);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('activities.details'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // View permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('activities.details'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $viewUrl . '">
                                    <i class="icon-base ti tabler-eye me-1"></i> View
                                </a>';
                                $hasAnyAction = true;
                            }

                            $actions .= '</div></div>';
                        }

                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('admin.activity_logs.index');
        } catch (\Exception $e) {
            return back()->with('error', 'Error fetching activity logs: ' . $e->getMessage());
        }
    }

    /**
     * View a specific activity log.
     */
    public function view(Request $request)
    {
        try {
            $id = (int) CustomHelper::decrypt($request->id);
            $activity = null;

            if ($id > 0) {
                $activity = ActivityLog::find($id);
            }

            return view('admin.activity_logs.view', [
                'page_heading' => 'Activity Log',
                'activity' => $activity,
                'id' => $id,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Error loading activity log: ' . $e->getMessage());
        }
    }
}
