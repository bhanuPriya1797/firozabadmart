<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentApplication;
use App\Models\StudentDocument;
use App\Models\ApprovalRecord;
use App\Models\ApplicationComment;
use App\Models\FinanceRecord;
use App\Models\StudentNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\CustomHelper;
use App\Mail\StudentActivationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class StudentController extends Controller
{
    private $ADMIN_ROUTE_NAME;

    public function __construct()
    {
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
    }

    public function index(Request $request, $filter_type = null)
    {
        // Existing index method remains unchanged
        if ($request->ajax()) {
            try {
                $query = Student::query()->latest();

                // Default condition: exclude latestApplication status 4 & 5
                $query->whereHas('latestApplication', function ($q) {
                    $q->whereNotIn('status', [4, 5]);
                });

                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->filled('family_lineage')) {
                    $query->where('family_lineage', $request->family_lineage);
                }
                if ($request->filled('start_date')) {
                    $query->whereDate('created_at', '>=', $request->start_date);
                }
                if ($request->filled('end_date')) {
                    $query->whereDate('created_at', '<=', $request->end_date);
                }
                if ($request->filled('application_status')) {
                    $query->whereHas('latestApplication', function ($q) use ($request) {
                        if ($request->application_status === 'submitted') {
                            $q->where('is_submitted', 1);
                        } elseif ($request->application_status === 'not_submitted') {
                            $q->where(function ($q2) {
                                $q2->whereNull('is_submitted')->orWhere('is_submitted', 0);
                            });
                        }
                    });
                }
                if ($request->filled('eligibility_status')) {
                    $query->whereHas('latestApplication', function ($q) use ($request) {
                        $q->where('status', $request->eligibility_status);
                    });
                }
                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%");
                    });
                }
                return DataTables::of($query)
                    ->editColumn('details', function ($row) {
                        $name = !empty($row->first_name) ? e($row->first_name).' '.e($row->surname) : 'N/A';
                        $email = !empty($row->email) ? e($row->email) : 'N/A';
                        $phone = !empty($row->contact_no) ? e($row->contact_no) : 'N/A';
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
                    ->addColumn('status', function ($row) {
                        return ($row->status == 1) ? '<span class="badge bg-label-primary me-1">Active</span>' : '<span class="badge bg-label-danger me-1">Inactive</span>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y h:i A') : '';
                    })
                    ->addColumn('application_status', function ($row) {
                        $html = '';
                        
                        // Application submission status
                        if ($row->latestApplication && $row->latestApplication->is_submitted) {
                            $html .= '<span class="badge bg-success me-1">Submitted</span>';
                        } else {
                            $html .= '<span class="badge bg-warning text-dark me-1">Not submitted Yet</span>';
                        }
                        
                        // Application type badge
                        if ($row->latestApplication) {
                            $applicationType = $row->latestApplication->application_type ?? 'new';
                            $typeClass = $applicationType === 'recurring' ? 'bg-info' : 'bg-primary';
                            $typeText = ucfirst($applicationType);
                            $html .= '<br><span class="badge ' . $typeClass . ' mt-1">' . $typeText . '</span>';
                        }
                        
                        return $html;
                    })
                    ->addColumn('eligibility_status', function ($row) {
                        if ($row->latestApplication && $row->latestApplication->status == 1) {
                            return '<span class="badge bg-warning text-dark">Eligible</span>';
                        } else if($row->latestApplication && $row->latestApplication->status == 2) {
                            return '<span class="badge bg-success">Approved</span>';
                        } else if($row->latestApplication && $row->latestApplication->status == 3) {
                            return '<span class="badge bg-danger">Rejected</span>';
                        } else if($row->latestApplication && $row->latestApplication->status == 5) {
                            return '<span class="badge bg-danger">Closed</span>';
                        } else if($row->latestApplication && $row->latestApplication->status == 4) {
                            return '<span class="badge bg-success">Accepted/Go To Finance</span>';
                        }else{
                            return '<span class="badge bg-info">Pending</span>'; 
                        }
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $applicationId = $row->latestApplication ? CustomHelper::encrypt($row->latestApplication->id) : null;
                        $viewUrl = $applicationId ? route($this->ADMIN_ROUTE_NAME . '.students.showByApplication', $applicationId) : '#';
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.students.edit', ['student' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.students.destroy', $encryptedId);
                        
                        $actions = '';
                        $hasAnyAction = false;
                        
                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.view')) || (auth()->user()->can('students.edit')) || (auth()->user()->can('students.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            
                            // View permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.view'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' .$viewUrl. '" data-id="' . $row->id . '" data-mode="view" data-title="Preview Student">
                                    <i class="icon-base ti tabler-eye me-1"></i> View/Work
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Edit permission
                            /*if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '" data-id="' . $row->id . '" data-mode="edit" data-title="Edit Student">
                                    <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }*/
                            
                            // Documents permission (same as view)
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.view'))) {
                                $actions .= '<a class="dropdown-item waves-effect btn-show-documents" href="javascript:void(0);" data-id="' . $encryptedId . '">
                                    <i class="icon-base ti tabler-folder me-1"></i> Documents
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.delete'))) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-student" href="javascript:void(0);" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            $actions .= '</div></div>';
                        }
                        
                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->orderColumn('details', function ($query, $order) {
                        $query->orderBy('name', $order);
                    })
                    ->rawColumns(['action','details','status','application_status','eligibility_status'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('StudentController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }
        $page_title = 'Students';
        $defaultFilters = [
            'status' => null,
            'application_status' => null,
            'eligibility_status' => null
        ];

        switch ($filter_type) {
            case 'approved':
                $defaultFilters['eligibility_status'] = 2;
                $page_title = 'Approved Students';
                break;
            case 'pending':
                $defaultFilters['eligibility_status'] = 0;
                $page_title = 'Pending Students';
                break;
            case 'eligible':
                $defaultFilters['eligibility_status'] = 1;
                $page_title = 'Eligible Students';
                break;
            case 'active':
                $defaultFilters['status'] = 1;
                $page_title = 'Active Students';
                break;
            case 'deactive':
                $defaultFilters['status'] = 0;
                $page_title = 'Deactive Students';
                break;
        }
        return view('admin.students.index', [
            'page_title' => $page_title,
            'defaultFilters' => $defaultFilters,
            'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME
        ]);
    }

    public function create()
    {
        $states = DB::table('states')->where('country_id', 101)->pluck('name')->toArray();
        return view('admin.students.form', compact('states'));
    }

    public function validateStep(Request $request)
    {
        $tab = $request->input('tab');
        $studentId = session('student_id');
        $validationRules = [];
        $data = [];

        try {
            switch ($tab) {
                case 'personal':
                    $validationRules = [
                        'first_name' => 'required|max:255',
                        'surname' => 'required|max:255',
                        'family_lineage' => 'required|in:SYED,NON-SYED',
                        'gender' => 'required|in:Male,Female,Other',
                        'dob' => 'nullable|date',
                        'email' => $studentId ? 'required|email|max:255|unique:students,email,'.$studentId : 'required|email|max:255|unique:students,email',
                        'password' => $studentId ? 'nullable|min:6' : 'required|min:6',
                        'address' => 'nullable',
                        'state' => 'required',
                        'district' => 'required',
                        'pincode' => 'nullable|max:20',
                        'contact_no' => 'required|max:20',
                        'guardian_name' => 'required|max:255',
                        'guardian_contact_no' => 'required|max:20',
                        'guardian_occupation' => 'required|max:255',
                        'monthly_income' => 'required|min:0',
                        'family_members' => 'required|integer|min:0',
                        'status' => 'required|in:1,0',
                    ];
                    $data = $request->validate($validationRules);
                    if (!empty($data['password'])) {
                        $data['password'] = Hash::make($data['password']);
                    } elseif ($studentId) {
                        unset($data['password']);
                    }
                    if ($studentId) {
                        $student = Student::findOrFail($studentId);
                        $student->update($data);
                    } else {
                        $student = Student::create($data);
                        session(['student_id' => $student->id]);
                    }
                    break;

                case 'financial':
                    if (!$studentId) {
                        return response()->json(['success' => false, 'errors' => ['general' => ['Personal information must be completed first.']]]);
                    }
                    $validationRules = [
                        'reason_for_aid' => 'nullable',
                        'received_support_from_others' => 'required|in:yes,no',
                        'received_scholarship' => 'required|in:yes,no',
                        'scholarship_details' => 'nullable',
                        'how_fees_paid_before' => 'nullable',
                    ];
                    $data = $request->validate($validationRules);
                    $student = Student::findOrFail($studentId);
                    $application = $student->applications()->firstOrCreate(['student_id' => $studentId], $data);
                    $application->update($data);
                    break;


                case 'course':
                    if (!$studentId) {
                        return response()->json(['success' => false, 'errors' => ['general' => ['Personal and financial information must be completed first.']]]);
                    }
                    $validationRules = [
                        'course_name' => 'required|max:255',
                        'branch' => 'nullable|max:255',
                        'edu_stage' => 'required|max:255',
                        'course_duration_years' => 'nullable|integer|min:0|max:10',
                        'course_duration_months' => 'nullable|integer|min:0|max:11',
                        'enrolment_year' => 'required|integer|digits:4',
                        'current_year_or_sem' => 'required|in:Year,Semester',
                        'current_number' => 'required|integer|min:1|max:12',
                        'total_course_fees' => 'nullable|numeric|min:0',
                        'fees_entries' => 'required|array|min:1',
                        'fees_entries.*.fees_purpose' => 'required',
                        'fees_entries.*.fees_purpose_description' => 'nullable|required_if:fees_entries.*.fees_purpose,Others',
                        'fees_entries.*.fees_duration' => 'required|in:Yearly,Semester,Monthly,One-time',
                        'fees_entries.*.fees_amount' => 'required|numeric|min:0',
                        'amount_needed' => 'nullable|numeric|min:0',
                        'support_required' => 'required|in:One-time,Recurring',
                        'fees_submission_status' => 'required|in:not_yet_known,date',
                        'last_fees_submission_date' => 'nullable|date',
                        'college_name' => 'required|max:255',
                        'college_address' => 'required',
                    ];
                    $data = $request->validate($validationRules);
                    $student = Student::findOrFail($studentId);
                    //prd($data);
                    $application = $student->applications()->firstOrCreate(['student_id' => $studentId], $data);
                    $application->update($data);
                    break;

                case 'bank':
                    if (!$studentId) {
                        return response()->json(['success' => false, 'errors' => ['general' => ['Previous steps must be completed first.']]]);
                    }
                    $validationRules = [
                        'account_no' => 'nullable|max:40',
                        'account_name' => 'nullable|max:255',
                        'bank_name' => 'nullable|max:255',
                        'bank_branch' => 'nullable|max:255',
                        'ifsc_code' => 'nullable|max:30',
                    ];
                    $data = $request->validate($validationRules);
                    $student = Student::findOrFail($studentId);
                    $application = $student->applications()->firstOrCreate(['student_id' => $studentId], $data);
                    $application->update($data);
                    break;

                case 'documents':

                    if (!$studentId) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['general' => ['Previous steps must be completed first.']]
                        ]);
                    }

                    $student = Student::findOrFail($studentId);
                    $application = $student->applications()->firstOrFail();

                    $validationRules = [
                        'documents' => 'required|array',
                        'documents.*.id'            => 'nullable|integer|exists:student_documents,id',
                        'documents.*.course_name'   => 'nullable|max:255',
                        'documents.*.college'       => 'nullable|max:255',
                        'documents.*.academic_year'=> 'nullable|max:255',
                        'documents.*.percentage'    => 'nullable|numeric|min:0|max:100',
                        'documents.*.document_name'=> 'nullable|max:255',
                        'documents.*.document_file'=> 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:2048',
                    ];

                    $data = $request->validate($validationRules);
                    $existingDocuments = $application->documents()->get()->keyBy('id');
                    $submittedDocumentIds = [];

                    foreach ($data['documents'] as $idx => $docData) {
                        $fileKey = "documents.$idx.document_file";
                        $docInput = [
                            'student_id'     => $student->id,
                            'course_name'    => $docData['course_name'] ?? null,
                            'college'        => $docData['college'] ?? null,
                            'academic_year'  => $docData['academic_year'] ?? null,
                            'percentage'     => $docData['percentage'] ?? null,
                            'document_name'  => $docData['document_name'] ?? null,
                        ];

                        $documentId = $docData['id'] ?? null;

                        if ($documentId && $existingDocuments->has($documentId)) {
                            $document = $existingDocuments[$documentId];
                            $submittedDocumentIds[] = $documentId;

                            if ($request->hasFile($fileKey)) {
                                if ($document->document_file && Storage::disk('public')->exists($document->document_file)) {
                                    Storage::disk('public')->delete($document->document_file);
                                }
                                $docInput['document_file'] = $request->file($fileKey)->store('uploads/students/docs', 'public');
                            } else {
                                $docInput['document_file'] = $document->document_file; // retain old
                            }

                            $document->update($docInput);

                        } else {
                            // New entry
                            if (
                                $request->hasFile($fileKey) ||
                                array_filter($docInput, fn($v) => $v !== null && $v !== '')
                            ) {

                                if ($request->hasFile($fileKey)) {
                                    $docInput['document_file'] = $request->file($fileKey)->store('uploads/students/docs', 'public');
                                }
                                $docInput['student_application_id'] = $application->id;
                                $docInput['student_id'] = $student->id;
                                $newDoc = $application->documents()->create($docInput);

                                if ($newDoc) {
                                    $submittedDocumentIds[] = $newDoc->id;
                                }
                            }
                        }
                    }
                    // Remove documents not submitted anymore
                    $toDelete = $existingDocuments->keys()->diff($submittedDocumentIds);
                    foreach ($toDelete as $docId) {
                        $doc = $existingDocuments[$docId];
                        /*
                        // STOPPING physical deletion
                        if ($doc->document_file && Storage::disk('public')->exists($doc->document_file)) {
                            Storage::disk('public')->delete($doc->document_file);
                        }
                        */
                        $doc->delete();
                    }

                    // Update is_submitted if provided
                    if ($request->has('is_submitted') && $request->input('is_submitted') == 1) {
                        $application->update(['is_submitted' => 1]);
                    }

                    return response()->json(['success' => true]);
                    break;
            }
            return response()->json(['success' => true, 'student_id' => session('student_id')]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('StudentController@validateStep: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => ['general' => ['Error: ' . $e->getMessage()]]], 500);
        }
    }

    /*public function store(Request $request)
    {
        $studentId = session('student_id');
        if (!$studentId) {
            return redirect()->route('admin.students.create')
                ->with(['error' => 'Please complete all steps before submitting.']);
        }

        DB::beginTransaction();
        try {
            $student = Student::findOrFail($studentId);
            $application = $student->applications()->firstOrFail();
            // Update status to mark as completed
            $student->update(['status' => 1]);
            $application->update(['status' => 1]);

            DB::commit();
            session()->forget('student_id');
            return redirect()->route('admin.students.index')
                ->with(['success' => 'Student application created successfully!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('StudentController@store: ' . $e->getMessage());
            return back()->with(['error' => 'Error: ' . $e->getMessage()]);
        }
    }*/

    public function edit($id)
    {
        $id = CustomHelper::decrypt($id);
        $student = Student::with(['applications.documents'])->findOrFail($id);
        $studentApplication = $student->applications()->latest()->first();
        $states = DB::table('states')->where('country_id', 101)->pluck('name')->toArray();
        session(['student_id' => $student->id]); // Set student_id for editing
        return view('admin.students.form', compact('student', 'studentApplication', 'states'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $student = Student::findOrFail($id);
            $application = $student->applications()->firstOrFail();
            // Validate and update all tabs (similar to store, but for update)
            $studentData = $request->validate([
                'first_name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'family_lineage' => 'required|in:SYED,NON-SYED',
                'gender' => 'required|in:Male,Female,Other',
                'dob' => 'nullable|date',
                'email' => 'required|string|email|max:255|unique:students,email,'.$student->id,
                'password' => 'nullable|string|min:6',
                'address' => 'nullable|string',
                'state' => 'required|string',
                'district' => 'required|string',
                'pincode' => 'nullable|string|max:20',
                'contact_no' => 'required|string|max:20',
                'guardian_name' => 'nullable|string|max:255',
                'guardian_contact_no' => 'nullable|string|max:20',
                'guardian_occupation' => 'nullable|string|max:255',
                'guardian_occupation_details' => 'nullable|string',
                'monthly_income' => 'nullable|numeric|min:0',
                'family_members' => 'nullable|integer|min:0',
                'status' => 'required|in:1,0',
            ]);
            if (!empty($studentData['password'])) {
                $studentData['password'] = Hash::make($studentData['password']);
            } else {
                unset($studentData['password']);
            }
            $student->update($studentData);

            $applicationData = $request->validate([
                'reason_for_aid' => 'nullable|string',
                'received_support_from_others' => 'required|in:yes,no',
                'received_scholarship' => 'required|in:yes,no',
                'scholarship_details' => 'nullable|string',
                'how_fees_paid_before' => 'nullable|string',
                'course_name' => 'required|string|max:255',
                'branch' => 'nullable|string|max:255',
                'edu_stage' => 'required|string|max:255',
                'course_duration_years' => 'nullable|integer|min:0|max:10',
                'course_duration_months' => 'nullable|integer|min:0|max:11',
                'enrollment_year' => 'required|integer|digits:4',
                'current_year_or_sem' => 'required|in:Year,Semester',
                'current_number' => 'required|integer|min:1|max:12',
                'total_course_fees' => 'nullable|numeric|min:0',
                'current_year_semester_fees' => 'nullable|numeric|min:0',
                'fees_type' => 'required|in:yearly,semester',
                'fees_amount' => 'required|numeric|min:0',
                'amount_needed' => 'nullable|numeric|min:0',
                'support_required' => 'required|in:One-time,Recurring',
                'college_name' => 'required|string|max:255',
                'college_address' => 'required|string',
                'account_no' => 'nullable|string|max:40',
                'account_name' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'bank_branch' => 'nullable|string|max:255',
                'ifsc_code' => 'nullable|string|max:30',
            ]);
            $applicationData['status'] = $studentData['status'];
            $application->update($applicationData);

            $documents = $request->input('documents', []);
            $request->validate([
                'documents.*.document_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:2048',
                'documents.*.course_name' => 'nullable|string|max:255',
                'documents.*.college' => 'nullable|string|max:255',
                'documents.*.academic_year' => 'nullable|string|max:255',
                'documents.*.percentage' => 'nullable|numeric|min:0|max:100',
                'documents.*.document_name' => 'nullable|string|max:255',
            ]);
            $application->documents()->delete();
            if (!empty($documents)) {
                foreach ($documents as $idx => $docData) {
                    $fileKey = "documents.$idx.document_file";
                    $docInput = [
                        'student_id'     => $student->id,
                        'course_name' => $docData['course_name'] ?? null,
                        'college' => $docData['college'] ?? null,
                        'academic_year' => $docData['academic_year'] ?? null,
                        'percentage' => $docData['percentage'] ?? null,
                        'document_name' => $docData['document_name'] ?? null,
                    ];
                    if ($request->hasFile($fileKey)) {
                        $file = $request->file($fileKey);
                        $path = $file->store('uploads/students/docs', 'public');
                        $docInput['document_file'] = $path;
                    }
                    if (array_filter($docInput)) {
                        $application->documents()->create($docInput);
                    }
                }
            }

            DB::commit();
            
            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'students',
                $student->id,
                'Update Student',
                'Updated student: ' . $student->first_name . ' ' . $student->surname,
                json_encode($studentData)
            );
            
            session()->forget('student_id');
            return redirect()->route('admin.students.index')
                ->with(['success' => 'Student application updated successfully!']);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('StudentController@update: ' . $e->getMessage());
            return back()->withInput()->with(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $id = CustomHelper::decrypt($id);
        try {
            $student = Student::with(['applications.documents'])->findOrFail($id);

            // Delete documents and their files
            foreach ($student->applications as $application) {
                foreach ($application->documents as $document) {
                    // Delete file from storage
                    /*
                    // STOPPING physical deletion
                    if ($document->document_file && Storage::disk('public')->exists($document->document_file)) {
                        Storage::disk('public')->delete($document->document_file);
                    }
                    */
                    $document->delete();
                }

                // Delete application
                $application->delete();
            }

            // Delete the student
            $student->delete();

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'students',
                $id,
                'Delete Student',
                'Deleted student: ' . $student->first_name . ' ' . $student->surname,
                'Deleted student: ' . $student->first_name . ' ' . $student->surname . ' (ID: ' . $id . ')'
            );

            return response()->json(['success' => true, 'message' => 'Student and related records deleted successfully.']);
        } catch (\Throwable $e) {
            \Log::error('StudentController@destroy: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function removeDocument(Request $request)
    {

        try {
            // Validate the request
            $request->validate([
                'document_id' => 'required|exists:student_documents,id',
            ]);

            // Find the document
            $document = StudentDocument::findOrFail($request->document_id);

            // Ensure the document belongs to the authenticated user's application
            $studentApplication = StudentApplication::where('id', $document->student_application_id)->first();

            if (!$studentApplication) {
                return response()->json([
                    'success' => false,
                    'errors' => ['You are not authorized to remove this document.']
                ], 403);
            }

            // Delete the file from storage if it exists
            /*
            // STOPPING physical deletion
            if ($document->document_file && Storage::disk('public')->exists($document->document_file)) {
                Storage::disk('public')->delete($document->document_file);
            }
            */

            // Delete the document record from the database
            $document->delete();

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()['document_id'] ?? ['Invalid document ID.']
            ], 422);
        } catch (\Exception $e) {
            Log::error('Document removal error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['An error occurred while removing the document.']
            ], 500);
        }
    }

    public function fetchDocuments($id)
    {

        $id = CustomHelper::decrypt($id);
        $student = Student::with(['latestApplication.documents'])->findOrFail($id);
        $documents = $student->latestApplication?->documents ?? [];

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }


    /**
     * Display a preview of student details, application, and documents.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {

        $id = CustomHelper::decrypt($id);
        $student = Student::findOrFail($id);
        
        // Get all applications for the student, ordered by latest first
        $applications = $student->applications()
            ->with(['recurredFrom', 'recurredApplications'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get the current (latest) application
        $currentApplication = $applications->first();
        
        // Get documents for the current application
        $documents = $currentApplication ? StudentDocument::where('student_application_id', $currentApplication->id)->get() : collect([]);

        $approvals = $currentApplication ? ApprovalRecord::with('user')
            ->where('student_application_id', $currentApplication->id)
            ->get() : collect([]);

        $comments = $currentApplication ? ApplicationComment::with('user')
            ->where('student_application_id', $currentApplication->id)
            ->get() : collect([]);
            
        $ADMIN_ROUTE_NAME = $this->ADMIN_ROUTE_NAME;
        return view('admin.students.view', compact('student', 'currentApplication', 'documents', 'approvals', 'comments', 'ADMIN_ROUTE_NAME'));
    }

    /**
     * Display student details based on application ID.
     *
     * @param int $applicationId
     * @return \Illuminate\View\View
     */
    public function showByApplication($applicationId)
    {
        $applicationId = CustomHelper::decrypt($applicationId);
        $currentApplication = StudentApplication::findOrFail($applicationId);
        $student = $currentApplication->student;
        
        // Get documents for this specific application
        $documents = StudentDocument::where('student_application_id', $currentApplication->id)->get();

        // Get approvals for current application
        $approvals = ApprovalRecord::with('user')
            ->where('student_application_id', $currentApplication->id)
            ->get();

        // Get comments for current application
        $comments = ApplicationComment::with('user')
            ->where('student_application_id', $currentApplication->id)
            ->get();

        // Get all previous applications for the same student (only those created before current application)
        $previousApplications = $student->applications()
            ->with(['approvalRecords.user', 'applicationComments.user'])
            ->where('id', '!=', $currentApplication->id)
            ->where('created_at', '<', $currentApplication->created_at)
            ->orderBy('created_at', 'desc')
            ->get();

        // Group previous approvals and comments by application
        $previousApprovalsGrouped = [];
        $previousCommentsGrouped = [];
        
        foreach ($previousApplications as $prevApp) {
            if ($prevApp->approvalRecords->isNotEmpty()) {
                $previousApprovalsGrouped[] = [
                    'application' => $prevApp,
                    'approvals' => $prevApp->approvalRecords
                ];
            }
            
            if ($prevApp->applicationComments->isNotEmpty()) {
                $previousCommentsGrouped[] = [
                    'application' => $prevApp,
                    'comments' => $prevApp->applicationComments
                ];
            }
        }
            
        $ADMIN_ROUTE_NAME = $this->ADMIN_ROUTE_NAME;
        return view('admin.students.view', compact('student', 'currentApplication', 'documents', 'approvals', 'comments', 'previousApprovalsGrouped', 'previousCommentsGrouped', 'ADMIN_ROUTE_NAME'));
    }

    /**
     * Generate and download a PDF of student details, application, and documents.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf($id)
    {
        $id = CustomHelper::decrypt($id);
        $student = Student::findOrFail($id);
        $application = $student->latestApplication;
        $documents = $application ? StudentDocument::where('student_application_id', $application->id)->get() : collect([]);

        $pdf = Pdf::loadView('admin.students.pdf-preview', compact('student', 'application', 'documents'));
        return $pdf->download('student-preview-' . $student->id . '.pdf');
    }

    public function storeApproval(Request $request)
    {

        $updArr = [];
        $validated = $request->validate([
            'student_application_id' => 'required|exists:student_applications,id',
            'is_eligible' => 'required|boolean',
            'amount' => 'nullable|numeric',
            'recommended_by' => 'nullable|max:255',
            'comment' => 'nullable',
        ]);

        $user = auth()->user();
        $userId = $user->id;
        $applicationId = $validated['student_application_id'];

        //Prevent duplicate approval by same user
        $existing = ApprovalRecord::where('student_application_id', $applicationId)
            ->where('created_by', $userId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted your decision for this application.',
            ], 409);
        }

        //Save approval record
        $approvalData = $validated;
        $approvalData['created_by'] = $userId;
        unset($approvalData['comment']);

        ApprovalRecord::create($approvalData);

        //Save comment if any
        if (!empty($validated['comment'])) {
            $commentData = [
                'student_application_id' => $applicationId,
                'comment' => $validated['comment'],
                'created_by' => $userId,
            ];
            // Only SuperAdmin can mark comment to show on front
            if ($user && $user->hasRole('SuperAdmin')) {
                $commentData['show_in_front'] = $request->boolean('show_in_front') ? 1 : 0;
            }
            ApplicationComment::create($commentData);
        }

        //Get roles
        $inspectorIds = User::role('Inspector')->pluck('id')->toArray();
        $approverIds  = User::role('Approver')->pluck('id')->toArray();
        $adminIds  = User::role('SuperAdmin')->pluck('id')->toArray();
        //prd($adminIds);
        //Count approvals by role and eligibility
        $inspectorEligibleCount = ApprovalRecord::where('student_application_id', $applicationId)
            ->whereIn('created_by', $inspectorIds)
            ->where('is_eligible', 1)
            ->count();

        $approverEligibleCount = ApprovalRecord::where('student_application_id', $applicationId)
            ->whereIn('created_by', $approverIds)
            ->where('is_eligible', 1)
            ->count();

        $adminEligibleCount = ApprovalRecord::where('student_application_id', $applicationId)
            ->whereIn('created_by', $adminIds)
            ->where('is_eligible', 1)
            ->count();

        // Count rejections by role
        $inspectorRejectionCount = ApprovalRecord::where('student_application_id', $applicationId)
            ->whereIn('created_by', $inspectorIds)
            ->where('is_eligible', 0)
            ->count();

        $approverRejectionCount = ApprovalRecord::where('student_application_id', $applicationId)
            ->whereIn('created_by', $approverIds)
            ->where('is_eligible', 0)
            ->count();

        $adminRejectionCount = ApprovalRecord::where('student_application_id', $applicationId)
            ->whereIn('created_by', $adminIds)
            ->where('is_eligible', 0)
            ->count();

        //Determine final status
        $newStatus = null;

        // Admin rejection takes precedence
        if ($adminRejectionCount >= 1) {
            $newStatus = 3; // Rejected by Admin
        }
        // For approver level: need at least 2 rejections to reject the application
        elseif ($approverRejectionCount >= 2) {
            $newStatus = 3; // Rejected by Approvers
        }
        // For inspector level: need at least 2 rejections to reject the application
        elseif ($inspectorRejectionCount >= 2) {
            $newStatus = 3; // Rejected by Inspectors
        } elseif ($adminEligibleCount >= 1) {
            $newStatus = 4; // Approved by SuperAdmin

            // Get the current application to check if it already has a case_id
            $currentApplication = StudentApplication::find($applicationId);
            
            // Only generate new case ID if application doesn't already have one
            // This prevents generating new case IDs for recurring applications
            if (empty($currentApplication->case_id)) {
                $caseId = $this->generateCaseId($applicationId);
                $updArr['case_id'] = $caseId;
            }
            
            $updArr['approved_date'] = Carbon::now();

        } elseif ($approverEligibleCount >= 2) {
            $newStatus = 2; // Approved by Approvers
        } elseif ($inspectorEligibleCount >= 2) {
            $newStatus = 1; // Approved by Inspectors
        }

        if (!is_null($newStatus)) {
            $updArr['status'] = $newStatus;
            StudentApplication::where('id', $applicationId)->update($updArr);
        }

        // Log activity
        $student = StudentApplication::find($applicationId)->student;
        CustomHelper::recordActionLog(
            url()->current(),
            'student_applications',
            $applicationId,
            'Student Approval',
            'Approval submitted for student: ' . $student->first_name . ' ' . $student->surname,
            json_encode($validated)
        );

        return response()->json(['success' => true, 'message' => 'Approval submitted successfully.']);
    }

    public function storeComment(Request $request)
    {

        $validated = $request->validate([
            'student_application_id' => 'required|exists:student_applications,id',
            'comment' => 'required'
        ]);

        $validated['created_by'] = auth()->id();
        // Allow SuperAdmin to set show_in_front
        $user = auth()->user();
        if ($user && $user->hasRole('SuperAdmin')) {
            $validated['show_in_front'] = $request->boolean('show_in_front') ? 1 : 0;
        }
        ApplicationComment::create($validated);

        // Log activity
        $student = StudentApplication::find($validated['student_application_id'])->student;
        CustomHelper::recordActionLog(
            url()->current(),
            'student_applications',
            $validated['student_application_id'],
            'Student Comment',
            'Comment added for student: ' . $student->first_name . ' ' . $student->surname,
            'Comment: ' . $validated['comment']
        );

        return response()->json(['success' => true]);
    }

    public function financedApplications(Request $request)
    {

        if ($request->ajax()) {
            try {
                $query = StudentApplication::with(['student', 'financeRecords'])
                    ->whereIn('status', [4, 5]) // Assuming 4 = Closed
                    ->latest();

                // Filter by payment completion status
                $paymentStatus = $request->get('payment_status', 'pending'); // Default to pending
                if ($paymentStatus === 'completed') {
                    // Show only applications where total paid exactly equals amount_needed
                    $query->whereHas('financeRecords', function($q) {
                        $q->selectRaw('student_application_id, SUM(amount) as total_paid')
                          ->groupBy('student_application_id')
                          ->havingRaw('SUM(amount) = (SELECT amount_needed FROM student_applications WHERE id = student_application_id AND amount_needed > 0)');
                    });
                } elseif ($paymentStatus === 'pending') {
                    // Show only applications where total paid != amount_needed OR no finance records yet
                    $query->where(function($q) {
                        $q->whereDoesntHave('financeRecords')
                          ->orWhere(function($subQ) {
                              $subQ->whereHas('financeRecords', function($finQ) {
                                  $finQ->selectRaw('student_application_id, SUM(amount) as total_paid')
                                       ->groupBy('student_application_id')
                                       ->havingRaw('SUM(amount) != (SELECT amount_needed FROM student_applications WHERE id = student_application_id AND amount_needed > 0)');
                              });
                          });
                    })->where('amount_needed', '>', 0);
                }

                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('case_id', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('student_details', function ($row) {
                        $student = $row->student;
                        return $student
                            ? "{$student->first_name} {$student->surname} <br>Email: {$student->email} <br>Phone: {$student->contact_no}"
                            : 'N/A';
                    })
                    ->addColumn('approved_date', function ($row) {
                        return $row->approved_date ? Carbon::parse($row->approved_date)->format('d M Y h:i A') : '-';
                    })
                    ->addColumn('appeal_number', function ($row) {
                        return $row->created_at ? Carbon::parse($row->created_at)->format('m-Y') : '-';
                    })
                    ->addColumn('year', function ($row) {
                        return $row->created_at ? Carbon::parse($row->created_at)->format('Y') : '-';
                    })
                    ->addColumn('family_lineage', function ($row) {
                        return $row->student->family_lineage;
                    })
                    ->addColumn('amount_needed', function ($row) {
                        return $row->amount_needed;
                    })
                    ->addColumn('support_required', function ($row) {
                        return $row->support_required;
                    })
                    ->addColumn('payment_status', function ($row) {
                        $status = $row->getPaymentStatus();
                        if ($status === 'completed') {
                            return '<span class="badge bg-label-success">Completed</span>';
                        } elseif ($status === 'pending') {
                            return '<span class="badge bg-label-warning">Pending</span>';
                        } else {
                            return '<span class="badge bg-label-secondary">No Amount</span>';
                        }
                    })
                    ->addColumn('case_id', function ($row) {
                        return $row->case_id ? $row->case_id : '-';
                    })
                    ->addColumn('status_label', function ($row) {
                        return '<span class="badge bg-label-success">Accepted</span>';
                    })
                    ->addColumn('gender', function ($row) {
                        $student = $row->student;
                        return $student
                            ? "{$student->gender}"
                            : 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.financeRecords', CustomHelper::encrypt($row->id));
                        return '
                            <a href="' . $viewUrl . '" class="btn btn-sm btn-info">View</a>
                        ';
                    })
                    ->rawColumns(['student_details', 'status', 'status_label', 'payment_status', 'action'])
                    ->make(true);
            } catch (\Throwable $e) {
                \Log::error('financedApplications error: ' . $e->getMessage());
                return response()->json(['error' => 'Server error'], 500);
            }
        }

        $currentPaymentStatus = $request->get('payment_status', 'pending');
        
        return view('admin.students.application_finance', [
            'page_title' => 'Financed Applications',
            'application_status_code' => 4, // Optional for filter preselect
            'currentPaymentStatus' => $currentPaymentStatus
        ]);
    }

    public function closedApplications(Request $request)
    {

        if ($request->ajax()) {
            try {
                $query = StudentApplication::with('student')
                    ->where('status', 5) // Assuming 4 = Closed
                    ->latest();

                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('student_details', function ($row) {
                        $student = $row->student;
                        return $student
                            ? "{$student->first_name} {$student->surname} <br>Email: {$student->email} <br>Phone: {$student->contact_no}"
                            : 'N/A';
                    })
                    ->addColumn('application_date', function ($row) {
                        return $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';
                    })
                    ->addColumn('closed_date', function ($row) {
                        return $row->closed_date ? Carbon::parse($row->closed_date)->format('d M Y h:i A') : '-';
                    })
                    ->addColumn('status_label', function ($row) {
                        return '<span class="badge bg-label-secondary">Closed</span>';
                    })
                    ->addColumn('gender', function ($row) {
                        $student = $row->student;
                        return $student
                            ? "{$student->gender}"
                            : 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.showByApplication', CustomHelper::encrypt($row->id));
                        return '
                            <a href="' . $viewUrl . '" class="btn btn-sm btn-info">View</a>
                        ';
                    })
                    ->rawColumns(['student_details', 'status', 'status_label', 'action'])
                    ->make(true);
            } catch (\Throwable $e) {
                \Log::error('closedApplications error: ' . $e->getMessage());
                return response()->json(['error' => 'Server error'], 500);
            }
        }

        return view('admin.students.closed_applications', [
            'page_title' => 'Closed Applications',
            'application_status_code' => 5 // Optional for filter preselect
        ]);
    }

    public function pendingApplications(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = StudentApplication::with('student')
                    ->where('status', 0) // 0 = Pending
                    ->latest();

                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%")
                          ->orWhere('application_number', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('student_details', function ($row) {
                        $student = $row->student;
                        return $student
                            ? "{$student->first_name} {$student->surname} <br>Email: {$student->email} <br>Phone: {$student->contact_no}"
                            : 'N/A';
                    })
                    ->addColumn('application_date', function ($row) {
                        return $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';
                    })
                    ->addColumn('submitted_date', function ($row) {
                        return $row->submitted_at ? Carbon::parse($row->submitted_at)->format('d M Y h:i A') : 'Not submitted Yet';
                    })
                    ->addColumn('status_label', function ($row) {
                        return '<span class="badge bg-label-warning">Pending</span>';
                    })
                    ->addColumn('application_type', function ($row) {
                        $type = $row->application_type ?? 'new';
                        $class = $type === 'recurring' ? 'bg-label-info' : 'bg-label-success';
                        return '<span class="badge ' . $class . '">' . ucfirst($type) . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.showByApplication', CustomHelper::encrypt($row->id));
                        return '
                            <a href="' . $viewUrl . '" class="btn btn-sm btn-info">View</a>
                        ';
                    })
                    ->rawColumns(['student_details', 'status_label', 'application_type', 'action'])
                    ->make(true);
            } catch (\Throwable $e) {
                \Log::error('pendingApplications error: ' . $e->getMessage());
                return response()->json(['error' => 'Server error'], 500);
            }
        }

        return view('admin.students.pending_applications', [
            'page_title' => 'Pending Applications',
            'application_status_code' => 0 // Optional for filter preselect
        ]);
    }

    public function rejectedApplications(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = StudentApplication::with('student')
                    ->where('status', 3) // Assuming 3 = Rejected
                    ->latest();

                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('student_details', function ($row) {
                        $student = $row->student;
                        return $student
                            ? "{$student->first_name} {$student->surname} <br>Email: {$student->email} <br>Phone: {$student->contact_no}"
                            : 'N/A';
                    })
                    ->addColumn('application_date', function ($row) {
                        return $row->created_at ? Carbon::parse($row->created_at)->format('d M Y h:i A') : '-';
                    })
                    ->addColumn('status_label', function ($row) {
                        return '<span class="badge bg-label-danger">Rejected</span>';
                    })
                    ->addColumn('gender', function ($row) {
                        $student = $row->student;
                        return $student
                            ? "{$student->gender}"
                            : 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.showByApplication', CustomHelper::encrypt($row->id));
                        return '
                            <a href="' . $viewUrl . '" class="btn btn-sm btn-info">View</a>
                        ';
                    })
                    ->rawColumns(['student_details', 'status', 'status_label', 'action'])
                    ->make(true);
            } catch (\Throwable $e) {
                \Log::error('rejectedApplications error: ' . $e->getMessage());
                return response()->json(['error' => 'Server error'], 500);
            }
        }

        return view('admin.students.applications', [
            'page_title' => 'Rejected Applications',
            'application_status_code' => 3
        ]);
    }

    public function applicationFinanceRecords(Request $request)
    {
        try {
            $ADMIN_ROUTE_NAME = $this->ADMIN_ROUTE_NAME;
            $encryptedId = $request->id;
            $applicationId = CustomHelper::decrypt($encryptedId);
            $application = StudentApplication::with('student')->findOrFail($applicationId);

            $financeRecords = FinanceRecord::where('student_application_id', $applicationId)
                ->latest()
                ->get();

            // Determine approved amount from last SuperAdmin eligible approval
            $superAdminIds = User::role('SuperAdmin')->pluck('id')->toArray();
            $lastApprovedBySuperAdmin = ApprovalRecord::where('student_application_id', $applicationId)
                ->whereIn('created_by', $superAdminIds)
                ->where('is_eligible', 1)
                ->orderByDesc('created_at')
                ->first();
            $approvedAmount = $lastApprovedBySuperAdmin?->amount ?? null;

            $totalPaid = $financeRecords->sum('amount');

            return view('admin.students.finance_records', [
                'ADMIN_ROUTE_NAME' => $ADMIN_ROUTE_NAME,
                'application' => $application,
                'financeRecords' => $financeRecords,
                'approvedAmount' => $approvedAmount,
                'totalPaid' => $totalPaid,
            ]);
        } catch (\Throwable $e) {
            \Log::error('applicationFinanceRecords error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }


    /**
     * Generate a unique Case ID for the application.
     *
     * Format: ILM-{FinancialYear}-{ZeroPaddedID}
     */
    private function generateCaseId($applicationId)
    {
        // Calculate current financial year in "YYYY" format like 2526
        $currentYear = date('y');
        $nextYear = (date('n') >= 4) ? $currentYear + 1 : $currentYear;
        $startYear = (date('n') >= 4) ? $currentYear : $currentYear - 1;
        $endYear = (date('n') >= 4) ? $currentYear + 1 : $currentYear;
        $financialYear = str_pad($startYear, 2, '0', STR_PAD_LEFT) . str_pad($endYear, 2, '0', STR_PAD_LEFT);

        // Zero-pad application ID
        $paddedId = str_pad($applicationId, 5, '0', STR_PAD_LEFT);

        $caseId = config('custom.order_prefix')."-{$financialYear}-{$paddedId}";

        // Ensure uniqueness in DB
        $exists = StudentApplication::where('case_id', $caseId)->exists();
        if ($exists) {
            $caseId .= '-' . rand(100, 999);
        }

        return $caseId;
    }

    /**
     * Show all registered students (new registrations)
     */
    public function registeredStudents(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Student::query()->latest();

                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%")
                          ->orWhere('family_lineage', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('name', function ($row) {
                        return $row->first_name . ' ' . $row->surname;
                    })
                    ->addColumn('status', function ($row) {
                        $statusText = ($row->status == 1) ? 'Active' : 'Pending';
                        $statusClass = ($row->status == 1) ? 'bg-label-success' : 'bg-label-warning';
                        return '<span class="badge ' . $statusClass . ' me-1">' . $statusText . '</span>';
                    })
                    ->addColumn('applications_count', function ($row) {
                        $count = $row->applications()->count();
                        if ($count > 0) {
                            return '<button type="button" class="btn btn-sm btn-outline-primary" onclick="showApplicationsModal(' . $row->id . ')">
                                <i class="ti tabler-file-text"></i> &nbsp;' . $count . '
                            </button>';
                        }
                        return '<span class="text-muted">No Applications</span>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.show-simple', $encryptedId) . '?back_to=registered';
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.students.edit', ['student' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.students.destroy', $encryptedId);
                        
                        $actions = '';
                        $hasAnyAction = false;
                        
                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.view')) || (auth()->user()->can('students.edit')) || (auth()->user()->can('students.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            
                            // View permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.view'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $viewUrl . '">
                                    <i class="icon-base ti tabler-eye me-1"></i> View Details
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Edit permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '">
                                    <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Toggle Status permission (same as edit)
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect toggle-status" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $row->status . '">
                                    <i class="icon-base ti tabler-toggle-right me-1"></i> Toggle Status
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.delete'))) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-student" href="javascript:void(0);" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            $actions .= '</div></div>';
                        }
                        
                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['action', 'status', 'applications_count'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('StudentController@registeredStudents: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        return view('admin.students.registered', [
            'page_title' => 'Registered Students',
            'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME
        ]);
    }

    /**
     * Show activated students (status = 1)
     */
    public function activatedStudents(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Student::where('status', 1)->latest();

                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%")
                          ->orWhere('family_lineage', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('name', function ($row) {
                        return $row->first_name . ' ' . $row->surname;
                    })
                    ->addColumn('status', function ($row) {
                        return '<span class="badge bg-label-success me-1">Active</span>';
                    })
                    ->addColumn('applications_count', function ($row) {
                        $count = $row->applications()->count();
                        return '<button type="button" class="btn btn-sm btn-outline-primary" onclick="showApplicationsModal(' . $row->id . ', \'' . addslashes($row->first_name . ' ' . $row->surname) . '\')">' . $count . '</button>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.show-simple', $encryptedId) . '?back_to=activated';
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.students.edit', ['student' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.students.destroy', $encryptedId);
                        
                        $actions = '';
                        $hasAnyAction = false;
                        
                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('students.view')) ||
                            (auth()->user()->can('students.edit')) ||
                            (auth()->user()->can('students.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            
                            // View permission
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.view'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $viewUrl . '">
                                    <i class="icon-base ti tabler-eye me-1"></i> View Details
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Edit permission
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '">
                                    <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Toggle Status permission (same as edit)
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect toggle-status" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $row->status . '">
                                    <i class="icon-base ti tabler-toggle-right me-1"></i> Activate/Deactivate
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.delete'))) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-student" href="javascript:void(0);" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            $actions .= '</div></div>';
                        }
                        
                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['action', 'status', 'applications_count'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('StudentController@activatedStudents: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        return view('admin.students.activated', [
            'page_title' => 'Activated Students',
            'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME
        ]);
    }

    /**
     * Show deactivated students (status = 0)
     */
    public function deactivatedStudents(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Student::where('status', 0)->latest();

                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%")
                          ->orWhere('family_lineage', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('name', function ($row) {
                        return $row->first_name . ' ' . $row->surname;
                    })
                    ->addColumn('status', function ($row) {
                        return '<span class="badge bg-label-warning me-1">Pending</span>';
                    })
                    ->addColumn('applications_count', function ($row) {
                        $count = $row->applications()->count();
                        return '<button type="button" class="btn btn-sm btn-outline-primary" onclick="showApplicationsModal(' . $row->id . ', \'' . addslashes($row->first_name . ' ' . $row->surname) . '\')">' . $count . '</button>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.show-simple', $encryptedId) . '?back_to=deactivated';
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.students.edit', ['student' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.students.destroy', $encryptedId);
                        
                        $actions = '';
                        $hasAnyAction = false;
                        
                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('students.view')) ||
                            (auth()->user()->can('students.edit')) ||
                            (auth()->user()->can('students.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            
                            // View permission
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.view'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $viewUrl . '">
                                    <i class="icon-base ti tabler-eye me-1"></i> View Details
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Edit permission
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '">
                                    <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Toggle Status permission (same as edit)
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect toggle-status" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $row->status . '">
                                    <i class="icon-base ti tabler-toggle-right me-1"></i> Activate/Deactivate
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.delete'))) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-student" href="javascript:void(0);" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            $actions .= '</div></div>';
                        }
                        
                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['action', 'status', 'applications_count'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('StudentController@deactivatedStudents: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        return view('admin.students.deactivated', [
            'page_title' => 'Deactivated Students',
            'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME
        ]);
    }

    /**
     * Show recurring students (students with recurring applications)
     */
    public function recurringStudents(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Get students who have recurring applications
                $query = Student::whereHas('applications', function($q) {
                    $q->where('application_type', 'recurring');
                })->with(['latestApplication', 'applications' => function($q) {
                    $q->where('application_type', 'recurring')->latest();
                }])->latest();

                if (!empty($request->search['value'])) {
                    $searchTerm = $request->search['value'];
                    $query->where(function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, surname) LIKE ?", ["%{$searchTerm}%"])
                          ->orWhere('email', 'like', "%{$searchTerm}%")
                          ->orWhere('contact_no', 'like', "%{$searchTerm}%")
                          ->orWhere('family_lineage', 'like', "%{$searchTerm}%");
                    });
                }

                return DataTables::of($query)
                    ->addColumn('name', function ($row) {
                        return $row->first_name . ' ' . $row->surname;
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            return '<span class="badge bg-label-success me-1">Active</span>';
                        } else {
                            return '<span class="badge bg-label-warning me-1">Pending</span>';
                        }
                    })
                    ->addColumn('main_application', function ($row) {
                        $mainApplication = $row->applications()->where('application_type', 'new')->first();
                        if ($mainApplication) {
                            $encryptedId = CustomHelper::encrypt($mainApplication->id);
                            $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.showByApplication', $encryptedId);
                            return '<a href="' . $viewUrl . '" class="btn btn-sm btn-outline-primary">' . $mainApplication->application_number . '</a>';
                        }
                        return '<span class="text-muted">No main application</span>';
                    })
                    ->addColumn('recurring_applications', function ($row) {
                        $count = $row->applications()->where('application_type', 'recurring')->count();
                        return '<button type="button" class="btn btn-sm btn-outline-info" onclick="showApplicationsModal(' . $row->id . ', \'' . addslashes($row->first_name . ' ' . $row->surname) . '\')">View (' . $count . ')</button>';
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $viewUrl = route($this->ADMIN_ROUTE_NAME . '.students.show-simple', $encryptedId) . '?back_to=recurring';
                        $editUrl = route($this->ADMIN_ROUTE_NAME . '.students.edit', ['student' => $encryptedId]);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.students.destroy', $encryptedId);
                        
                        $actions = '';
                        $hasAnyAction = false;
                        
                        // Check if user has any permissions to show dropdown
                        if (auth()->user()->hasRole('SuperAdmin') || 
                            (auth()->user()->can('students.view')) ||
                            (auth()->user()->can('students.edit')) ||
                            (auth()->user()->can('students.delete'))) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';
                            
                            // View permission
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.view'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $viewUrl . '">
                                    <i class="icon-base ti tabler-eye me-1"></i> View Details
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Edit permission
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect" href="' . $editUrl . '">
                                    <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Toggle Status permission (same as edit)
                            if (auth()->user()->hasRole('SuperAdmin') || 
                                (auth()->user()->can('students.edit'))) {
                                $actions .= '<a class="dropdown-item waves-effect toggle-status" href="javascript:void(0);" data-id="' . $row->id . '" data-status="' . $row->status . '">
                                    <i class="icon-base ti tabler-toggle-right me-1"></i> Toggle Status
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            // Delete permission
                            if (auth()->user()->hasRole('SuperAdmin') || (auth()->user()->can('students.delete'))) {
                                $actions .= '<a class="dropdown-item waves-effect btn-delete-student" href="javascript:void(0);" data-id="' . $row->id . '" data-url="' . $deleteUrl . '">
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }
                            
                            $actions .= '</div></div>';
                        }
                        
                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['action', 'status', 'main_application', 'recurring_applications'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('StudentController@recurringStudents: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        return view('admin.students.recurring', [
            'page_title' => 'Recurring Students',
            'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME
        ]);
    }

    /**
     * Show simple student details (for registered/activated/deactivated students)
     */
    public function showSimple($id)
    {
        try {
            $id = CustomHelper::decrypt($id);
            $student = Student::findOrFail($id);
            
            return view('admin.students.show-simple', [
                'student' => $student,
                'ADMIN_ROUTE_NAME' => $this->ADMIN_ROUTE_NAME
            ]);
        } catch (\Exception $e) {
            \Log::error('StudentController@showSimple: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Student not found.');
        }
    }

    /**
     * Get student applications for modal display
     */
    public function getStudentApplications($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            $applications = $student->applications()
                ->with(['recurredFrom', 'recurredApplications', 'financeRecords'])
                ->latest()
                ->get();

            $applicationsData = $applications->map(function ($application) {
                $statusText = match($application->status) {
                    0 => 'Pending',
                    1 => 'Eligible',
                    2 => 'Approved',
                    3 => 'Rejected',
                    4 => 'Financed',
                    5 => 'Closed',
                    default => 'Unknown'
                };

                $statusClass = match($application->status) {
                    0 => 'bg-warning',
                    1 => 'bg-info',
                    2 => 'bg-success',
                    3 => 'bg-danger',
                    4 => 'bg-primary',
                    5 => 'bg-secondary',
                    default => 'bg-light'
                };

                $applicationType = $application->application_type ?? 'new';
                $typeClass = $applicationType === 'recurring' ? 'bg-info' : 'bg-success';
                $typeText = ucfirst($applicationType);

                // Calculate total finance records amount for closed applications
                $totalFinanceAmount = 0;
                if ($application->status == 5) { // Closed applications
                    $totalFinanceAmount = $application->financeRecords->sum('amount');
                }

                $data = [
                    'id' => $application->id,
                    'application_number' => $application->application_number,
                    'course_name' => $application->course_name ?? 'Not specified',
                    'submitted_at' => $application->submitted_at ? \Carbon\Carbon::parse($application->submitted_at)->format('d M Y h:i A') : 'Draft',
                    'status_text' => $statusText,
                    'status_class' => $statusClass,
                    'type_text' => $typeText,
                    'type_class' => $typeClass,
                    'is_recurred_from' => $application->is_recurred_from,
                    'parent_application_number' => $application->recurredFrom->application_number ?? null,
                    'view_url' => route($this->ADMIN_ROUTE_NAME . '.students.showByApplication', CustomHelper::encrypt($application->id)),
                    'status' => $application->status
                ];

                // Add amount fields for closed applications
                if ($application->status == 5) {
                    $data['amount_needed'] = $application->amount_needed ?? 0;
                    $data['total_finance_amount'] = $totalFinanceAmount;
                }

                return $data;
            });

            return response()->json([
                'success' => true,
                'student_name' => $student->first_name . ' ' . $student->surname,
                'applications' => $applicationsData
            ]);
        } catch (\Exception $e) {
            \Log::error('StudentController@getStudentApplications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle student status (activate/deactivate)
     */
    public function toggleStatus(Request $request)
    {
        try {
            $student = Student::findOrFail($request->id);
            $oldStatus = $student->status;
            $newStatus = $student->status == 1 ? 0 : 1;
            $student->update(['status' => $newStatus]);

            $statusText = $newStatus == 1 ? 'activated' : 'deactivated';
            
            // Send activation email if student is being activated
            if ($oldStatus == 0 && $newStatus == 1) {
                try {
                    Mail::to($student->email)->send(new StudentActivationNotification($student));
                    \Log::info('Activation email sent to student: ' . $student->email);
                } catch (\Exception $mailException) {
                    \Log::error('Failed to send activation email to ' . $student->email . ': ' . $mailException->getMessage());
                    // Don't fail the activation process if email fails
                }
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Student ' . $statusText . ' successfully!' . ($newStatus == 1 ? ' Activation email has been sent.' : ''),
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            \Log::error('StudentController@toggleStatus: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating status.'], 500);
        }
    }

    /**
     * Send a message to student and allow resubmission
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $applicationId
     * @return \Illuminate\Http\Response
     */
    public function sendMessageToStudent(Request $request, $applicationId)
    {
        $request->validate([
            'message' => 'required|string|min:10|max:2000',
        ]);

        $application = StudentApplication::with('student')->findOrFail($applicationId);
        
        DB::beginTransaction();
        try {            
            // Update the application with the admin message
            $application->update([
                'admin_message' => $request->input('message'),
                'admin_message_at' => now(),
                'is_submitted' => 0, // Allow resubmission
                'status' => 0, // Status 3 typically means 'Returned for Resubmission'
            ]);

            // Send email notification to student
            if ($application->student && $application->student->email) {
                $emailData = [
                    'student_name' => $application->student->first_name . ' ' . $application->student->surname,
                    'message' => $request->input('message'),
                    'application_id' => $application->application_number,
                    'login_url' => url('/login')
                ];

                try {
                    // Send the email using the view directly
                    Mail::send([], [], function($message) use ($application, $emailData) {
                        $html = view('emails.admin_message', $emailData)->render();
                        
                        $message->to($application->student->email, $application->student->first_name . ' ' . $application->student->surname)
                                ->subject('Update on Your Application #' . $application->application_number)
                                ->html($html);
                    });
                } catch (\Exception $e) {
                    // Log email sending error but don't fail the whole operation
                    \Log::error('Failed to send email notification: ' . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Message sent to student successfully. The application has been marked for resubmission.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error sending message to student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message. Please try again.'
            ], 500);
        }
    }
}