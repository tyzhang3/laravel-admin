# Upgrade Guide

## 2.0.0 (AdminLTE 3.2) Breaking Upgrade

This release upgrades the frontend stack from AdminLTE 2.x to AdminLTE 3.2.0.

## Breaking changes

- Admin layout/components migrated from `box-*` to `card-*`.
- Sidebar/menu switched to AdminLTE 3 semantics:
  - `data-widget="pushmenu"`
  - `nav-sidebar` + treeview structure
- Font Awesome upgraded to FA5 (`fontawesome-free`).
- `iCheck` removed. Checkbox/radio events now use native `change`.
- `Grid::editable()` no longer uses bootstrap3-editable internals; it now uses built-in inline-edit components.
- Legacy config keys removed:
  - `admin.skin`
  - old `admin.layout` semantics
- New UI config namespace:
  - `admin.ui.body_class`
  - `admin.ui.navbar_class`
  - `admin.ui.sidebar_class`
  - `admin.ui.brand_class`
  - `admin.ui.content_class`
  - `admin.ui.login_class`

## Required migration steps

1. Upgrade package and publish assets.

```bash
composer require encore/laravel-admin -vvv
php artisan vendor:publish --tag=laravel-admin-assets --force
php artisan vendor:publish --tag=laravel-admin-lang --force
php artisan view:clear
```

2. Update your `config/admin.php`:
   - remove `skin` and old `layout` usage
   - add and use `admin.ui.*`

3. If you override blades/components, migrate:
   - `box-*` to `card-*`
   - `btn-box-tool` to `btn btn-tool`
   - `data-widget="collapse/remove"` to `data-card-widget="collapse/remove"`

4. If you have custom checkbox/radio JS, replace `ifChecked/ifChanged` and `.iCheck(...)` calls with native `change` and `.prop('checked', ...)`.

5. If you use `Grid::editable()` extensions/customizations, verify your code against inline-edit templates and scripts.

## Verification

Run at least:

```bash
./vendor/bin/phpunit --filter PermissionsTest
./vendor/bin/phpunit --filter AuthTest
./vendor/bin/phpunit --filter IndexTest
./vendor/bin/phpunit --testsuite all
```

## Real browser regression (recommended)

After upgrading and publishing assets, run a real browser flow (for example with Playwright) against a fresh Laravel app that installs this package:

```bash
php artisan vendor:publish --provider="Encore\\Admin\\AdminServiceProvider" --force
php artisan admin:install
```

Verify at least:

1. Dashboard: no runtime JS errors in console.
2. Auth pages (`users/roles/menu/logs`): navigation, tree, and filter interactions still work.
3. Form plugins page: `select2`, `bootstrap-switch`, `ion-rangeSlider`, `duallistbox`, `fileinput`, `colorpicker`.
4. Grid inline edit page: popover opens and submits correctly.

## Notes from v2.0.0 browser validation

- `jquery-pjax` must be jQuery 3 compatible (`$.event.props` is removed in jQuery 3).
- Date widgets must use AdminLTE 3 moment bundle (`AdminLTE/plugins/moment/moment-with-locales.min.js`) for Tempus Dominus compatibility.
- If pjax fails to initialize, runtime should gracefully fall back (`reload`/`redirect`).
- Dynamic script loading (`$.admin.loadScripts`) should use strict duplicate checks (`!== -1`) to avoid false-positive skips.
- `admin:install` should allow route loading before userland auth controller scaffolding exists.
