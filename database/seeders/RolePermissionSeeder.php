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
            'approve_leave_kadiv',
            'approve_leave_hr',
            'approve_leave_direksi',

            // Role permissions
            'view_role',
            'view_any_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',

            // Payroll permissions
            'view_payroll',
            'view_any_payroll',
            'create_payroll',
            'view_salary_setting',
            'update_salary_setting',

            // Division permissions
            'view_division',
            'view_any_division',
            'create_division',
            'update_division',
            'delete_division',

            // Setting permissions
            'view_setting',
            'view_any_setting',
            'update_setting',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ===== SUPER ADMIN - Full access =====
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // ===== ADMIN / HR - Manage master data, approve leave layer 2, manage payroll =====
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view_user', 'view_any_user', 'create_user', 'update_user',
            'view_office', 'view_any_office', 'create_office', 'update_office',
            'view_shift', 'view_any_shift', 'create_shift', 'update_shift',
            'view_schedule', 'view_any_schedule', 'create_schedule', 'update_schedule',
            'view_attendance', 'view_any_attendance',
            'view_leave', 'view_any_leave', 'update_leave', 'approve_leave_hr',
            'view_payroll', 'view_any_payroll', 'create_payroll',
            'view_salary_setting', 'update_salary_setting',
            'view_division', 'view_any_division', 'create_division', 'update_division', 'delete_division',
            'view_setting', 'view_any_setting', 'update_setting',
        ]);

        // ===== DIREKSI - Final approval leave, view reports =====
        $direksiRole = Role::firstOrCreate(['name' => 'direksi']);
        $direksiRole->givePermissionTo([
            'view_attendance', 'view_any_attendance',
            'view_leave', 'view_any_leave', 'approve_leave_direksi',
            'view_payroll', 'view_any_payroll',
            'view_user', 'view_any_user',
        ]);

        // ===== KEPALA DIVISI - Approve leave layer 1, view team attendance =====
        $kadivRole = Role::firstOrCreate(['name' => 'kepala_divisi']);
        $kadivRole->givePermissionTo([
            'view_attendance', 'view_any_attendance',
            'view_leave', 'view_any_leave', 'approve_leave_kadiv',
            'view_payroll',
            'view_schedule',
            'view_user', 'view_any_user',
        ]);

        // ===== EMPLOYEE - Self-service only =====
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view_attendance',
            'create_attendance',
            'view_leave',
            'create_leave',
            'view_schedule',
            'view_payroll',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Roles: super_admin, admin, direksi, kepala_divisi, employee');
    }
}
