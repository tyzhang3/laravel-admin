<?php

namespace Encore\Admin\Auth\Database;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            DB::table(config('admin.database.role_users_table'))->truncate();
            DB::table(config('admin.database.role_permissions_table'))->truncate();
            DB::table(config('admin.database.role_menu_table'))->truncate();
            DB::table(config('admin.database.user_permissions_table'))->truncate();

            // create a user.
            $administrator = AdminDefaults::administrator();
            Administrator::truncate();
            $adminUser = Administrator::create([
                'username' => $administrator['username'],
                'password' => Hash::make($administrator['password']),
                'name'     => $administrator['name'],
            ]);

            // create a role.
            $role = AdminDefaults::role();
            Role::truncate();
            $administratorRole = Role::create([
                'name' => $role['name'],
                'slug' => $role['slug'],
            ]);

            // add role to user.
            $adminUser->roles()->save($administratorRole);

            // create permissions.
            Permission::truncate();
            Permission::insert(AdminDefaults::permissions());

            // 为管理员角色分配所有权限
            $administratorRole->permissions()->attach(Permission::all()->pluck('id')->toArray());

            // add default menus.
            Menu::truncate();

            $adminMenu = null;
            foreach (AdminDefaults::rootMenus() as $menu) {
                $created = Menu::create(array_merge(['parent_id' => 0], $menu));

                if ($menu['uri'] === '' && $menu['title'] === 'Admin') {
                    $adminMenu = $created;
                }
            }

            if ($adminMenu) {
                foreach (AdminDefaults::adminChildMenus() as $menu) {
                    Menu::create(array_merge(['parent_id' => $adminMenu->id], $menu));
                }

                // add role to menu.
                $adminMenu->roles()->save($administratorRole);
            }

            // 验证权限分配
            $this->verifyPermissions($adminUser, $administratorRole);

            echo "Laravel Admin 权限设置完成！\n";
            echo "用户名: admin\n";
            echo "密码: admin\n";
            echo "角色: Administrator\n";
            echo "权限数量: ".$administratorRole->permissions()->count()."\n";
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
