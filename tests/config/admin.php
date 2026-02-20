<?php

return [

    /*
     * Laravel-admin name.
     */
    'name' => 'Laravel-admin',

    /*
     * Logo in admin panel header.
     */
    'logo' => '<b>Laravel</b> admin',

    /*
     * Mini-logo in admin panel header.
     */
    'logo-mini' => '<b>La</b>',

    /*
     * Route configuration.
     */
    'route' => [

        'prefix' => 'admin',

        'namespace' => 'App\\Admin\\Controllers',

        'middleware' => ['web', 'admin'],
    ],

    /*
     * Laravel-admin install directory.
     */
    'directory' => app_path('Admin'),

    /*
     * Laravel-admin html title.
     */
    'title' => 'Admin',

    /*
     * Use `https`.
     */
    'secure' => false,

    /*
     * Laravel-admin auth setting.
     */
    'auth' => [
        'controller' => Encore\Admin\Controllers\AuthController::class,

        'guard' => 'admin',

        'guards' => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => Encore\Admin\Auth\Database\Administrator::class,
            ],
        ],

        'remember' => true,

        'login_method' => 'password',

        'redirect_to' => 'auth/login',

        'excepts' => [
            'auth/login',
            'auth/logout',
            'auth/openid/login',
            'auth/openid/callback',
        ],

        'routes' => [
            [
                'method' => 'GET',
                'uri' => 'auth/login',
                'action' => 'getLogin',
                'name' => 'login',
                'login_method' => 'password',
                'middleware' => ['admin.guest'],
                'without_middleware' => ['admin.auth'],
            ],
            [
                'method' => 'POST',
                'uri' => 'auth/login',
                'action' => 'postLogin',
                'login_method' => 'password',
                'without_middleware' => ['admin.auth'],
            ],
            [
                'method' => 'GET',
                'uri' => 'auth/openid/login',
                'action' => 'getLogin',
                'name' => 'openid.login',
                'login_method' => 'openid',
                'middleware' => ['admin.guest'],
                'without_middleware' => ['admin.auth'],
            ],
            [
                'method' => 'GET',
                'uri' => 'auth/openid/callback',
                'action' => 'getLogin',
                'name' => 'openid.callback',
                'login_method' => 'openid',
                'middleware' => ['admin.guest'],
                'without_middleware' => ['admin.auth'],
            ],
            [
                'method' => 'GET',
                'uri' => 'auth/logout',
                'action' => 'getLogout',
                'name' => 'logout',
                'login_method' => 'common',
            ],
            [
                'method' => 'GET',
                'uri' => 'auth/setting',
                'action' => 'getSetting',
                'name' => 'setting',
                'login_method' => 'common',
            ],
            [
                'method' => 'PUT',
                'uri' => 'auth/setting',
                'action' => 'putSetting',
                'login_method' => 'common',
            ],
        ],
    ],

    /*
     * Laravel-admin upload setting.
     */
    'upload' => [

        'disk' => 'admin',

        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],

    /*
     * Laravel-admin database setting.
     */
    'database' => [

        // Database connection for following tables.
        'connection' => '',

        // User tables and model.
        'users_table' => 'admin_users',
        'users_model' => Encore\Admin\Auth\Database\Administrator::class,

        // Role table and model.
        'roles_table' => 'admin_roles',
        'roles_model' => Encore\Admin\Auth\Database\Role::class,

        // Permission table and model.
        'permissions_table' => 'admin_permissions',
        'permissions_model' => Encore\Admin\Auth\Database\Permission::class,

        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => Encore\Admin\Auth\Database\Menu::class,

        // Pivot table for table above.
        'operation_log_table'    => 'admin_operation_log',
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table'       => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table'        => 'admin_role_menu',
    ],

    /*
     * By setting this option to open or close operation log in laravel-admin.
     */
    'operation_log' => [

        'enable' => true,

        /*
         * Routes that will not log to database.
         *
         * All method to path like: admin/auth/logs
         * or specific method to path like: get:admin/auth/logs
         */
        'except' => [
            'admin/auth/logs*',
        ],
    ],

    'ui' => [
        'body_class' => 'hold-transition sidebar-mini layout-fixed',
        'navbar_class' => 'main-header navbar navbar-expand navbar-white navbar-light',
        'sidebar_class' => 'main-sidebar sidebar-dark-primary elevation-4',
        'brand_class' => 'brand-link',
        'content_class' => 'content-wrapper',
        'login_class' => 'hold-transition login-page',
    ],

    /*
     * Version displayed in footer.
     */
    'version' => '2.x',

    /*
     * Settings for extensions.
     */
    'extensions' => [

    ],
];
