# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel Admin package (tyzhang3/laravel-admin) - a administrative interface builder for Laravel applications that helps build CRUD backends with minimal code. It's a fork of the original encore/laravel-admin package, specifically updated for Laravel 12 compatibility.

## Development Commands

### Testing
```bash
# Run all tests
composer test
# or
./vendor/bin/phpunit

# Run specific test file
./vendor/bin/phpunit tests/AuthTest.php
```

### Package Development
```bash
# Install package in development mode
composer install

# Generate package files
composer dump-autoload

# Publish assets and config (for testing)
php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider"

# Install admin panel
php artisan admin:install
```

### Code Quality
```bash
# Run linting (if configured)
php vendor/bin/phpcs --standard=PSR2 src/

# Run static analysis (if available)
php vendor/bin/phpstan analyse src/
```

## Architecture Overview

### Core Components

1. **Admin Class** (`src/Admin.php`)
   - Main facade entry point for all admin operations
   - Manages routes, assets, navigation, and extensions
   - Uses Facade pattern for static access to instance methods

2. **AdminServiceProvider** (`src/AdminServiceProvider.php`)
   - Registers package services and routes
   - Automatically registers admin routes via `registerAdminRoutes()` method
   - Registers middleware and blade directives
   - Publishes configuration and assets

3. **Main CRUD Components**
   - **Grid** (`src/Grid/`) - Data listing, filtering, exporting
   - **Form** (`src/Form/`) - Data entry with validation and file uploads
   - **Show** (`src/Show/`) - Data display with relationships

### Authentication & Authorization System

**Database Models** (`src/Auth/Database/`):
- `Administrator` - Admin user model
- `Role` - Role-based access control
- `Permission` - Granular permission system
- `Menu` - Navigation menu system
- `OperationLog` - Admin operation tracking

**Key Traits**:
- `HasPermissions` - Permission checking methods
- Uses role-based access control with granular permissions

### Middleware Stack
- `Authenticate` - Admin authentication
- `Permission` - Route permission checking
- `Bootstrap` - Admin interface setup
- `LogOperation` - Operation logging
- `Pjax` - PJAX request handling

### Routing System
- Routes are automatically registered via `Admin::routes()`
- Uses route groups with prefix `/admin` by default
- Includes authentication, user management, role/permission management routes
- Custom routes can be added via `admin_path('routes.php')`

### Field System
**Form Fields** (`src/Form/Field/`):
- 60+ field types including text, select, file uploads, relationships
- Built-in validation and file handling
- Support for complex nested forms

**Grid Displayers** (`src/Grid/Displayers/`):
- Various ways to display data in grids
- Actions, dropdowns, images, etc.

## Important Patterns

### Facade Usage
Always use the Admin Facade for static method calls:
```php
use Encore\Admin\Facades\Admin;

// Correct - uses facade
Admin::routes();

// Incorrect - direct static call
\Encore\Admin\Admin::routes();
```

### Route Registration
Routes are automatically registered, but can be customized:
- Routes are loaded in `AdminServiceProvider::boot()`
- Custom routes can be added in `admin_path('routes.php')`
- Route prefix and middleware are configurable

### Permission System
- Uses role-based access control
- Permissions can be assigned to roles, roles to users
- Middleware checks permissions automatically
- Use `Admin::user()->can('permission.slug')` for manual checks

### Database Tables
All table names are configurable via `config/admin.php`:
- `admin_users` - Administrator accounts
- `admin_roles` - User roles
- `admin_permissions` - Permission definitions
- `admin_menu` - Navigation structure
- `admin_operation_log` - Action tracking

## Common Development Tasks

### Adding New Admin Features
1. Create controller extending `Encore\Admin\Controllers\AdminController`
2. Define routes in custom routes file
3. Assign permissions to roles
4. Add menu items via admin interface

### Customizing Fields
- Extend field classes in `src/Form/Field/`
- Use `$form->text('field')` for basic fields
- Override field methods for custom behavior

### Extending Permissions
- Add permissions via database or seeder
- Use middleware for route protection
- Check permissions with `Admin::user()->can()`

## File Structure Notes

- `src/` - Main package code
- `database/migrations/` - Database schema
- `database/migrations/2025_01_01_000002_fix_admin_permissions.php` - Permission fix migration
- `resources/` - Views, assets, and language files
- `tests/` - Comprehensive test suite
- `config/admin.php` - Package configuration (published to app)

## Testing Notes

- Tests use Orchestra Testbench for Laravel application simulation
- Test fixtures in `tests/seeds/` and `tests/migrations/`
- Key test files: `AuthTest.php`, `PermissionsTest.php`, `UserGridTest.php`
- Test with `php artisan admin:test` command (if available)

## Extension System

The package supports extensions via:
- `Admin::extend()` method
- Extension directory in `app/Admin/Extensions/`
- Configuration via `config/admin.php.extensions`