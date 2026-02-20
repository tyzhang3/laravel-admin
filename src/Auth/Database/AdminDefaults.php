<?php

namespace Encore\Admin\Auth\Database;

class AdminDefaults
{
    /**
     * @return array<string, string>
     */
    public static function administrator(): array
    {
        return [
            'username' => 'admin',
            'password' => 'admin',
            'name'     => 'Administrator',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function role(): array
    {
        return [
            'name' => 'Administrator',
            'slug' => 'administrator',
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function permissions(): array
    {
        return [
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
                'name'        => 'Auth management',
                'slug'        => 'auth.management',
                'http_method' => '',
                'http_path'   => '/auth*',
            ],
        ];
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public static function rootMenus(): array
    {
        return [
            [
                'order' => 1,
                'title' => 'Dashboard',
                'icon'  => 'fa-bar-chart',
                'uri'   => '/',
            ],
            [
                'order' => 2,
                'title' => 'Admin',
                'icon'  => 'fa-tasks',
                'uri'   => '',
            ],
        ];
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public static function adminChildMenus(): array
    {
        return [
            [
                'order' => 3,
                'title' => 'Users',
                'icon'  => 'fa-users',
                'uri'   => 'auth/users',
            ],
            [
                'order' => 4,
                'title' => 'Roles',
                'icon'  => 'fa-user',
                'uri'   => 'auth/roles',
            ],
            [
                'order' => 5,
                'title' => 'Permission',
                'icon'  => 'fa-ban',
                'uri'   => 'auth/permissions',
            ],
            [
                'order' => 6,
                'title' => 'Menu',
                'icon'  => 'fa-bars',
                'uri'   => 'auth/menu',
            ],
            [
                'order' => 7,
                'title' => 'Operation log',
                'icon'  => 'fa-history',
                'uri'   => 'auth/logs',
            ],
        ];
    }
}
