<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAssignPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // Kita definisikan command kita untuk menerima nama role dan nama permission
    protected $signature = 'permission:assign {role} {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a specific permission to a role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleName = $this->argument('role');
        $permissionName = $this->argument('permission');

        // Cari Role berdasarkan nama
        $role = Role::where('name', $roleName)->first();
        if (! $role) {
            $this->error("Role '{$roleName}' tidak ditemukan!");
            return 1;
        }

        // Cari Permission berdasarkan nama
        $permission = Permission::where('name', $permissionName)->first();
        if (! $permission) {
            $this->error("Permission '{$permissionName}' tidak ditemukan!");
            return 1;
        }
        
        // Cek apakah role sudah punya permission tersebut
        if ($role->hasPermissionTo($permission)) {
            $this->info("Role '{$roleName}' sudah memiliki permission '{$permissionName}'. Tidak ada perubahan.");
            return 0;
        }

        // Berikan permission ke role
        $role->givePermissionTo($permission);

        $this->info("Sukses! Permission '{$permissionName}' berhasil diberikan kepada role '{$roleName}'.");

        return 0;
    }
}