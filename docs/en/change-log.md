# Change log

## v2.0.0 (2026-02-20)

- Upgraded frontend framework to AdminLTE 3.2.0.
- Upgraded icon system to Font Awesome 5.
- Removed `admin.skin` and old `admin.layout` semantics.
- Added `admin.ui.*` configuration namespace.
- Migrated layout classes from `box-*` to `card-*`.
- Removed iCheck integration; switched to native `change` events.
- Replaced `Grid::editable()` internals with inline-edit components.
- Upgraded tests and assertions for AdminLTE 3 resource paths and layout classes.
- Fixed `admin:install` bootstrap flow: when `admin.auth.controller` has not been scaffolded yet, route loading now falls back to built-in auth controller.
- Fixed `jquery-pjax` compatibility with jQuery 3 (`$.event.props` removal).
- Fixed runtime initialization when pjax is unavailable: `laravel-admin.js` now guards pjax setup/reload/redirect calls.
- Fixed `$.admin.loadScripts` duplicate-check logic to avoid accidentally skipping dynamic script loading.
- Fixed datetime dependency chain for Tempus Dominus by switching to AdminLTE 3 moment bundle (`AdminLTE/plugins/moment/moment-with-locales.min.js`).

## v1.2.9、v1.3.3、v1.4.1

- Add user settings and modify avatar function
- Embedded form support
- Support for customize navigation bar (upper right corner)
- Add scaffolding, database command line tool, web artisan help tool
- Support for customize login page and login logic
- The form supports setting the width and setting the action
- Optimize table filters
- Fix bugs, optimize code and logic
