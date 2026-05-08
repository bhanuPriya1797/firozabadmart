<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Helpers\CustomHelper;
use Spatie\Permission\Models\Role;
use File;
use Image;

class ProfileController extends Controller
{
    private $ADMIN_ROUTE_NAME;
    protected $currentUrl;

    public function __construct(){
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        $this->currentUrl = url()->current();
    }

    public function index()
    {
        $user = Auth::user();
        $roles  = Role::all();
        return view('admin.profile', compact('user','roles'));
    }

    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            $id = $user->id;

            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'phone' => ['nullable', 'regex:/^\+?[0-9]{10,14}$/'],
                'address' => 'nullable|max:255',
            ];

            $messages = [
                'phone.regex' => 'Phone number must be 10-14 digits and may start with +.',
            ];

            if ($request->filled('password')) {
                $rules['password'] = [
                    'required',
                    'confirmed',
                    'string',
                    'min:8',
                    'max:20',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,20}$/'
                ];

                $messages['password.regex'] = 'Password must include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.';
            }

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Update user
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address ?? "";

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            if ($request->filled('cropped_image')) {
                $imageData = $request->input('cropped_image');
                $image = str_replace('data:image/png;base64,', '', $imageData);
                $image = str_replace(' ', '+', $image);
                $imageName = 'user_' . time() . '.png';
                Storage::disk('public')->put('uploads/users/' . $imageName, base64_decode($image));

                // Remove old image if exists
                if (!empty($user->image['name']) && file_exists(public_path('storage/uploads/users/' . $user->image['name']))) {
                    unlink(public_path('storage/uploads/users/' . $user->image['name']));
                }

                $user->image = $imageName;
            }

            if ($request->input('remove_image') == 1) {
                if (!empty($user->image['name']) && file_exists(public_path('storage/uploads/users/' . $user->image['name']))) {
                    unlink(public_path('storage/uploads/users/' . $user->image['name']));
                }
                $user->image = null;
            }

            $user->save();

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'users',
                $user->id,
                'Update Profile',
                'Updated profile: ' . $user->name,
                json_encode($request->except(['password', 'password_confirmation', 'cropped_image', 'remove_image']))
            );

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully.'
            ]);            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
