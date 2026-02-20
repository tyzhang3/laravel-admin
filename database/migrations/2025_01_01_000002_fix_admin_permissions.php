<?php

use Illuminate\Database\Migrations\Migration;
use Encore\Admin\Auth\Database\AdminDefaults;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Auth\Database\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $administrator = AdminDefaults::administrator();
        $role = AdminDefaults::role();

        // 确保管理员用户存在
        $adminUser = Administrator::where('username', $administrator['username'])->first();
        if (!$adminUser) {
            $adminUser = Administrator::create([
                'username' => $administrator['username'],
                'password' => Hash::make($administrator['password']),
                'name' => $administrator['name'],
            ]);
        }

        // 确保管理员角色存在
        $administratorRole = Role::where('slug', $role['slug'])->first();
        if (!$administratorRole) {
            $administratorRole = Role::create([
                'name' => $role['name'],
                'slug' => $role['slug'],
            ]);
        }

        // 确保用户拥有角色
        if (!$adminUser->roles()->where('id', $administratorRole->id)->exists()) {
            $adminUser->roles()->attach($administratorRole->id);
        }

        // 创建或更新权限
        $now = now();
        $permissions = array_map(function ($permission) use ($now) {
            return array_merge($permission, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, AdminDefaults::permissions());

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
        $administrator = AdminDefaults::administrator();
        $role = AdminDefaults::role();

        $adminUser = Administrator::where('username', $administrator['username'])->first();
        $administratorRole = Role::where('slug', $role['slug'])->first();
        
        if ($adminUser && $administratorRole) {
            $adminUser->roles()->detach($administratorRole->id);
            $administratorRole->permissions()->detach();
        }
    }
};
