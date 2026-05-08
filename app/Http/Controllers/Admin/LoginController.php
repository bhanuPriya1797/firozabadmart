<?php

namespace App\Http\Controllers\Admin;

use App\Models\User as Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\CustomHelper;

class LoginController extends Controller{

    public function index(Request $request)
    {
        $ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        if (auth()->check()) {
            return redirect()->route($ADMIN_ROUTE_NAME . '.index');
        }

        if ($request->isMethod('post')) {
            try {
                $validator = Validator::make($request->all(), [
                    'username' => 'required|email',
                    'password' => 'required',
                ], [
                    'username.required' => 'Email is required.',
                    'username.email'    => 'Please enter a valid email address.',
                    'password.required' => 'Password is required.',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                if (Auth::attempt([
                    'email' => $request->input('username'),
                    'password' => $request->input('password')
                ])) {
                    $user = Auth::user();
                    $user->last_login_at = now();
                    $user->save();
                    return redirect()->route($ADMIN_ROUTE_NAME . '.index');
                } else {
                    return back()->with('error', 'Invalid username or password.')->withInput();
                }

            } catch (\Exception $e) {
                return back()->with('error', 'Login failed: ' . $e->getMessage());
            }
        }

        return view('admin.login.index');
    }

    public function logout(Request $request)
    {
        $ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route($ADMIN_ROUTE_NAME . '.index');
    }

/*End of controller */
}