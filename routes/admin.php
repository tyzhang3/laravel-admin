<?php

use Illuminate\Support\Facades\Route;
use Encore\Admin\Controllers\UserController;
use Encore\Admin\Controllers\RoleController;
use Encore\Admin\Controllers\PermissionController;
use Encore\Admin\Controllers\MenuController;
use Encore\Admin\Controllers\LogController;
use Encore\Admin\Controllers\HandleController;
use Encore\Admin\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Laravel Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::namespace('Encore\Admin\Controllers')->group(function () {
    $authController = config('admin.auth.controller', AuthController::class);
    $loginMethod = config('admin.auth.login_method', 'password');
    $selectedLoginMethod = in_array($loginMethod, ['password', 'openid'], true) ? $loginMethod : 'password';

    $authRoutes = config('admin.auth.routes', [
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
    ]);

    foreach ($authRoutes as $routeConfig) {
        $routeLoginMethod = $routeConfig['login_method'] ?? 'common';
        if (
            $routeLoginMethod !== 'common'
            && $routeLoginMethod !== $selectedLoginMethod
        ) {
            continue;
        }

        $controller = $routeConfig['controller'] ?? $authController;
        $method = $routeConfig['method'] ?? 'GET';
        $uri = $routeConfig['uri'] ?? null;
        $action = $routeConfig['action'] ?? null;

        if (!$uri || !$action) {
            continue;
        }

        $methods = array_map('strtoupper', (array) $method);
        if (in_array('GET', $methods, true) && !in_array('HEAD', $methods, true)) {
            $methods[] = 'HEAD';
        }

        $route = Route::match($methods, $uri, [$controller, $action]);

        if (!empty($routeConfig['name'])) {
            $route->name($routeConfig['name']);
        }

        if (!empty($routeConfig['middleware'])) {
            $route->middleware($routeConfig['middleware']);
        }

        if (!empty($routeConfig['without_middleware'])) {
            $route->withoutMiddleware($routeConfig['without_middleware']);
        }
    }

    // 资源管理路由
    Route::resource('auth/users', UserController::class)->names('admin.auth.users');
    Route::resource('auth/roles', RoleController::class)->names('admin.auth.roles');
    Route::resource('auth/permissions', PermissionController::class)->names('admin.auth.permissions');
    Route::resource('auth/menu', MenuController::class, ['except' => ['create']])->names('admin.auth.menu');
    Route::resource('auth/logs', LogController::class, ['only' => ['index', 'destroy']])->names('admin.auth.logs');

    // 处理相关路由
    Route::post('_handle_form_', [HandleController::class, 'handleForm'])->name('admin.handle-form');
    Route::post('_handle_action_', [HandleController::class, 'handleAction'])->name('admin.handle-action');
    Route::get('_handle_selectable_', [HandleController::class, 'handleSelectable'])->name('admin.handle-selectable');
    Route::get('_handle_renderable_', [HandleController::class, 'handleRenderable'])->name('admin.handle-renderable');
});
