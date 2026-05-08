<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Models\CustomPermission as Permission;
use Illuminate\Support\Carbon;
use Session;

class RolesController extends Controller
{
    public $user;
    private $ADMIN_ROUTE_NAME;
    protected $currentUrl;

    public function __construct(){
        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        $this->currentUrl = url()->current();
        $this->user = Auth::user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all()->map(function ($role) {
            $userCount = DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->where('model_type', \App\Models\User::class)
                ->count();

            $role->user_count = $userCount;
            return $role;
        });
        $all_permissions = Permission::where('status', 1)->get(); // Only active permissions
        $permission_groups = User::getPermissionGroups();
        return view('admin.roles.index', compact('roles', 'all_permissions', 'permission_groups'));
    }

    public function getRole(Request $request)
    {
        try {
            $id = !empty($request->id) ? CustomHelper::decrypt($request->id) : null;

            if ($request->isMethod('post')) {
                if (is_numeric($id) && $id > 0) {
                    $role = Role::find($id);

                    if ($role) {
                        // Fetch permissions as array of names
                        $permissions = $role->permissions()
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray();

                        return response()->json([
                            'status' => true,
                            'message' => 'Role fetched successfully.',
                            'data' => [
                                'name' => $role->name,
                                'permissions' => $permissions
                            ]
                        ], 200);
                    }

                    return response()->json([
                        'status' => false,
                        'message' => 'Role not found.'
                    ], 404);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Role ID.'
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

    public function save(Request $request)
    {
        $isAjax = $request->ajax();
        $response = ['status' => false, 'message' => 'Something went wrong.'];
        $id = !empty($request->id) ? CustomHelper::decrypt($request->id) : 0;

        try {
            $rules = [
                'name' => 'required|max:100|unique:roles,name' . ($id ? ',' . $id : '')
            ];

            $messages = [
                'name.required' => 'Please provide a role name.'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'name' => $request->name,
                'guard_name' => 'web'
            ];
            //prd($request->input('permissions', []));
            if ($id) {
                $role = Role::find($id);
                $role->update($data);
            } else {
                $role = Role::create($data);
            }

            // Update Log
            $actionType = $id ? 'Edit Role' : 'Add Role';
            $desc = ($id ? 'Edit' : 'Add') . " ({$request->name})";
            $description = $desc . ' ' . json_encode($request->except(['_token', 'permissions', 'id']));
            $row_id = ($id ? $id : $role->id);
            CustomHelper::recordActionLog(
                $this->currentUrl,
                'roles',
                $row_id,
                $actionType,
                $desc,
                $description
            );

            // Sync permissions
            $permissions = $request->input('permissions', []);
            $role->syncPermissions($permissions);

            $successMessage = $id ? 'Role updated successfully.' : 'Role created successfully.';
            return response()->json(['status' => true, 'message' => $successMessage]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $id = !empty($request->id) ? CustomHelper::decrypt($request->id) : "";
        $method = $request->method();
        $is_deleted = 0;

        if($method == 'POST'){
            if(is_numeric($id) && $id > 0){
                $role = Role::find($id);
                if (!is_null($role)) {
                    $is_deleted = $role->delete();
                }
            }
        }

        //LogHelper::log('Delete Role', Session::get('organization_id'), 'Role', "Deleted Role: ID: {$id}, Name: {$role->name}");
        // Update Log
        $actionType = 'Delete Role';
        $desc = 'Delete Role' . " ({$role->name})";
        $description = $desc;        
        CustomHelper::recordActionLog(
            $this->currentUrl,
            'roles',
            $id,
            $actionType,
            $desc,
            $description
        );

        return response()->json(['status' => true, 'message' => 'Role deleted successfully']);
    }
}
