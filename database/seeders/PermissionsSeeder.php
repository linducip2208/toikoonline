<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Granular store permissions. Run with:
 *
 *   php artisan db:seed --class=Database\\Seeders\\PermissionsSeeder
 */
class PermissionsSeeder extends Seeder
{
    public const PERMISSIONS = [
        // Products
        'view_products', 'create_products', 'update_products', 'delete_products', 'publish_products',
        // Orders
        'view_orders', 'update_orders', 'refund_orders', 'cancel_orders',
        // Operational areas
        'manage_inventory', 'manage_shipping', 'manage_payment',
        'manage_cms', 'manage_pages', 'manage_themes', 'manage_media',
        'manage_settings', 'manage_users', 'manage_roles',
        'manage_api', 'manage_webhooks',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // super_admin: full access
        $superAdmin->syncPermissions(self::PERMISSIONS);

        // admin: everything except role + settings management (super_admin only)
        $admin->syncPermissions(array_values(array_diff(self::PERMISSIONS, [
            'manage_roles', 'manage_settings',
        ])));

        // staff: operational scoped access
        $staff->syncPermissions([
            'view_products',
            'view_orders', 'update_orders',
            'manage_inventory', 'manage_shipping', 'manage_media',
        ]);
    }
}
