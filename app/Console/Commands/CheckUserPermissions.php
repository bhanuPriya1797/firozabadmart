<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:check {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user permissions and roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return;
            }
            
            $this->info("User: {$user->name} ({$user->email})");
            $this->info("Roles: " . $user->getRoleNames()->implode(', '));
            $this->info("Permissions: " . $user->getAllPermissions()->pluck('name')->implode(', '));
        } else {
            $this->info("=== ROLES & PERMISSIONS SUMMARY ===");
            $this->info("Total Roles: " . Role::count());
            $this->info("Total Permissions: " . Permission::count());
            $this->newLine();
            
            $this->info("=== ROLES ===");
            foreach (Role::all() as $role) {
                $this->info("- {$role->name}: {$role->permissions->count()} permissions");
            }
            
            $this->newLine();
            $this->info("=== USERS ===");
            foreach (User::all() as $user) {
                $this->info("- {$user->name} ({$user->email}): " . $user->getRoleNames()->implode(', '));
            }
        }
    }
}