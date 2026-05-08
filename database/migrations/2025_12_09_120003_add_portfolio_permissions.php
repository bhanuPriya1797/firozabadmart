<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\CustomPermission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'portfolios.view', 'guard_name' => 'web', 'group_name' => 'portfolios', 'status' => 1],
            ['name' => 'portfolios.create', 'guard_name' => 'web', 'group_name' => 'portfolios', 'status' => 1],
            ['name' => 'portfolios.edit', 'guard_name' => 'web', 'group_name' => 'portfolios', 'status' => 1],
            ['name' => 'portfolios.delete', 'guard_name' => 'web', 'group_name' => 'portfolios', 'status' => 1],
            ['name' => 'portfolios.publish', 'guard_name' => 'web', 'group_name' => 'portfolios', 'status' => 1],
        ];

        foreach ($permissions as $perm) {
            $exists = CustomPermission::where('name', $perm['name'])->first();
            if (!$exists) {
                CustomPermission::create($perm);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Role::where('name', 'SuperAdmin')->first();
        if ($superAdmin) {
            foreach ($permissions as $perm) {
                $superAdmin->givePermissionTo($perm['name']);
            }
        }

        $editor = Role::where('name', 'Editor')->first();
        if ($editor) {
            $editorPerms = ['portfolios.view', 'portfolios.create', 'portfolios.edit', 'portfolios.publish'];
            foreach ($editorPerms as $p) {
                $editor->givePermissionTo($p);
            }
        }

        $viewer = Role::where('name', 'Viewer')->first();
        if ($viewer) {
            $viewer->givePermissionTo('portfolios.view');
        }
    }

    public function down(): void
    {
        $names = ['portfolios.view','portfolios.create','portfolios.edit','portfolios.delete','portfolios.publish'];
        foreach ($names as $n) {
            DB::table('permissions')->where('name', $n)->delete();
        }
    }
};
