<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use File;
use Image;
use Storage;

class AdminController extends Controller {

    private $ADMIN_ROUTE_NAME;
    protected $currentUrl;

    public function __construct(){
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        $this->currentUrl = url()->current();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = User::query()
                    ->leftJoin('model_has_roles', function ($join) {
                        $join->on('users.id', '=', 'model_has_roles.model_id')
                            ->where('model_has_roles.model_type', '=', User::class);
                    })
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('users.id', '!=', 1)
                    ->select([
                        'users.id',
                        'users.name',
                        'users.email',
                        'users.phone',
                        'users.status',
                        'users.image',
                        'users.created_at',
                        'users.last_login_at',
                        'roles.name as role_name',
                    ]);

                // Role filtering support
                if ($request->has('role') && !empty($request->role)) {
                    $query->where('roles.name', $request->role);
                }

                // Search support
                if (!empty($request->search['value'])) {
                    $search = $request->search['value'];

                    $query->where(function ($q) use ($search) {
                        $q->where('users.name', 'like', '%' . $search . '%')
                          ->orWhere('users.email', 'like', '%' . $search . '%')
                          ->orWhere('users.phone', 'like', '%' . $search . '%')
                          ->orWhere('roles.name', 'like', '%' . $search . '%');
                    });
                }

                return DataTables::of($query)
                    ->addIndexColumn()
                    ->editColumn('details', function ($row) {
                        $name = !empty($row->name) ? e($row->name) : 'N/A';
                        $email = !empty($row->email) ? e($row->email) : 'N/A';
                        $phone = !empty($row->phone) ? e($row->phone) : 'N/A';
                        $imagePath = $row->image['path'] ?? asset('images/default-user.png');

                        return '
                            <div class="d-flex align-items-center gap-2">
                                <img src="' . $imagePath . '" alt="' . $name . '" class="rounded-circle" width="50" height="50">
                                <div class="d-flex flex-column">
                                    <div><strong>Name:</strong> ' . $name . '</div>
                                    <div><strong>Email:</strong> ' . $email . '</div>
                                    <div><strong>Phone:</strong> ' . $phone . '</div>
                                </div>
                            </div>
                        ';
                    })
                    ->editColumn('role', fn($row) => e($row->role_name ?? 'N/A'))
                    ->addColumn('status', function ($row) {
                        return ($row->status == 1)
                            ? '<span class="badge bg-label-primary me-1">Active</span>'
                            : '<span class="badge bg-label-danger me-1">Inactive</span>';
                    })
                    ->editColumn('created_at', fn($row) => optional($row->created_at)->format('d, M Y h:i A'))
                    ->editColumn('last_login_at', fn($row) => optional($row->last_login_at)->format('d, M Y h:i A'))
                    ->addColumn('action', function ($row) {
                        $encryptedId = CustomHelper::encrypt($row->id);
                        $deleteUrl = route($this->ADMIN_ROUTE_NAME . '.users.delete', $encryptedId);

                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a 
                                        class="dropdown-item waves-effect open-user-modal" 
                                        href="javascript:void(0);"
                                        data-id="' . $encryptedId . '"
                                        data-mode="edit"
                                        data-title="Edit User"
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#add-new-record"
                                    >
                                        <i class="icon-base ti tabler-pencil me-1"></i> Edit
                                    </a>
                                    <a 
                                        class="dropdown-item waves-effect btn-delete-user"
                                        href="javascript:void(0);"
                                        data-url="' . $deleteUrl . '"
                                    >
                                        <i class="icon-base ti tabler-trash me-1"></i> Delete
                                    </a>
                                </div>
                            </div>
                        ';
                    })
                    ->orderColumn('details', function ($query, $order) {
                        $query->orderBy('users.name', $order);
                    })
                    ->orderColumn('role', function ($query, $order) {
                        $query->orderBy('roles.name', $order);
                    })
                    ->rawColumns(['action', 'status', 'details'])
                    ->make(true);
            } catch (\Exception $e) {
                \Log::error('UserController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
            }
        }

        $roles = Role::all();
        $currentRole = $request->get('role');
        return view('admin.admins.index', compact('roles', 'currentRole'));
    }


    public function add(Request $request)
    {
        try {
            $id = !empty($request->id) ? CustomHelper::decrypt($request->id) : 0;
            $user = $id ? User::find($id) : null;

            if ($id && empty($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No user found for edit.'
                ], 404);
            }

            if ($request->isMethod('post')) {
                $rules = [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email,' . $id,
                    'status' => 'required',
                    'role_id' => 'required',
                    'address' => 'nullable|max:255',
                    'phone' => [
                        'required',
                        'regex:/^\+?[0-9]{10,14}$/'
                    ],
                ];

                if (!$id || $request->filled('password')) {
                    $rules['password'] = [
                        'required',
                        'confirmed',
                        'string',
                        'min:8',
                        'max:20',
                        'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,20}$/'
                    ];
                }

                $messages = [
                    'phone.regex' => 'Phone number must be 10-14 digits and may start with +.',
                    'role_id.required' => 'Role is required',
                    'password.regex' => 'Password must include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
                ];

                $validator = Validator::make($request->all(), $rules, $messages);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation errors occurred.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $savedUserId = $this->save($request, $id);

                if ($savedUserId) {
                    $actionType = $id ? 'Edit User' : 'Add User';
                    $desc = ($id ? 'Edit' : 'Add') . " ({$request->name})";
                    $description = $desc . ' ' . json_encode($request->except(['_token', 'password', 'password_confirmation', 'id','cropped_image','remove_image']));

                    CustomHelper::recordActionLog(
                        $this->currentUrl,
                        'users',
                        $savedUserId,
                        $actionType,
                        $desc,
                        $description
                    );

                    return response()->json([
                        'status' => true,
                        'message' => $id ? 'User has been updated successfully.' : 'User has been added successfully.'
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong. Please try again later.'
                ], 500);
            }

            return response()->json([
                'status' => false,
                'message' => 'Invalid request method.'
            ], 405);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUser(Request $request)
    {
        try {
            $id = !empty($request->id) ? CustomHelper::decrypt($request->id) : null;

            if ($request->isMethod('post')) {
                if (is_numeric($id) && $id > 0) {
                    $user = User::find($id);

                    if ($user) {
                        return response()->json([
                            'status' => true,
                            'message' => 'User fetched successfully.',
                            'data' => $user
                        ], 200);
                    }

                    return response()->json([
                        'status' => false,
                        'message' => 'User not found.'
                    ], 404);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid user ID.'
                ], 400);
            }

            return response()->json([
                'status' => false,
                'message' => 'Invalid request method.'
            ], 405);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request){

        $id = !empty($request->id) ? CustomHelper::decrypt($request->id) : "";
        $method = $request->method();
        $is_deleted = 0;

        if($method == 'POST'){
            if(is_numeric($id) && $id > 0){
                $userObj = User::findOrFail($id);
                $function_name = $this->currentUrl;
                $action_table = "users";
                $row_id = $id;
                $action_type = "Delete User";
                $action_description = "Delete(ID : ".$userObj->id.", Name : " . $userObj->name . ", Email :". $userObj->email .")";
                $description = "Delete(ID : ".$userObj->id.", Name : " . $userObj->name . ", Email :". $userObj->email .")";

                if (!empty($userObj->image) && file_exists(public_path('storage/uploads/users/' . $userObj->image['name']))) {
                    unlink(public_path('storage/uploads/users/' . $userObj->image['name']));
                }                

                $is_deleted = $userObj->delete();
            }
        }

        CustomHelper::recordActionLog(
            $function_name,
            $action_table,
            $row_id,
            $action_type,
            $action_description,
            $description
        );

        return response()->json(['status' => true, 'message' => 'User deleted successfully']);
    }

    public function save(Request $request, $id=0){

        $data = $request->except(['_token', 'back_url','password','password_confirmation','cropped_image','remove_image']);

        if(!empty($request->password)){
            $data['password'] = bcrypt($request->password);
        }

        $admin = new User;

        if(is_numeric($id) && $id > 0){
            $exist = User::find($id);

            if(isset($exist->id) && $exist->id == $id){
                $admin = $exist;

                if ($request->input('remove_image') == 1) {
                    if (!empty($admin->image['name']) && file_exists(public_path('storage/uploads/users/' . $admin->image['name']))) {
                        unlink(public_path('storage/uploads/users/' . $admin->image['name']));
                    }

                    $data['image'] = null;
                }

            }
        }

        if ($request->filled('cropped_image')) {
            $imageData = $request->input('cropped_image');

            // Decode base64
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'user_' . time() . '.png';
            Storage::disk('public')->put('uploads/users/' . $imageName, base64_decode($image));

            // Remove old image if exists
            if (!empty($admin->image['name']) && file_exists(public_path('storage/uploads/users/' . $admin->image['name']))) {
                unlink(public_path('storage/uploads/users/' . $admin->image['name']));
            }

            $data['image'] = $imageName;
        }

        foreach($data as $key=>$val){
            $admin->$key = $val;
        }

        $isSaved = $admin->save();

        if($isSaved){
            if ($request->filled('role_id')) {
                $role = Role::findById($request->role_id);
                if ($role) {
                    $admin->syncRoles([$role->name]);
                }
            }
        }

        return $admin->id;
    }
    /* End of controller */
}