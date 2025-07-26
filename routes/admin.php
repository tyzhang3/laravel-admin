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
    // 认证相关路由
    $authController = config('admin.auth.controller', AuthController::class);
    Route::get('auth/login', [$authController, 'getLogin'])->name('admin.login');
    Route::post('auth/login', [$authController, 'postLogin']);
    Route::get('auth/logout', [$authController, 'getLogout'])->name('admin.logout');
    Route::get('auth/setting', [$authController, 'getSetting'])->name('admin.setting');
    Route::put('auth/setting', [$authController, 'putSetting']);

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