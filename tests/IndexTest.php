<?php

use Encore\Admin\Auth\Database\Administrator;

class IndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testIndex()
    {
        $this->visit('admin/')
            ->seeElement('link[href*="AdminLTE/dist/css/adminlte.min.css"]')
            ->seeElement('link[href*="AdminLTE/plugins/fontawesome-free/css/all.min.css"]')
            ->seeElement('script[src*="AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js"]')
            ->seeElement('script[src*="AdminLTE/dist/js/adminlte.min.js"]')
            ->seeElement('a[data-widget=pushmenu]')
            ->seeElement('ul.nav-sidebar')
            ->seeElement('div.card')
            ->dontSee('plugins/jQuery/jQuery-2.1.4.min.js')
            ->dontSee('dist/css/skins/')
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
//            ->see('>=7.0.0')
            ->see('laravel/framework');
    }

    public function testClickMenu()
    {
        $this->visit('admin/')
            ->click('Users')
            ->seePageis('admin/auth/users')
            ->click('Roles')
            ->seePageis('admin/auth/roles')
            ->click('Permission')
            ->seePageis('admin/auth/permissions')
            ->click('Menu')
            ->seePageis('admin/auth/menu')
            ->click('Operation log')
            ->seePageis('admin/auth/logs');
    }
}
