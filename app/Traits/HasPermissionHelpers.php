<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasPermissionHelpers
{
    /**
     * Check if current user has permission
     */
    public function hasPermission($permission)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // SuperAdmin has all permissions
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        return $user->can($permission);
    }

    /**
     * Check if current user has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // SuperAdmin has all permissions
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if current user has all of the given permissions
     */
    public function hasAllPermissions($permissions)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // SuperAdmin has all permissions
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!$user->can($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if current user has specific role
     */
    public function hasRole($role)
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole($role);
    }

    /**
     * Check if current user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAnyRole($roles);
    }
}









