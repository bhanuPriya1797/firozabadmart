<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\AdminRegistrationNotification;
use App\Mail\StudentWelcomeNotification;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Storage;

class StudentAuthController extends Controller
{
    /**
     * Show the student login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm(): \Illuminate\View\View|RedirectResponse
    {
        // Redirect to dashboard if student is already logged in
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        
        return view('frontend.pages.student-login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Check if the student exists
            $student = Student::where('email', $request->email)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials do not match our records.'
                ], 401);
            }

            // Check if the student's account is approved
            if ($student->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has not been approved yet. Please wait for admin approval or contact us for assistance. You will be notified via email when your account is approved.'
                ], 403);
            }

            // Check if password is correct
            if (!Hash::check($request->password, $student->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials do not match our records.'
                ], 401);
            }

            // Log the student in
            Auth::guard('student')->login($student, $request->remember);

            return response()->json([
                'success' => true,
                'message' => 'Welcome back, ' . $student->first_name . '!',
                'redirect' => route('student.dashboard')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Student login error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the student registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm(): \Illuminate\View\View|RedirectResponse
    {
        // Redirect to dashboard if student is already logged in
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        
        return view('frontend.pages.student-register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'surname' => 'nullable|string|max:255',
                'father_name' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:students',
                'phone' => 'required|regex:/^\+?[0-9]{10,14}$/',
                'gender' => 'required|in:Male,Female,Other',
                'dob' => 'required|date',
                'alternate_phone' => 'nullable|regex:/^\+?[0-9]{10,14}$/',
                'whatsapp_number' => 'required|regex:/^\+?[0-9]{10,14}$/',
                'marital_status' => 'required|in:Married,Single',
                'aadhar_card_number' => 'required|string|max:50',
                'aadhar_document' => 'required|file|mimes:pdf,jpeg,jpg,png|max:5120',
                'password' => 'required|min:8|max:20|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,20}$/',
                'terms' => 'required|accepted',
            ], [
                'phone.regex' => 'Phone number must be 10-14 digits and may start with +.',
                'alternate_phone.regex' => 'Alternate phone number must be 10-14 digits and may start with +.',
                'whatsapp_number.regex' => 'WhatsApp number must be 10-14 digits and may start with +.',
                'password.regex' => 'Password must include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
                'terms.accepted' => 'You must agree to the Terms and Conditions & Privacy Policy.',
            ]);

            $aadharPath = null;
            if ($request->hasFile('aadhar_document')) {
                $file = $request->file('aadhar_document');
                $aadharPath = $file->store('uploads/students/aadhar', 'public');
            }

            $registerData = [
                'first_name' => $request->first_name,
                'surname' => $request->surname,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'email' => $request->email,
                'contact_no' => $request->phone,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'alternate_phone' => $request->alternate_phone,
                'whatsapp_number' => $request->whatsapp_number,
                'marital_status' => $request->marital_status,
                'is_minor' => $request->boolean('is_minor'),
                'aadhar_card_number' => $request->aadhar_card_number,
                'aadhar_document' => $aadharPath,
                'family_lineage' => $request->family_lineage,
                'password' => Hash::make($request->password),
                'status' => 0,
            ];

            // Create the student
            $student = Student::create($registerData);

            // Send email notifications
            $this->sendRegistrationEmails($student);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Your account has been created and is pending approval. We will notify you via email once your account is approved.',
                'redirect' => route('student.login')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Student registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration. Please try again.'
            ], 500);
        }
    }

    /**
     * Send registration notification emails to admin and student
     *
     * @param  \App\Models\Student  $student
     * @return void
     */
    private function sendRegistrationEmails($student)
    {
        try {
            // Get admin email from settings
            $adminEmail = \App\Helpers\CustomHelper::getSettings(['contact_email'])['contact_email'];
            
            if(env('TEST_EMAIL_PREFERENCE')){
                $adminEmail = env('TEST_EMAIL_TO');
            }

            // Send email to admin using Mailable class
            Mail::to($adminEmail)->send(new AdminRegistrationNotification($student));
            
            // Send email to student using Mailable class
            Mail::to($student->email)->send(new StudentWelcomeNotification($student));
            
        } catch (\Exception $e) {
            \Log::error('Failed to send registration emails: ' . $e->getMessage());
        }
    }

    /**
     * Show the student dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('frontend.pages.student-dashboard');
    }

    /**
     * Log the student out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login')->with('success', 'You have been successfully logged out.');
    }

    /**
     * Show the form for requesting a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showForgotPasswordForm(): \Illuminate\View\View|RedirectResponse
    {
        // Redirect to dashboard if student is already logged in
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        
        return view('frontend.pages.student-forgot-password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            // Check if the student exists and is activated
            $student = Student::where('email', $request->email)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'We could not find a student with that email address.'
                ], 404);
            }

            // Check if the student's account is activated
            if ($student->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has not been approved yet. Please wait for admin approval before requesting a password reset.'
                ], 403);
            }

            // Generate a reset token
            $token = Str::random(60);
            
            // Store the token in the password_resets table
            DB::table('password_resets')->updateOrInsert(
                ['email' => $student->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => now()
                ]
            );

            // Create reset URL
            $resetUrl = route('student.password.reset', $token) . '?email=' . urlencode($student->email);

            // Send the password reset email
            Mail::to($student->email)->send(new \App\Mail\StudentPasswordResetNotification($student, $resetUrl));

            return response()->json([
                'success' => true,
                'message' => 'We have emailed your password reset link! Please check your email inbox.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Password reset email error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the reset link. Please try again later.'
            ], 500);
        }
    }
    
    /**
     * Display the password reset view.
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('frontend.pages.student-reset-password', ['token' => $token, 'email' => $request->email]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|string|min:8|max:20|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,20}$/',
            ], [
                'password.regex' => 'Password must include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
            ]);

            // Verify token is valid
            $tokenData = DB::table('password_resets')
                ->where('email', $request->email)
                ->first();

            if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token or email address.'
                ], 400);
            }

            // Check if token is expired (tokens valid for 60 minutes)
            if (now()->diffInMinutes($tokenData->created_at) > 60) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password reset link has expired. Please request a new one.'
                ], 400);
            }

            // Find the student and update password
            $student = Student::where('email', $request->email)->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'We could not find a student with that email address.'
                ], 404);
            }

            // Check if student is activated
            if ($student->status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has not been approved yet. Please wait for admin approval.'
                ], 403);
            }

            $student->password = Hash::make($request->password);
            $student->save();

            // Delete the token
            DB::table('password_resets')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Your password has been reset successfully! You can now log in with your new password.',
                'redirect' => route('student.login')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Password reset error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resetting your password. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the student profile page.
     *
     * @return \Illuminate\View\View
     */
    public function showProfile()
    {
        $student = Auth::guard('student')->user();
        return view('frontend.pages.student-profile', compact('student'));
    }

    /**
     * Update the student's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $student = Auth::guard('student')->user();
            
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'surname' => 'nullable|string|max:255',
                'email' => 'required|email|unique:students,email,' . $student->id,
                'contact_no' => 'required|string|max:20',
                'gender' => 'required|in:Male,Female,Other',
                'family_lineage' => 'required|in:SYED,NON-SYED',
                'dob' => 'nullable|date',
                'address' => 'nullable|string|max:500',
                'state' => 'nullable|string|max:100',
                'district' => 'nullable|string|max:100',
                'pincode' => 'nullable|string|max:10',
                'guardian_name' => 'nullable|string|max:255',
                'guardian_contact_no' => 'nullable|string|max:20',
                'guardian_occupation' => 'nullable|string|max:255',
                'monthly_income' => 'nullable|numeric|min:0',
                'family_members' => 'nullable|integer|min:1',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'cropped_image' => 'nullable|string',
                'remove_image' => 'nullable|boolean'
            ]);
            //prd($request->all());

            // Handle profile photo upload/cropping
            if ($request->has('cropped_image') && !empty($request->cropped_image)) {
                // Handle cropped image
                $croppedImage = $request->cropped_image;
                
                // Remove data URL prefix
                $croppedImage = str_replace('data:image/png;base64,', '', $croppedImage);
                $croppedImage = str_replace('data:image/jpeg;base64,', '', $croppedImage);
                $croppedImage = str_replace('data:image/jpg;base64,', '', $croppedImage);
                $croppedImage = str_replace(' ', '+', $croppedImage);
                
                // Decode base64 image
                $imageData = base64_decode($croppedImage);
                
                // Generate unique filename
                $filename = 'student_' . $student->id . '_' . time() . '.png';
                $path = 'uploads/students/profile-photos/' . $filename;
                
                // Delete old photo if exists
                if ($student->profile_photo) {
                    Storage::delete('public/' . $student->profile_photo);
                }
                
                // Store new cropped image
                $stored = Storage::disk('public')->put($path, $imageData);
                
                if ($stored) {
                    $validated['profile_photo'] = $path;
                    \Log::info('Profile photo uploaded successfully: ' . $path);
                } else {
                    \Log::error('Failed to upload profile photo: ' . $path);
                }
                
            }/* elseif ($request->hasFile('profile_photo')) {
                // Handle regular file upload (fallback)
                if ($student->profile_photo) {
                    \Storage::delete('public/' . $student->profile_photo);
                }
                $path = $request->file('profile_photo')->store('uploads/students/profile-photos', 'public');
                if ($path) {
                    $validated['profile_photo'] = $path;
                    \Log::info('Profile photo uploaded successfully (regular): ' . $path);
                } else {
                    \Log::error('Failed to upload profile photo (regular)');
                }
                
            }*/ elseif ($request->has('remove_image') && $request->remove_image == '1') {
                // Handle image removal
                if ($student->profile_photo) {
                    // \Storage::delete('public/' . $student->profile_photo); // STOPPING physical deletion
                }
                $validated['profile_photo'] = null;
            } else {
                // No image changes - keep existing photo
                unset($validated['profile_photo']);
            }

            $student->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Student profile update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating profile'
            ], 500);
        }
    }

    /**
     * Show the change password form.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('frontend.pages.student-change-password');
    }

    /**
     * Update the student's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required|string|min:8'
            ]);

            $student = Auth::guard('student')->user();

            // Check current password
            if (!Hash::check($validated['current_password'], $student->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update password
            $student->update([
                'password' => Hash::make($validated['new_password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while changing password'
            ], 500);
        }
    }

    /**
     * Show the help request form.
     *
     * @return \Illuminate\View\View
     */
    public function showHelpRequestForm()
    {
        return view('frontend.pages.student-help-request');
    }

    /**
     * Submit a help request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitHelpRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'course_name' => 'required|string|max:255',
                'institution' => 'required|string|max:255',
                'course_duration' => 'nullable|string|max:100',
                'current_semester' => 'nullable|string|max:100',
                'help_type' => 'required|in:Financial Assistance,Academic Support,Mentorship,Study Materials,Other',
                'help_description' => 'required|string|max:2000',
                'urgency_level' => 'required|in:Low,Medium,High',
                'course_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'additional_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048'
            ]);

            $student = Auth::guard('student')->user();

            // Handle file uploads
            if ($request->hasFile('course_certificate')) {
                $validated['course_certificate'] = $request->file('course_certificate')->store('students/help-requests/certificates', 'public');
            }

            $additionalDocs = [];
            if ($request->hasFile('additional_documents')) {
                foreach ($request->file('additional_documents') as $file) {
                    $additionalDocs[] = $file->store('students/help-requests/additional', 'public');
                }
                $validated['additional_documents'] = json_encode($additionalDocs);
            }

            // Create help request (you'll need to create a HelpRequest model and migration)
            // For now, we'll just return success
            // HelpRequest::create([
            //     'student_id' => $student->id,
            //     ...$validated
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Help request submitted successfully! We will review your request and get back to you soon.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting help request'
            ], 500);
        }
    }

    /**
     * Show the application form.
     *
     * @return \Illuminate\View\View
     */
    public function showApplicationForm()
    {
        $student = auth('student')->user();
        $application = $student->applications()->latest()->first();
        
        // Check if student has already submitted an application
        if ($application && $application->is_submitted == 1 && empty($application->latestApprovedApplicationRequest)) {
            return redirect()->route('student.applications.list')
                ->with('info', 'You have already submitted an application. You can view your applications below.');
        }
        
        return view('frontend.pages.student-application', compact('application'));
    }

    /**
     * Save application step by step.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveApplicationStep(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $student = auth('student')->user();
            $step = $request->input('step');
            
            // Get or create application
            $application = $student->applications()->latest()->firstOrCreate(
                ['student_id' => $student->id],
                ['application_number' => $this->generateApplicationNumber()]
            );
            
            // Check if application is already submitted
            if ($application->is_submitted == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'This application has already been submitted and cannot be modified.'
                ], 403);
            }
            
            // Ensure application number exists
            if (!$application->application_number) {
                $application->update(['application_number' => $this->generateApplicationNumber()]);
            }
            
            switch ($step) {
                case 'personal':
                    $studentData = $request->validate([
                        'first_name' => 'required|string|max:255',
                        'surname' => 'required|string|max:255',
                        'family_lineage' => 'required|in:SYED,NON-SYED',
                        'gender' => 'required|in:Male,Female,Other',
                        'dob' => 'nullable|date',
                        'email' => 'required|string|email|max:255|unique:students,email,'.$student->id,
                        'contact_no' => 'required|string',
                        'address' => 'required|string',
                        'state' => 'required|string',
                        'district' => 'required|string',
                        'pincode' => 'required|string',
                        'guardian_name' => 'required|string|max:255',
                        'guardian_contact_no' => 'required|string|max:255',
                        'guardian_occupation' => 'required|string|max:255',
                        'guardian_occupation_details' => 'required|string',
                        'monthly_income' => 'required|min:0',
                        'family_members' => 'required|integer|min:0',
                    ]);
                    
                    $student->update($studentData);
                    break;
                    
                case 'financial':
                    $financialData = $request->validate([
                        'reason_for_aid' => 'nullable|string',
                        'received_support_from_others' => 'required|in:yes,no',
                        'received_scholarship' => 'required|in:yes,no',
                        'scholarship_details' => 'nullable|string',
                        'how_fees_paid_before' => 'nullable|string',
                    ]);
                    
                    $application->update($financialData);
                    break;
                    
                case 'course':
                    $courseData = $request->validate([
                        'course_name' => 'required|string|max:255',
                        'branch' => 'nullable|string|max:255',
                        'edu_stage' => 'required|string|max:255',
                        'course_duration_years' => 'nullable|integer|min:0|max:10',
                        'course_duration_months' => 'nullable|integer|min:0|max:11',
                        'enrolment_year' => 'required|integer',
                        'current_year_or_sem' => 'required|string',
                        'current_number' => 'required|integer|min:1|max:12',
                        'total_course_fees' => 'nullable|numeric|min:0',
                        'current_year_semester_fees' => 'nullable|numeric|min:0',
                        'semester_year_amount' => 'nullable|numeric|min:0',
                        'amount_needed' => 'nullable|numeric|min:0',
                        'support_required' => 'required|in:One-time,Recurring',
                        'fees_submission_status' => 'required|in:not_yet_known,date',
                        'last_fees_submission_date' => 'nullable|date',
                        'college_name' => 'required|max:255',
                        'college_address' => 'required',
                        'fees_entries' => 'required|array|min:1',
                        'fees_entries.*.fees_purpose' => 'required|string',
                        'fees_entries.*.fees_purpose_description' => 'nullable|string',
                        'fees_entries.*.fees_duration' => 'required|string',
                        'fees_entries.*.fees_amount' => 'required|numeric|min:0',
                    ]);
                    
                    // Additional validation: ensure at least one fees entry has all required fields
                    $feesEntries = $request->input('fees_entries', []);
                    $hasValidFeesEntry = false;
                    
                    foreach ($feesEntries as $entry) {
                        if (!empty($entry['fees_purpose']) && 
                            !empty($entry['fees_duration']) && 
                            !empty($entry['fees_amount']) && 
                            $entry['fees_amount'] > 0) {
                            $hasValidFeesEntry = true;
                            break;
                        }
                    }
                    
                    if (!$hasValidFeesEntry) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please add at least one Financial Aid Request Details entry with all required fields filled.',
                            'errors' => ['fees_entries' => ['At least one fees entry must be completed with all required fields.']]
                        ], 422);
                    }
                    
                    $application->update($courseData);
                    break;
                    
                case 'bank':
                    $bankData = $request->validate([
                        'account_no' => 'nullable|string',
                        'account_name' => 'nullable|max:255',
                        'bank_name' => 'nullable|max:255',
                        'bank_branch' => 'nullable|max:255',
                        'ifsc_code' => 'nullable|max:30',
                    ]);
                    
                    $application->update($bankData);
                    break;
                    
                case 'documents':
                    $documents = $request->input('documents', []);
                    $removedDocuments = $request->input('removed_documents', []);
                    
                    // Get all existing document IDs
                    $existingDocumentIds = $application->documents()->pluck('id')->toArray();
                    $submittedDocumentIds = [];
                    
                    // First, handle removed documents
                    if (!empty($removedDocuments)) {
                        foreach ($removedDocuments as $docId) {
                            $documentToDelete = $application->documents()->find($docId);
                            if ($documentToDelete) {
                                // Delete the file from storage
                                if ($documentToDelete->document_file && file_exists(public_path('storage/' . $documentToDelete->document_file))) {
                                    unlink(public_path('storage/' . $documentToDelete->document_file));
                                }
                                // Delete the database record
                                $documentToDelete->delete();
                            }
                        }
                    }
                    
                    if (!empty($documents)) {
                        foreach ($documents as $idx => $docData) {
                            $fileKey = "documents.$idx.document_file";
                            $documentId = $docData['id'] ?? null;
                            
                            // Skip if this document was marked for removal
                            if (in_array($documentId, $removedDocuments)) {
                                continue;
                            }
                            
                            $docInput = [
                                'student_id' => $student->id,
                                'student_application_id' => $application->id,
                                'course_name' => $docData['course_name'] ?? null,
                                'college' => $docData['college'] ?? null,
                                'academic_year' => $docData['academic_year'] ?? null,
                                'percentage' => $docData['percentage'] ?? null,
                                'document_name' => $docData['document_name'] ?? null,
                            ];
                            
                            // Check if this is an existing document or a new one
                            if ($documentId) {
                                $submittedDocumentIds[] = $documentId;
                                
                                // Update existing document
                                $existingDocument = $application->documents()->find($documentId);
                                
                                if ($existingDocument) {
                                    // Only update if there's a new file or if other data has changed
                                    $hasNewFile = $request->hasFile($fileKey);
                                    $hasDataChanges = (
                                        $existingDocument->course_name !== $docInput['course_name'] ||
                                        $existingDocument->college !== $docInput['college'] ||
                                        $existingDocument->academic_year !== $docInput['academic_year'] ||
                                        $existingDocument->percentage !== $docInput['percentage'] ||
                                        $existingDocument->document_name !== $docInput['document_name']
                                    );
                                    
                                    if ($hasNewFile || $hasDataChanges) {
                                        // Handle file upload if new file is provided
                                        if ($hasNewFile) {
                                            // Delete old file if it exists
                                            if ($existingDocument->document_file && file_exists(public_path('storage/' . $existingDocument->document_file))) {
                                                unlink(public_path('storage/' . $existingDocument->document_file));
                                            }
                                            
                                            $file = $request->file($fileKey);
                                            $filename = time() . '_' . $file->getClientOriginalName();
                                            $docInput['document_file'] = $file->storeAs('uploads/students/docs', $filename, 'public');
                                        }
                                        
                                        $existingDocument->update($docInput);
                                    }
                                }
                            } else {
                                // Create new document - save even without file
                                // Check if at least some document data is provided
                                $hasDocumentData = !empty($docInput['course_name']) || 
                                                 !empty($docInput['college']) || 
                                                 !empty($docInput['academic_year']) || 
                                                 !empty($docInput['percentage']) || 
                                                 !empty($docInput['document_name']);
                                
                                if ($hasDocumentData || $request->hasFile($fileKey)) {
                                    // Handle file upload if provided
                                    if ($request->hasFile($fileKey)) {
                                        $file = $request->file($fileKey);
                                        $filename = time() . '_' . $file->getClientOriginalName();
                                        $docInput['document_file'] = $file->storeAs('uploads/students/docs', $filename, 'public');
                                    }
                                    
                                    $application->documents()->create($docInput);
                                }
                            }
                        }
                    }
                    break;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($step) . ' information saved successfully!',
                'application_id' => $application->id,
                'application_number' => $application->application_number
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Student application step save error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the information'
            ], 500);
        }
    }

    /**
     * Show application preview before final submission.
     *
     * @return \Illuminate\View\View
     */
    public function showApplicationPreview()
    {
        $student = auth('student')->user();
        $application = $student->applications()->latest()->first();
        
        if (!$application) {
            return redirect()->route('student.application')->with('error', 'No application found. Please complete the application form first.');
        }
        
        // If application is already submitted, redirect to applications list
        if ($application->is_submitted == 1) {
            return redirect()->route('student.applications.list')
                ->with('info', 'You have already submitted this application. You can view it in your applications list.');
        }
        
        return view('frontend.pages.student-application-preview', compact('application'));
    }

    /**
     * Submit the final application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitFinalApplication(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $student = auth('student')->user();
            $application = $student->applications()->latest()->first();
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'No application found to submit'
                ], 404);
            }
            
            if ($application->is_submitted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application has already been submitted'
                ], 400);
            }
            
            // Mark as submitted and clear any previous admin message
            $application->update([
                'is_submitted' => 1,
                'status' => 0, // Pending status
                'submitted_at' => now(),
                'admin_message' => null,
                'admin_message_at' => null
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully! We will review your application and get back to you soon.',
                'application_number' => $application->application_number
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Final application submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the application'
            ], 500);
        }
    }

    /**
     * Show student's applications list.
     *
     * @return \Illuminate\View\View
     */
    public function showApplicationsList()
    {
        $student = auth('student')->user();
        $applications = $student->applications()
            ->with(['recurredFrom', 'recurredApplications'])
            ->latest()
            ->get();

        return view('frontend.pages.student-applications-list', compact('applications'));
    }

    /**
     * Get application data for AJAX requests.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplicationData(Request $request)
    {
        $student = auth('student')->user();
        
        // Get application ID from request parameter
        $applicationId = $request->get('application_id');
        
        if ($applicationId) {
            // Get specific application by ID (ensure it belongs to the authenticated student)
            $application = $student->applications()->with('documents')->where('id', $applicationId)->first();
        } else {
            // Fallback to first application if no ID provided
            $application = $student->applications()->with('documents')->first();
        }
        
        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'No application found.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'application' => $application,
            'student' => $student
        ]);
    }

    /**
     * Download application as PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadApplicationPDF()
    {
        $student = auth('student')->user();
        $application = $student->applications()->with('documents')->first();
        
        if (!$application) {
            return redirect()->route('student.application')->with('error', 'No application found.');
        }
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('frontend.pages.student-application-pdf', compact('application'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('ILM_Application_' . $application->application_number . '.pdf');
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

    /**
     * Show the edit profile form.
     *
     * @return \Illuminate\View\View
     */
    public function showEditProfileForm()
    {
        $student = auth('student')->user();
        return view('frontend.pages.student-edit-profile', compact('student'));
    }

    /**
     * Send application request notification to admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendApplicationRequestNotification(Request $request)
    {
        try {
            $student = auth('student')->user();
            $requestType = $request->input('request_type', 'new'); // 'new' or 'recurring'
            $applicationId = $request->input('application_id');
            
            $application = null;
            if ($applicationId && $requestType === 'recurring') {
                $application = $student->applications()->find($applicationId);
            }
            
            // Check if there's already a pending request for this student and type
            $existingRequest = \App\Models\ApplicationRequest::where('student_id', $student->id)
                ->where('request_type', $requestType)
                ->where('status', 'pending');
                
            if ($requestType === 'recurring' && $applicationId) {
                $existingRequest->where('application_id', $applicationId);
            }
            
            if ($existingRequest->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a pending ' . $requestType . ' application request. Please wait for admin approval.'
                ]);
            }
            
            // Create new application request record
            $applicationRequest = \App\Models\ApplicationRequest::create([
                'student_id' => $student->id,
                'application_id' => $applicationId,
                'request_type' => $requestType,
                'status' => 'pending',
                'requested_at' => now()
            ]);
            
            // Get admin email from settings or use default
            $adminEmail = CustomHelper::getSetting('contact_email');
            
            if(env('TEST_EMAIL_PREFERENCE') == true){
                $adminEmail = env('TEST_EMAIL_TO');
            }

            // Send email notification
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(
                new \App\Mail\AdminApplicationRequestNotification($student, $application, $requestType)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Your request has been sent to the admin successfully. You will be notified once it is reviewed.'
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Application request notification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification. Please try again later.'
            ], 500);
        }
    }

    /**
     * Check for pending application requests for the authenticated student.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPendingRequests()
    {
        try {
            $student = auth('student')->user();
            
            $pendingRequests = \App\Models\ApplicationRequest::where('student_id', $student->id)
                ->where('status', 'pending')
                ->select('request_type', 'application_id')
                ->get();
            
            return response()->json([
                'success' => true,
                'pending_requests' => $pendingRequests
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Check pending requests error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check pending requests.'
            ], 500);
        }
    }
}
