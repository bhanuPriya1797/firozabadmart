<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route($ADMIN_ROUTE_NAME . '.login');
        }

        $user = Auth::user();

        // SuperAdmin has all permissions
        if ($user->hasRole('SuperAdmin')) {
            return $next($request);
        }

        // Check if user has the required permission and it's active
        if (!$user->can($permission)) {
            abort(403, 'You do not have permission to access this resource.');
        }
        
        // Additional check: ensure the permission is active
        $permissionModel = \App\Models\CustomPermission::where('name', $permission)->first();
        if (!$permissionModel || $permissionModel->status != 1) {
            abort(403, 'This permission is currently inactive.');
        }

        return $next($request);
    }
}