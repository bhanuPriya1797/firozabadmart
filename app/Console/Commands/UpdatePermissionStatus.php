<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class UpdatePermissionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing permissions status to active';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated = Permission::whereNull('status')->update(['status' => 1]);
        
        $this->info("Updated {$updated} permissions to active status");
        $this->info("Total active permissions: " . Permission::where('status', 1)->count());
        $this->info("Total inactive permissions: " . Permission::where('status', 0)->count());
    }
}