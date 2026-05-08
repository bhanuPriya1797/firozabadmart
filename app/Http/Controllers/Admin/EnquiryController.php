<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enquiry;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class EnquiryController extends Controller
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
                $query = Enquiry::select('id', 'name', 'contact_email', 'phone', 'country', 'ip_address', 'is_read', 'created_at')->orderByDesc('id');

                return DataTables::of($query)
                    ->addColumn('email', function ($row) {
                        return $row->contact_email;
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $routeName = CustomHelper::getAdminRouteName();
                        $viewUrl = route($routeName . '.enquiries.view', ['id' => $row->id]);
                        $deleteUrl = route($routeName . '.enquiries.delete', ['id' => $row->id]);
                        $markUrl = route($routeName . '.enquiries.mark_read');

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('enquiries.view')) || 
                            (auth()->user()->can('enquiries.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // View permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('enquiries.view'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect" 
                                    href="' . $viewUrl . '"
                                >
                                    <i class="icon-base ti tabler-eye me-1"></i> View
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('enquiries.delete'))) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-enquiry"
                                    href="javascript:void(0);"
                                    data-url="' . $deleteUrl . '"
                                >
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }

                            $actions .= '<a class="dropdown-item waves-effect btn-mark-read" href="javascript:void(0);" data-url="' . $markUrl . '" data-id="' . $row->id . '" data-read="' . ($row->is_read ? 0 : 1) . '"><i class="icon-base ti ' . ($row->is_read ? 'tabler-eye-off' : 'tabler-eye') . ' me-1"></i> ' . ($row->is_read ? 'Mark Unread' : 'Mark Read') . '</a>';
                            $actions .= '</div></div>';
                        }

                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('EnquiryController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        $data['page_title'] = 'Enquiries';
        return view('admin.enquiries.index', $data);
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $enquiry = Enquiry::findOrFail($id);
            if ($enquiry->is_read == 0) {
                $enquiry->is_read = 1;
                $enquiry->save();
            }

            return view('admin.enquiries.view', [
                'page_heading' => 'Enquiry Details',
                'enquiry' => $enquiry,
            ]);
        } catch (\Exception $e) {
            Log::error('EnquiryController@view: ' . $e->getMessage());
            return redirect()->route($this->ADMIN_ROUTE_NAME . '.enquiries.index')->with('error', 'Enquiry not found.');
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = $request->id;
            $enquiry = Enquiry::findOrFail($id);
            $enquiry->delete();

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'enquiries',
                $id,
                'Delete Enquiry',
                'Deleted enquiry from: ' . $enquiry->name,
                'Deleted enquiry from: ' . $enquiry->name . ' (ID: ' . $id . ')'
            );

            return response()->json([
                'status' => true,
                'message' => 'Enquiry deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('EnquiryController@delete: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete enquiry.'
            ], 500);
        }
    }

    public function markRead(Request $request)
    {
        try {
            $id = (int)$request->input('id');
            $read = (int)$request->input('read', 1);
            $enquiry = Enquiry::findOrFail($id);
            $enquiry->is_read = $read ? 1 : 0;
            $enquiry->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            Log::error('EnquiryController@markRead: ' . $e->getMessage());
            return response()->json(['status' => false], 500);
        }
    }
}
