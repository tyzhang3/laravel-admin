<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;

class PermissionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testPermissionsIndex()
    {
        $this->assertTrue(Administrator::first()->isAdministrator());

        $this->visit('admin/auth/permissions')
            ->see('Permissions');
    }

    public function testAddAndDeletePermissions()
    {
        $baseCount = Permission::count();

        $this->visit('admin/auth/permissions/create')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-edit', 'name' => 'Can edit', 'http_path' => 'users/1/edit', 'http_method' => ['GET']])
            ->seePageIs('admin/auth/permissions')
            ->visit('admin/auth/permissions/create')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-delete', 'name' => 'Can delete', 'http_path' => 'users/1', 'http_method' => ['DELETE']])
            ->seePageIs('admin/auth/permissions')
            ->seeInDatabase(config('admin.database.permissions_table'), ['slug' => 'can-edit', 'name' => 'Can edit', 'http_path' => 'users/1/edit', 'http_method' => 'GET'])
            ->seeInDatabase(config('admin.database.permissions_table'), ['slug' => 'can-delete', 'name' => 'Can delete', 'http_path' => 'users/1', 'http_method' => 'DELETE'])
            ->assertEquals($baseCount + 2, Permission::count());

        $this->assertTrue(Administrator::first()->can('can-edit'));
        $this->assertTrue(Administrator::first()->can('can-delete'));

        $this->delete('admin/auth/permissions/'.$this->permissionId('can-edit'))
            ->assertEquals($baseCount + 1, Permission::count());

        $this->delete('admin/auth/permissions/'.$this->permissionId('can-delete'))
            ->assertEquals($baseCount, Permission::count());
    }

    public function testAddPermissionToRole()
    {
        $baseCount = Permission::count();

        $this->visit('admin/auth/permissions/create')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-create', 'name' => 'Can Create', 'http_path' => 'users/create', 'http_method' => ['GET']])
            ->seePageIs('admin/auth/permissions');

        $this->assertEquals($baseCount + 1, Permission::count());

        $permissionId = $this->permissionId('can-create');

        $this->visit('admin/auth/roles/1/edit')
            ->see('Edit')
            ->submitForm('Submit', ['permissions' => [$permissionId]])
            ->seePageIs('admin/auth/roles')
            ->seeInDatabase(config('admin.database.role_permissions_table'), ['role_id' => 1, 'permission_id' => $permissionId]);
    }

    public function testAddPermissionToUser()
    {
        $baseCount = Permission::count();

        $this->visit('admin/auth/permissions/create')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-create', 'name' => 'Can Create', 'http_path' => 'users/create', 'http_method' => ['GET']])
            ->seePageIs('admin/auth/permissions');

        $this->assertEquals($baseCount + 1, Permission::count());

        $permissionId = $this->permissionId('can-create');

        $this->visit('admin/auth/users/1/edit')
            ->see('Edit')
            ->submitForm('Submit', ['permissions' => [$permissionId], 'roles' => [1]])
            ->seePageIs('admin/auth/users')
            ->seeInDatabase(config('admin.database.user_permissions_table'), ['user_id' => 1, 'permission_id' => $permissionId])
            ->seeInDatabase(config('admin.database.role_users_table'), ['user_id' => 1, 'role_id' => 1]);
    }

    public function testAddUserAndAssignPermission()
    {
        $baseCount = Permission::count();

        $user = [
            'username'              => 'Test',
            'name'                  => 'Name',
            'password'              => '123456',
            'password_confirmation' => '123456',
        ];

        $this->visit('admin/auth/users/create')
            ->see('Create')
            ->submitForm('Submit', $user)
            ->seePageIs('admin/auth/users')
            ->seeInDatabase(config('admin.database.users_table'), ['username' => 'Test']);

        $this->assertFalse(Administrator::find(2)->isAdministrator());

        $this->visit('admin/auth/permissions/create')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-update', 'name' => 'Can Update', 'http_path' => 'users/*/edit', 'http_method' => ['GET']])
            ->seePageIs('admin/auth/permissions');

        $this->assertEquals($baseCount + 1, Permission::count());

        $this->visit('admin/auth/permissions/create')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-remove', 'name' => 'Can Remove', 'http_path' => 'users/*', 'http_method' => ['DELETE']])
            ->seePageIs('admin/auth/permissions');

        $this->assertEquals($baseCount + 2, Permission::count());

        $canUpdateId = $this->permissionId('can-update');
        $canRemoveId = $this->permissionId('can-remove');

        $this->visit('admin/auth/users/2/edit')
            ->see('Edit')
            ->submitForm('Submit', ['permissions' => [$canUpdateId]])
            ->seePageIs('admin/auth/users')
            ->seeInDatabase(config('admin.database.user_permissions_table'), ['user_id' => 2, 'permission_id' => $canUpdateId]);

        $this->assertTrue(Administrator::find(2)->can('can-update'));
        $this->assertTrue(Administrator::find(2)->cannot('can-remove'));

        $this->visit('admin/auth/users/2/edit')
            ->see('Edit')
            ->submitForm('Submit', ['permissions' => [$canRemoveId]])
            ->seePageIs('admin/auth/users')
            ->seeInDatabase(config('admin.database.user_permissions_table'), ['user_id' => 2, 'permission_id' => $canRemoveId]);

        $this->assertTrue(Administrator::find(2)->can('can-remove'));

        $this->visit('admin/auth/users/2/edit')
            ->see('Edit')
            ->submitForm('Submit', ['permissions' => []])
            ->seePageIs('admin/auth/users')
            ->missingFromDatabase(config('admin.database.user_permissions_table'), ['user_id' => 2, 'permission_id' => $canUpdateId])
            ->missingFromDatabase(config('admin.database.user_permissions_table'), ['user_id' => 2, 'permission_id' => $canRemoveId]);

        $this->assertTrue(Administrator::find(2)->cannot('can-update'));
        $this->assertTrue(Administrator::find(2)->cannot('can-remove'));
    }

    public function testPermissionThroughRole()
    {
        $baseCount = Permission::count();

        $user = [
            'username'              => 'Test',
            'name'                  => 'Name',
            'password'              => '123456',
            'password_confirmation' => '123456',
        ];

        // 1.add a user
        $this->visit('admin/auth/users/create')
            ->see('Create')
            ->submitForm('Submit', $user)
            ->seePageIs('admin/auth/users')
            ->seeInDatabase(config('admin.database.users_table'), ['username' => 'Test']);

        $this->assertFalse(Administrator::find(2)->isAdministrator());

        // 2.add a role
        $this->visit('admin/auth/roles/create')
            ->see('Roles')
            ->submitForm('Submit', ['slug' => 'developer', 'name' => 'Developer...'])
            ->seePageIs('admin/auth/roles')
            ->seeInDatabase(config('admin.database.roles_table'), ['slug' => 'developer', 'name' => 'Developer...'])
            ->assertEquals(2, Role::count());

        $this->assertFalse(Administrator::find(2)->isRole('developer'));

        // 3.assign role to user
        $this->visit('admin/auth/users/2/edit')
            ->see('Edit')
            ->submitForm('Submit', ['roles' => [2]])
            ->seePageIs('admin/auth/users')
            ->seeInDatabase(config('admin.database.role_users_table'), ['user_id' => 2, 'role_id' => 2]);

        $this->assertTrue(Administrator::find(2)->isRole('developer'));

        //  4.add a permission
        $this->visit('admin/auth/permissions/create')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-remove', 'name' => 'Can Remove', 'http_path' => 'users/*', 'http_method' => ['DELETE']])
            ->seePageIs('admin/auth/permissions');

        $this->assertEquals($baseCount + 1, Permission::count());

        $this->assertTrue(Administrator::find(2)->cannot('can-remove'));

        $canRemoveId = $this->permissionId('can-remove');

        // 5.assign permission to role
        $this->visit('admin/auth/roles/2/edit')
            ->see('Edit')
            ->submitForm('Submit', ['permissions' => [$canRemoveId]])
            ->seePageIs('admin/auth/roles')
            ->seeInDatabase(config('admin.database.role_permissions_table'), ['role_id' => 2, 'permission_id' => $canRemoveId]);

        $this->assertTrue(Administrator::find(2)->can('can-remove'));
    }

    public function testEditPermission()
    {
        $baseCount = Permission::count();

        $this->visit('admin/auth/permissions/create')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-edit', 'name' => 'Can edit', 'http_path' => 'users/1/edit', 'http_method' => ['GET']])
            ->seePageIs('admin/auth/permissions')
            ->seeInDatabase(config('admin.database.permissions_table'), ['slug' => 'can-edit'])
            ->seeInDatabase(config('admin.database.permissions_table'), ['name' => 'Can edit'])
            ->assertEquals($baseCount + 1, Permission::count());

        $canEditId = $this->permissionId('can-edit');

        $this->visit('admin/auth/permissions/'.$canEditId.'/edit')
            ->see('Permissions')
            ->submitForm('Submit', ['slug' => 'can-delete'])
            ->seePageIs('admin/auth/permissions')
            ->seeInDatabase(config('admin.database.permissions_table'), ['slug' => 'can-delete'])
            ->assertEquals($baseCount + 1, Permission::count());
    }

    private function permissionId(string $slug): int
    {
        return (int) Permission::query()->where('slug', $slug)->value('id');
    }
}
