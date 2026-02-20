<?php

use Illuminate\Support\Facades\Route;

class AuthTest extends TestCase
{
    public function testLoginPage()
    {
        $this->visit('admin/auth/login')
            ->see('login');
    }

    public function testLoginPageSupportsHeadMethod()
    {
        $this->call('HEAD', 'admin/auth/login');
        $this->assertResponseStatus(200);
    }

    public function testVisitWithoutLogin()
    {
        $this->visit('admin')
            ->dontSeeIsAuthenticated('admin')
            ->seePageIs('admin/auth/login');
    }

    public function testLogin()
    {
        $credentials = ['username' => 'admin', 'password' => 'admin'];

        $this->visit('admin/auth/login')
            ->see('login')
            ->submitForm('Login', $credentials)
            ->see('dashboard')
            ->seeCredentials($credentials, 'admin')
            ->seeIsAuthenticated('admin')
            ->seePageIs('admin')
            ->see('Dashboard')
            ->see('Description...')
            ->see('Environment')
            ->see('PHP version')
            ->see('Laravel version')
            ->see('Available extensions')
            ->seeLink('laravel-admin-ext/helpers', 'https://github.com/laravel-admin-extensions/helpers')
            ->seeLink('laravel-admin-ext/backup', 'https://github.com/laravel-admin-extensions/backup')
            ->seeLink('laravel-admin-ext/media-manager', 'https://github.com/laravel-admin-extensions/media-manager')
            ->see('Dependencies')
            ->see('php')
            ->see('laravel/framework');
    }

    public function testLogout()
    {
        $this->visit('admin/auth/logout')
            ->seePageIs('admin/auth/login')
            ->dontSeeIsAuthenticated('admin');
    }

    public function testVisitWithoutLoginRedirectsToOpenidWhenOpenidEnabled()
    {
        $this->app['config']->set('admin.auth.login_method', 'openid');
        $this->app['config']->set('admin.auth.redirect_to', 'auth/login');

        Route::get('admin/auth/openid/login', function () {
            return 'openid login';
        });

        $this->visit('admin')
            ->dontSeeIsAuthenticated('admin')
            ->seePageIs('admin/auth/openid/login')
            ->see('openid login');
    }
}
