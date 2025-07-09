<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Baris ini penting untuk reset cache roles dan permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- TAHAP 1: MEMBUAT SEMUA IZIN (PERMISSIONS) ---
        // Izin untuk Dashboard
        Permission::create(['name' => 'view dashboard']);
        Permission::create(['name' => 'view summary dashboard']);

        // Izin untuk Kas & Transaksi
        Permission::create(['name' => 'view transactions']);
        Permission::create(['name' => 'create transactions']);
        Permission::create(['name' => 'edit transactions']);
        Permission::create(['name' => 'delete transactions']);
        
        // Izin untuk Kategori
        Permission::create(['name' => 'manage categories']);

        // Izin untuk Anggota
        Permission::create(['name' => 'view members']);
        Permission::create(['name' => 'create members']);
        Permission::create(['name' => 'edit members']);
        Permission::create(['name' => 'delete members']);

        // Izin untuk Fitur Khusus
        Permission::create(['name' => 'validate dues']);
        Permission::create(['name' => 'print reports']);
        Permission::create(['name' => 'view own dues']);


        // --- TAHAP 2: MEMBUAT SEMUA PERAN (ROLES) & MEMBERIKAN IZIN ---
        // Peran: Admin (diberi semua izin yang ada)
        $TestUser = Role::create(['name' => 'super_admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Peran: Ketua
        $ketuaRole = Role::create(['name' => 'Ketua']);
        $ketuaRole->givePermissionTo([
            'view dashboard',
            'view transactions',
            'view members',
            'print reports',
        ]);

        // Peran: Bendahara
        $bendaharaRole = Role::create(['name' => 'Bendahara']);
        $bendaharaRole->givePermissionTo([
            'view dashboard',
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'manage categories',
            'validate dues',
            'print reports',
        ]);

        // Peran: Sekretaris
        $sekretarisRole = Role::create(['name' => 'Sekretaris']);
        $sekretarisRole->givePermissionTo([
            'view dashboard',
            'view members',
            'create members',
            'edit members',
            'delete members',
            'print reports',
        ]);

        // Peran: Anggota
        $anggotaRole = Role::create(['name' => 'Anggota']);
        $anggotaRole->givePermissionTo([
            'view summary dashboard',
            'view own dues',
        ]);
    }
}