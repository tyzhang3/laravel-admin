<?php

namespace Encore\Admin\Auth\Database;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            // create a user.
            Administrator::truncate();
            $adminUser = Administrator::create([
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'name'     => 'Administrator',
            ]);

            // create a role.
            Role::truncate();
            $administratorRole = Role::create([
                'name' => 'Administrator',
                'slug' => 'administrator',
            ]);

            // add role to user.
            $adminUser->roles()->save($administratorRole);

        //create a permission
        Permission::truncate();
        // 创建所有权限
        $permissions = [
            [
                'name'        => 'All permission',
                'slug'        => '*',
                'http_method' => '',
                'http_path'   => '*',
            ],
            [
                'name'        => 'Dashboard',
                'slug'        => 'dashboard',
                'http_method' => 'GET',
                'http_path'   => '/',
            ],
            [
                'name'        => 'Login',
                'slug'        => 'auth.login',
                'http_method' => '',
                'http_path'   => "/auth/login\r\n/auth/logout",
            ],
            [
                'name'        => 'User setting',
                'slug'        => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path'   => '/auth/setting',
            ],
            [
                'name'        => 'Users management',
                'slug'        => 'auth.users',
                'http_method' => '',
                'http_path'   => "/auth/users*\r\n/auth/users/*",
            ],
            [
                'name'        => 'Roles management',
                'slug'        => 'auth.roles',
                'http_method' => '',
                'http_path'   => "/auth/roles*\r\n/auth/roles/*",
            ],
            [
                'name'        => 'Permissions management',
                'slug'        => 'auth.permissions',
                'http_method' => '',
                'http_path'   => "/auth/permissions*\r\n/auth/permissions/*",
            ],
            [
                'name'        => 'Menu management',
                'slug'        => 'auth.menu',
                'http_method' => '',
                'http_path'   => "/auth/menu*\r\n/auth/menu/*",
            ],
            [
                'name'        => 'Operation log',
                'slug'        => 'auth.logs',
                'http_method' => '',
                'http_path'   => "/auth/logs*\r\n/auth/logs/*",
            ],
        ];

        Permission::insert($permissions);

        // 为管理员角色分配所有权限
        $administratorRole = Role::first();
        $administratorRole->permissions()->attach(Permission::all()->pluck('id')->toArray());

        // add default menus.
        Menu::truncate();
        Menu::insert([
            [
                'parent_id' => 0,
                'order'     => 1,
                'title'     => 'Dashboard',
                'icon'      => 'fa-bar-chart',
                'uri'       => '/',
            ],
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => 'Admin',
                'icon'      => 'fa-tasks',
                'uri'       => '',
            ],
            [
                'parent_id' => 2,
                'order'     => 3,
                'title'     => 'Users',
                'icon'      => 'fa-users',
                'uri'       => 'auth/users',
            ],
            [
                'parent_id' => 2,
                'order'     => 4,
                'title'     => 'Roles',
                'icon'      => 'fa-user',
                'uri'       => 'auth/roles',
            ],
            [
                'parent_id' => 2,
                'order'     => 5,
                'title'     => 'Permission',
                'icon'      => 'fa-ban',
                'uri'       => 'auth/permissions',
            ],
            [
                'parent_id' => 2,
                'order'     => 6,
                'title'     => 'Menu',
                'icon'      => 'fa-bars',
                'uri'       => 'auth/menu',
            ],
            [
                'parent_id' => 2,
                'order'     => 7,
                'title'     => 'Operation log',
                'icon'      => 'fa-history',
                'uri'       => 'auth/logs',
            ],
        ]);

        // add role to menu.
        $adminMenu = Menu::find(2);
        if ($adminMenu) {
            $adminMenu->roles()->save($administratorRole);
        }

        // 验证权限分配
        $this->verifyPermissions($adminUser, $administratorRole);
        
        echo "Laravel Admin 权限设置完成！\n";
        echo "用户名: admin\n";
        echo "密码: admin\n";
        echo "角色: Administrator\n";
        echo "权限数量: " . $administratorRole->permissions()->count() . "\n";
        
        } catch (\Exception $e) {
            echo "权限设置失败: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    /**
     * 验证权限分配是否正确
     */
    private function verifyPermissions($user, $role)
    {
        // 验证用户是否拥有角色
        if (!$user->roles()->where('id', $role->id)->exists()) {
            throw new \Exception("用户角色分配失败");
        }

        // 验证角色是否拥有权限
        if ($role->permissions()->count() === 0) {
            throw new \Exception("角色权限分配失败");
        }

        // 验证用户是否为管理员
        if (!$user->isRole('administrator')) {
            throw new \Exception("管理员权限验证失败");
        }

        echo "权限验证通过！\n";
    }
}
