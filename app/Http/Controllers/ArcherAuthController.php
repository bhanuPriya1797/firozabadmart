<?php

namespace App\Http\Controllers;

use App\Models\Archer;
use App\Models\ArcherApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ArcherAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('frontend.pages.student-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('archer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully.',
                'redirect' => route('archer.dashboard'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.',
        ], 401);
    }

    public function showRegisterForm()
    {
        return view('frontend.pages.student-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:archers',
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
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not exceed 20 characters.',
            'password.confirmed' => 'Passwords do not match.',
            'password.regex' => 'Password must include uppercase, lowercase, number, and special character.',
        ]);

        $aadharPath = null;
        if ($request->hasFile('aadhar_document')) {
            $file = $request->file('aadhar_document');
            $aadharPath = $file->store('uploads/archers/aadhar', 'public');
        }

        $archer = Archer::create([
            'first_name' => $request->first_name,
            'surname' => $request->surname,
            'father_name' => $request->father_name,
            'mother_name' => $request->mother_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'alternate_phone' => $request->alternate_phone,
            'whatsapp_number' => $request->whatsapp_number,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'marital_status' => $request->marital_status,
            'is_minor' => $request->boolean('is_minor'),
            'aadhar_card_number' => $request->aadhar_card_number,
            'aadhar_document' => $aadharPath,
            'password' => Hash::make($request->password),
            'status' => 0,
        ]);

        $application = ArcherApplication::create([
            'archer_id' => $archer->id,
            'application_number' => strtoupper(Str::random(10)),
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'redirect' => route('archer.login'),
        ]);
    }

    public function dashboard()
    {
        $archer = Auth::guard('archer')->user();
        $applications = \App\Models\ArcherApplication::where('archer_id', $archer->id)->latest()->get();

        return view('frontend.pages.student-dashboard', compact('archer', 'applications'));
    }

    public function applications()
    {
        $archer = Auth::guard('archer')->user();
        $applications = \App\Models\ArcherApplication::where('archer_id', $archer->id)->latest()->get();

        return view('frontend.pages.student-applications-list', compact('applications'));
    }

    public function logout(Request $request)
    {
        Auth::guard('archer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('archer.login')->with('success', 'Logged out successfully.');
    }
}
