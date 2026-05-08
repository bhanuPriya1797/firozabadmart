<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class CustomPermission extends SpatiePermission
{
    /**
     * Scope to get only active permissions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get only inactive permissions
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Check if permission is active
     */
    public function isActive()
    {
        return $this->status == 1;
    }

    /**
     * Check if permission is inactive
     */
    public function isInactive()
    {
        return $this->status == 0;
    }
}