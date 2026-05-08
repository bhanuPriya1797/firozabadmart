<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\TourLead;
use Illuminate\Http\Request;

class TourLeadController extends Controller
{
    public function index(Request $request)
    {
        $routeName = CustomHelper::getAdminRouteName();
        $leads = TourLead::with('tour')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.tour_leads.index', compact('leads', 'routeName'));
    }

    public function destroy($id)
    {
        try {
            $lead = TourLead::findOrFail($id);
            $lead->delete();
            CustomHelper::recordActionLog(
                url()->current(),
                'tour_leads',
                $id,
                'Delete Tour Lead',
                'Deleted tour lead: ' . ($lead->full_name ?? 'N/A'),
                json_encode(['email' => $lead->email ?? null, 'phone' => $lead->phone ?? null])
            );
            return back()->with('success', 'Lead deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('TourLeadController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete lead.');
        }
    }

    public function markRead(Request $request)
    {
        try {
            $id = (int)$request->input('id');
            $read = (int)$request->input('read', 1);
            $lead = TourLead::findOrFail($id);
            $lead->is_read = $read ? 1 : 0;
            $lead->save();
            CustomHelper::recordActionLog(
                url()->current(),
                'tour_leads',
                $lead->id,
                'Mark Lead Read',
                'Marked as ' . ($lead->is_read ? 'Read' : 'Unread'),
                json_encode(['read' => $read])
            );
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            \Log::error('TourLeadController@markRead: ' . $e->getMessage());
            return response()->json(['status' => false], 500);
        }
    }
}
