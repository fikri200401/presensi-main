<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view_user',
            'view_any_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',

            // Office permissions
            'view_office',
            'view_any_office',
            'create_office',
            'update_office',
            'delete_office',
            'delete_any_office',

            // Shift permissions
            'view_shift',
            'view_any_shift',
            'create_shift',
            'update_shift',
            'delete_shift',
            'delete_any_shift',

            // Schedule permissions
            'view_schedule',
            'view_any_schedule',
            'create_schedule',
            'update_schedule',
            'delete_schedule',
            'delete_any_schedule',

            // Attendance permissions
            'view_attendance',
            'view_any_attendance',
            'create_attendance',
            'update_attendance',
            'delete_attendance',
            'delete_any_attendance',

            // Leave permissions
            'view_leave',
            'view_any_leave',
            'create_leave',
            'update_leave',
            'delete_leave',
            'delete_any_leave',

            // Role permissions
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view_user', 'view_any_user', 'create_user', 'update_user',
            'view_office', 'view_any_office', 'create_office', 'update_office',
            'view_shift', 'view_any_shift', 'create_shift', 'update_shift',
            'view_schedule', 'view_any_schedule', 'create_schedule', 'update_schedule',
            'view_attendance', 'view_any_attendance',
            'view_leave', 'view_any_leave', 'update_leave',
        ]);

        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view_attendance',
            'create_attendance',
            'view_leave',
            'create_leave',
            'view_schedule',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
