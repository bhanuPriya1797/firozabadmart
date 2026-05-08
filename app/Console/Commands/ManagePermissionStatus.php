<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CustomPermission as Permission;

class ManagePermissionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:manage {action} {permission?} {--group=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage permission status (activate/deactivate)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $permission = $this->argument('permission');
        $group = $this->option('group');

        if (!in_array($action, ['activate', 'deactivate', 'list'])) {
            $this->error('Invalid action. Use: activate, deactivate, or list');
            return;
        }

        if ($action === 'list') {
            $this->listPermissions($group);
            return;
        }

        $status = $action === 'activate' ? 1 : 0;
        $statusText = $action === 'activate' ? 'activated' : 'deactivated';

        if ($permission) {
            // Manage specific permission
            $perm = Permission::where('name', $permission)->first();
            if (!$perm) {
                $this->error("Permission '{$permission}' not found.");
                return;
            }

            $perm->update(['status' => $status]);
            $this->info("Permission '{$permission}' {$statusText} successfully.");
        } elseif ($group) {
            // Manage all permissions in a group
            $permissions = Permission::where('group_name', $group)->get();
            if ($permissions->isEmpty()) {
                $this->error("No permissions found for group '{$group}'.");
                return;
            }

            $updated = Permission::where('group_name', $group)->update(['status' => $status]);
            $this->info("{$updated} permissions in group '{$group}' {$statusText} successfully.");
        } else {
            $this->error('Please specify either a permission name or group name.');
        }
    }

    private function listPermissions($group = null)
    {
        $query = Permission::query();
        
        if ($group) {
            $query->where('group_name', $group);
        }

        $permissions = $query->orderBy('group_name')->orderBy('name')->get();

        if ($permissions->isEmpty()) {
            $this->info('No permissions found.');
            return;
        }

        $this->info('Permission Status:');
        $this->line('');

        $currentGroup = null;
        foreach ($permissions as $permission) {
            if ($currentGroup !== $permission->group_name) {
                $currentGroup = $permission->group_name;
                $this->line("<fg=cyan>{$currentGroup}</fg=cyan>");
            }

            $status = $permission->status == 1 ? '<fg=green>Active</fg=green>' : '<fg=red>Inactive</fg=red>';
            $this->line("  {$permission->name} - {$status}");
        }
    }
}