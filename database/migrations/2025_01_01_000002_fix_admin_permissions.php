<?php

use Illuminate\Database\Migrations\Migration;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Auth\Database\Permission;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 确保管理员用户存在
        $adminUser = Administrator::where('username', 'admin')->first();
        if (!$adminUser) {
            $adminUser = Administrator::create([
                'username' => 'admin',
                'password' => bcrypt('admin'),
                'name' => 'Administrator',
            ]);
        }

        // 确保管理员角色存在
        $administratorRole = Role::where('slug', 'administrator')->first();
        if (!$administratorRole) {
            $administratorRole = Role::create([
                'name' => 'Administrator',
                'slug' => 'administrator',
            ]);
        }

        // 确保用户拥有角色
        if (!$adminUser->roles()->where('id', $administratorRole->id)->exists()) {
            $adminUser->roles()->attach($administratorRole->id);
        }

        // 创建或更新权限
        $permissions = [
            [
                'name' => 'All permission',
                'slug' => '*',
                'http_method' => '',
                'http_path' => '*',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'http_method' => 'GET',
                'http_path' => '/',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Login',
                'slug' => 'auth.login',
                'http_method' => '',
                'http_path' => "/auth/login\n/auth/logout",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User setting',
                'slug' => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path' => '/auth/setting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Users management',
                'slug' => 'auth.users',
                'http_method' => '',
                'http_path' => "/auth/users*\n/auth/users/*",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Roles management',
                'slug' => 'auth.roles',
                'http_method' => '',
                'http_path' => "/auth/roles*\n/auth/roles/*",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Permissions management',
                'slug' => 'auth.permissions',
                'http_method' => '',
                'http_path' => "/auth/permissions*\n/auth/permissions/*",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Menu management',
                'slug' => 'auth.menu',
                'http_method' => '',
                'http_path' => "/auth/menu*\n/auth/menu/*",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operation log',
                'slug' => 'auth.logs',
                'http_method' => '',
                'http_path' => "/auth/logs*\n/auth/logs/*",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // 批量插入或更新权限
        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // 为管理员角色分配所有权限
        $allPermissionIds = Permission::all()->pluck('id')->toArray();
        $administratorRole->permissions()->sync($allPermissionIds);

        // 清理可能存在的重复权限分配
        DB::table(config('admin.database.user_permissions_table'))
            ->where('user_id', $adminUser->id)
            ->delete();

        echo "权限修复完成！\n";
        echo "管理员用户已拥有所有权限。\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 清理管理员权限分配
        $adminUser = Administrator::where('username', 'admin')->first();
        $administratorRole = Role::where('slug', 'administrator')->first();
        
        if ($adminUser && $administratorRole) {
            $adminUser->roles()->detach($administratorRole->id);
            $administratorRole->permissions()->detach();
        }
    }
};