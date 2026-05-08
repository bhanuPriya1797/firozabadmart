<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentNote;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StudentQueryController extends Controller
{
    /**
     * Display a listing of student queries.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $queries = StudentNote::with('assignedUser')
                ->select(['id', 'student_name', 'email', 'phone', 'note_message', 'assigned_to', 'status', 'created_at']);
            
            // Filter by status - by default show only pending and processing
            $showCompleted = $request->get('show_completed', false);
            if (!$showCompleted) {
                $queries->whereIn('status', ['pending', 'processing']);
            }
            
            $queries->orderBy('created_at', 'desc');

            return DataTables::of($queries)
                ->addIndexColumn()
                ->addColumn('assigned_user', function ($row) {
                    return $row->assignedUser ? $row->assignedUser->name : 'Not Assigned';
                })
                ->addColumn('note_preview', function ($row) {
                    return \Str::limit($row->note_message, 50);
                })
                ->addColumn('status', function ($row) {
                    $statusClass = '';
                    $statusText = ucfirst($row->status ?? 'pending');
                    
                    switch ($row->status) {
                        case 'pending':
                            $statusClass = 'badge bg-warning';
                            break;
                        case 'processing':
                            $statusClass = 'badge bg-info';
                            break;
                        case 'completed':
                            $statusClass = 'badge bg-success';
                            break;
                        default:
                            $statusClass = 'badge bg-secondary';
                    }
                    
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->addColumn('created_date', function ($row) {
                    return $row->created_at->format('M d, Y H:i');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    
                    // View button
                    $btn .= '<button type="button" class="btn btn-sm btn-info" onclick="viewQuery(' . $row->id . ')" title="View Query">';
                    $btn .= '<i class="ti tabler-eye"></i>';
                    $btn .= '</button>';
                    
                    // Assign user button
                    $btn .= '<button type="button" class="btn btn-sm btn-warning" onclick="assignUser(' . $row->id . ', \'' . addslashes(\Str::limit($row->note_message, 30)) . '\')" title="Assign User">';
                    $btn .= '<i class="ti tabler-user-plus"></i>';
                    $btn .= '</button>';
                    
                    // Update status button
                    $btn .= '<button type="button" class="btn btn-sm btn-success" onclick="updateStatus(' . $row->id . ', \'' . addslashes(\Str::limit($row->note_message, 30)) . '\', \'' . ($row->status ?? 'pending') . '\')" title="Update Status">';
                    $btn .= '<i class="ti tabler-edit"></i>';
                    $btn .= '</button>';
                    
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.student-queries.index');
    }

    /**
     * Get a specific student query for viewing.
     */
    public function show($id)
    {
        $query = StudentNote::with('assignedUser')->findOrFail($id);
        $users = User::where('status', 1)->get();
        
        return response()->json([
            'success' => true,
            'query' => $query,
            'users' => $users
        ]);
    }

    /**
     * Get pending queries count for sidebar
     */
    public function getPendingCount()
    {
        $pendingCount = StudentNote::where('status', 'pending')->count();
        
        return response()->json([
            'success' => true,
            'count' => $pendingCount
        ]);
    }

    /**
     * Assign a user to a student query.
     */
    public function assignUser(Request $request)
    {
        try {
            $request->validate([
                'query_id' => 'required|exists:student_notes,id',
                'user_id' => 'required|exists:users,id'
            ]);

            $query = StudentNote::findOrFail($request->query_id);
            $query->assigned_to = $request->user_id;
            $query->save();

            return response()->json([
                'success' => true,
                'message' => 'User assigned successfully to the query.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign user. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the status of a student query.
     */
    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'query_id' => 'required|exists:student_notes,id',
                'status' => 'required|in:pending,processing,completed'
            ]);

            $query = StudentNote::findOrFail($request->query_id);
            $query->status = $request->status;
            $query->save();

            return response()->json([
                'success' => true,
                'message' => 'Query status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status. Please try again.'
            ], 500);
        }
    }

    /**
     * Get all users for assignment dropdown.
     */
    public function getUsers()
    {
        try {
            $users = \App\Models\User::select('id', 'name', 'email')
                ->where('status', 1)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load users.'
            ], 500);
        }
    }
}