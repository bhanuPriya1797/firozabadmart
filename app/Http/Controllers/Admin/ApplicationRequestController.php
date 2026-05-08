<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentApplicationStatusNotification;
use Illuminate\Support\Facades\Log;

class ApplicationRequestController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    /**
     * Display a listing of application requests.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = ApplicationRequest::with(['student', 'application', 'approvedBy'])
                    ->latest();

                // Apply filters
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->filled('request_type')) {
                    $query->where('request_type', $request->request_type);
                }
                if ($request->filled('start_date')) {
                    $query->whereDate('requested_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('requested_at', '<=', $request->end_date);
                }
                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->whereHas('student', function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('student_details', function ($row) {
                        $name = !empty($row->student->first_name) ? e($row->student->first_name).' '.e($row->student->surname) : 'N/A';
                        $email = !empty($row->student->email) ? e($row->student->email) : 'N/A';
                        $phone = !empty($row->student->contact_no) ? e($row->student->contact_no) : 'N/A';
                        return '
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex flex-column">
                                    <div><strong>Name:</strong> ' . $name . '</div>
                                    <div><strong>Email:</strong> ' . $email . '</div>
                                    <div><strong>Phone:</strong> ' . $phone . '</div>
                                </div>
                            </div>
                        ';
                    })
                    ->addColumn('request_type_badge', function ($row) {
                        return $row->request_type === 'recurring' 
                            ? '<span class="badge bg-warning text-dark">Recurring</span>'
                            : '<span class="badge bg-info">New</span>';
                    })
                    ->addColumn('status_badge', function ($row) {
                        switch ($row->status) {
                            case 'pending':
                                return '<span class="badge bg-warning text-dark">Pending</span>';
                            case 'approved':
                                return '<span class="badge bg-success">Approved</span>';
                            case 'rejected':
                                return '<span class="badge bg-danger">Rejected</span>';
                            default:
                                return '<span class="badge bg-secondary">Unknown</span>';
                        }
                    })
                    ->addColumn('requested_at_formatted', function ($row) {
                        return $row->requested_at ? $row->requested_at->format('d M Y h:i A') : '';
                    })
                    ->addColumn('approved_at_formatted', function ($row) {
                        return $row->approved_at ? $row->approved_at->format('d M Y h:i A') : 'N/A';
                    })
                    ->addColumn('approved_by_name', function ($row) {
                        return $row->approvedBy ? e($row->approvedBy->name) : 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $actions = '';
                        $hasAnyAction = false;
                        
                        // Check if user has permissions
                        if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('students.view') || auth()->user()->can('students.approve')) {
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            
                            // View details
                            if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('students.view')) {
                                $actions .= '<a class="dropdown-item waves-effect btn-view-request" href="javascript:void(0);" data-id="' . $encryptedId . '">
                                    <i class="icon-base ti tabler-eye me-1"></i> View Details
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Approve/Reject actions for pending requests
                            if ($row->status === 'pending' && (auth()->user()->hasRole('SuperAdmin') || auth()->user()->can('students.approve'))) {
                                $actions .= '<a class="dropdown-item waves-effect btn-approve-request" href="javascript:void(0);" data-id="' . $encryptedId . '">
                                    <i class="icon-base ti tabler-check me-1"></i> Approve
                                </a>';
                                $actions .= '<a class="dropdown-item waves-effect btn-reject-request" href="javascript:void(0);" data-id="' . $encryptedId . '">
                                    <i class="icon-base ti tabler-x me-1"></i> Reject
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            $actions .= '</div></div>';
                        }
                        
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['student_details', 'request_type_badge', 'status_badge', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('ApplicationRequestController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        return view('admin.application-requests.index', [
            'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME
        ]);
    }

    /**
     * Show the details of a specific application request.
     */
    public function show($id)
    {
        try {
            $id = CustomHelper::decrypt($id);
            $request = ApplicationRequest::with(['student', 'application', 'approvedBy'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $request->id,
                    'student' => [
                        'name' => $request->student->first_name . ' ' . $request->student->surname,
                        'email' => $request->student->email,
                        'contact_no' => $request->student->contact_no,
                        'family_lineage' => $request->student->family_lineage,
                        'gender' => $request->student->gender,
                    ],
                    'request_type' => $request->request_type,
                    'status' => $request->status,
                    'requested_at' => $request->requested_at->format('F j, Y \\a\\t g:i A'),
                    'approved_at' => $request->approved_at ? $request->approved_at->format('F j, Y \\a\\t g:i A') : null,
                    'approved_by' => $request->approvedBy ? $request->approvedBy->name : null,
                    'admin_notes' => $request->admin_notes,
                    'application' => $request->application ? [
                        'application_number' => $request->application->application_number,
                        'case_id' => $request->application->case_id,
                        'status_text' => $request->application->status_text,
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('ApplicationRequestController@show: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Request not found.'], 404);
        }
    }

    /**
     * Approve an application request.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $id = CustomHelper::decrypt($id);
            $applicationRequest = ApplicationRequest::with(['student', 'application'])->findOrFail($id);
            
            if ($applicationRequest->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'This request has already been processed.'], 400);
            }

            // Create a new application copy from the main application
            $this->createRecurringApplication($applicationRequest);

            $applicationRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'admin_notes' => $request->admin_notes
            ]);
            // Send email notification to student
            try {
                Mail::to($applicationRequest->student->email)->send(
                    new StudentApplicationStatusNotification(
                        $applicationRequest->student,
                        'approved',
                        $request->admin_notes,
                        $applicationRequest->request_type
                    )
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send approval email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Application request approved successfully, new application created, and notification sent to student.'
            ]);
        } catch (\Exception $e) {
            \Log::error('ApplicationRequestController@approve: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to approve request.'], 500);
        }
    }

    /**
     * Reject an application request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        try {
            $id = CustomHelper::decrypt($id);
            $applicationRequest = ApplicationRequest::with(['student', 'application'])->findOrFail($id);
            
            if ($applicationRequest->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'This request has already been processed.'], 400);
            }

            $applicationRequest->update([
                'status' => 'rejected',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'admin_notes' => $request->admin_notes
            ]);

            // Send email notification to student
            try {
                Mail::to($applicationRequest->student->email)->send(
                    new StudentApplicationStatusNotification(
                        $applicationRequest->student,
                        'rejected',
                        $request->admin_notes,
                        $applicationRequest->request_type
                    )
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send rejection email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Application request rejected and notification sent to student.'
            ]);
        } catch (\Exception $e) {
            \Log::error('ApplicationRequestController@reject: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to reject request.'], 500);
        }
    }

    /**
     * Create a recurring application copy from the main application
     */
    private function createRecurringApplication(ApplicationRequest $applicationRequest)
    {
        //prd($applicationRequest);
        $originalApplication = $applicationRequest->application;
        $student = $applicationRequest->student;

        $newCaseId = null;

        // Create new application with copied data
        $newApplication = new \App\Models\StudentApplication();

        if(!empty($originalApplication)){            
            // Generate new case ID with suffix
            $newCaseId = $this->generateRecurringCaseId($originalApplication->case_id, $student->id);
            $newApplication->fill($originalApplication->toArray());
        }else{
            $newApplication->student_id = $student->id;          
        }
        
        // Set new values
        $newApplication->case_id = $newCaseId;
        $newApplication->application_number = $this->generateApplicationNumber();
        $newApplication->status = 0; // Pending
        $newApplication->submitted_at = null;
        $newApplication->created_at = now();
        $newApplication->updated_at = now();
        
        // Set recurrence flags
        $newApplication->application_type = $applicationRequest->request_type;
        $newApplication->is_recurred_from = !empty($originalApplication) ? $originalApplication->id : null;
        $newApplication->is_recurred = false;
        
        // Clear Financial Info fields (set to empty string for required fields)
        $newApplication->reason_for_aid = '';
        $newApplication->received_support_from_others = 'no';
        $newApplication->received_scholarship = 'no';
        $newApplication->scholarship_details = null;
        $newApplication->how_fees_paid_before = null;
        
        // Clear Course Info fields (set to empty string for required fields)
        //$newApplication->course_name = null;
        //$newApplication->branch = null;
        //$newApplication->edu_stage = null;
        //$newApplication->course_duration_years = null;
        //$newApplication->course_duration_months = null;
        //$newApplication->enrolment_year = null;
        //$newApplication->current_year_or_sem = '';
        $newApplication->current_number = null;
        $newApplication->total_course_fees = null;
        $newApplication->current_year_semester_fees = null;
        $newApplication->amount_needed = null;
        $newApplication->support_required = null;
        $newApplication->fees_entries = null;
        //$newApplication->college_name = null;
        //$newApplication->college_address = null;
        $newApplication->is_submitted = 0;
        $newApplication->submitted_at = null;
        $newApplication->approved_date = null;
        $newApplication->closed_date = null;
        
        $newApplication->save();
        
        if(!empty($originalApplication)){ 
            // Mark the original application as recurred
            $originalApplication->update(['is_recurred' => true]);
            
            // Copy student documents
            $this->copyStudentDocuments($originalApplication, $newApplication);
        }

        return $newApplication;
    }

    /**
     * Generate recurring case ID with suffix (-A, -B, etc.)
     */
    private function generateRecurringCaseId($originalCaseId, $studentId)
    {
        // Remove any existing suffix from the original case ID
        $baseCaseId = preg_replace('/-[A-Z]$/', '', $originalCaseId);
        
        // Find existing applications for this student with the same base case ID
        $existingApplications = \App\Models\StudentApplication::where('student_id', $studentId)
            ->where('case_id', 'LIKE', $baseCaseId . '%')
            ->pluck('case_id')
            ->toArray();
        
        // Determine the next suffix
        $suffixes = [];
        foreach ($existingApplications as $caseId) {
            if (preg_match('/-([A-Z])$/', $caseId, $matches)) {
                $suffixes[] = $matches[1];
            }
        }
        
        // Find the next available suffix
        $nextSuffix = 'A';
        while (in_array($nextSuffix, $suffixes)) {
            $nextSuffix++;
        }
        
        return $baseCaseId . '-' . $nextSuffix;
    }

    /**
     * Copy student documents to new application
     */
    private function copyStudentDocuments($originalApplication, $newApplication)
    {
        $originalDocuments = $originalApplication->documents;
        
        foreach ($originalDocuments as $document) {
            $newDocument = new \App\Models\StudentDocument();
            $newDocument->fill($document->toArray());
            $newDocument->student_application_id = $newApplication->id;
            $newDocument->created_at = now();
            $newDocument->updated_at = now();
            $newDocument->save();
        }
    }

    /**
     * Generate unique application number.
     *
     * @return string
     */
    private function generateApplicationNumber()
    {
        do {
            $number = 'ILM-' . date('mY') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (\App\Models\StudentApplication::where('application_number', $number)->exists());
        
        return $number;
    }
}