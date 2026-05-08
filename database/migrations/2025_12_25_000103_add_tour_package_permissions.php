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
            ['name' => 'tour_packages.view', 'guard_name' => 'web', 'group_name' => 'tour_packages', 'status' => 1],
            ['name' => 'tour_packages.create', 'guard_name' => 'web', 'group_name' => 'tour_packages', 'status' => 1],
            ['name' => 'tour_packages.edit', 'guard_name' => 'web', 'group_name' => 'tour_packages', 'status' => 1],
            ['name' => 'tour_packages.delete', 'guard_name' => 'web', 'group_name' => 'tour_packages', 'status' => 1],
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
            $editorPerms = ['tour_packages.view', 'tour_packages.create', 'tour_packages.edit'];
            foreach ($editorPerms as $p) {
                $editor->givePermissionTo($p);
            }
        }

        $viewer = Role::where('name', 'Viewer')->first();
        if ($viewer) {
            $viewer->givePermissionTo('tour_packages.view');
        }
    }

    public function down(): void
    {
        $names = ['tour_packages.view','tour_packages.create','tour_packages.edit','tour_packages.delete'];
        foreach ($names as $n) {
            DB::table('permissions')->where('name', $n)->delete();
        }
    }
};

