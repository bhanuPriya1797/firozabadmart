<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;
use App\Helpers\CustomHelper;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Exports\NewsletterExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NewsletterController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $newsletters = NewsletterSubscriber::select('id', 'email', 'created_at');

                return DataTables::of($newsletters)
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.newsletter.delete', $encryptedId);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (Gate::allows('newsletter.delete')) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // Delete permission
                            if (Gate::allows('newsletter.delete')) {
                                $actions .= '<a class="dropdown-item btn-delete-newsletter" href="javascript:void(0);" data-url="' . $deleteUrl . '" data-id="' . $row->id . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }

                            $actions .= '</div></div>';
                        }

                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->editColumn('created_at', fn($row) => Carbon::parse($row->created_at)->format('d, M Y h:i A'))
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('admin.newsletter.index');
        } catch (\Exception $e) {
            Log::error('NewsletterController@index: ' . $e->getMessage());
            return back()->with('error', 'Failed to load newsletter list.');
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $decryptedId = CustomHelper::decrypt($id);
            $newsletter = NewsletterSubscriber::findOrFail($decryptedId);

            $newsletter->delete();

            CustomHelper::recordActionLog(
                url()->current(),
                'newsletter_subscribers',
                $decryptedId,
                'Delete Newsletter Subscriber',
                "Deleted ID: $decryptedId",
                "Deleted newsletter subscriber with ID: $decryptedId"
            );

            return response()->json(['success' => true, 'msg' => 'Subscriber deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('NewsletterController@delete: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Deletion failed.']);
        }
    }

    public function exportXls(Request $request)
    {
        try {
            $fileName = 'newsletter_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new NewsletterExport, $fileName);
        } catch (\Exception $e) {
            Log::error('NewsletterController@exportXls: ' . $e->getMessage());
            return back()->with('error', 'Export failed.');
        }
    }
}
