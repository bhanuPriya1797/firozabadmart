<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StudentNote;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class StudentNoteController extends Controller
{
    /**
     * Store a new student note from frontend
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeStudentNote(Request $request): JsonResponse
    {
        try {
            // Validate the request (reCAPTCHA validation removed)
            $validator = Validator::make($request->all(), [
                'student_name' => 'required|string|max:255',
                'student_email' => 'required|email|max:255',
                'student_phone' => 'nullable|string|max:20',
                'note_message' => 'required|string|max:2000'
                // 'g-recaptcha-response' => 'required' // Commented out - reCAPTCHA disabled
            ], [
                'student_name.required' => 'Student name is required.',
                'student_email.required' => 'Student email is required.',
                'student_email.email' => 'Please enter a valid email address.',
                'note_message.required' => 'Note message is required.',
                'note_message.max' => 'Note message cannot exceed 2000 characters.'
                // 'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.' // Commented out
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // reCAPTCHA v3 Server-Side Verification - COMMENTED OUT
            /*
                reCAPTCHA v3 Server-Side Verification Process - DISABLED:
                1. Extract the token sent from frontend (g-recaptcha-response)
                2. Send token to Google's verification API along with secret key
                3. Google returns a JSON response with:
                   - success: boolean (true/false)
                   - score: float (0.0 to 1.0, higher = more human-like)
                   - action: string (matches the action from frontend)
                   - challenge_ts: timestamp of the challenge
                   - hostname: domain where the challenge was solved
                
                Security Notes:
                - Always verify on server side, never trust frontend-only validation
                - Token expires in 2 minutes, so verify immediately
                - Score interpretation: 1.0 = very likely human, 0.0 = very likely bot
                - Typical threshold: 0.5 (adjust based on your needs)
                - Store secret key securely in .env file
                
                CustomHelper::checkGoogleReCaptcha() handles:
                - HTTP request to Google's verification endpoint
                - Secret key validation
                - Score threshold checking
                - Error handling for network issues
            */
            // $recaptchaResponse = $request->input('g-recaptcha-response');
            // $recaptchaResult = CustomHelper::checkGoogleReCaptcha($recaptchaResponse);
            // 
            // if (!$recaptchaResult) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'reCAPTCHA verification failed. Please try again.'
            //     ], 422);
            // }

            // Create the student note
            // Note: assigned_to is set to null as it will be managed from the backend admin panel
            // Note: recaptcha_token is set to null as reCAPTCHA verification is disabled
            $studentNote = StudentNote::create([
                'student_name' => $request->student_name,
                'email' => $request->student_email,
                'phone' => $request->student_phone,
                'assigned_to' => null, // Managed from backend, not frontend
                'note_message' => $request->note_message,
                'recaptcha_token' => null, // reCAPTCHA disabled
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student note submitted successfully! We will review it and get back to you soon.',
                'data' => [
                    'id' => $studentNote->id,
                    'student_name' => $studentNote->student_name,
                    'status' => $studentNote->status
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error storing student note from frontend: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting your note. Please try again later.'
            ], 500);
        }
    }

    /**
     * Store method - alias for storeStudentNote to match route definition
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return $this->storeStudentNote($request);
    }
}