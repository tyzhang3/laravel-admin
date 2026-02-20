# Changelog

## 2.0.0 - 2026-02-20

### Breaking Changes

- Upgraded frontend framework from AdminLTE 2.x to AdminLTE 3.2.0.
- Removed legacy `admin.skin` and old `admin.layout` semantics.
- Added new UI configuration namespace: `admin.ui.*`.
- Migrated layout/component classes from `box-*` to `card-*`.
- Upgraded icon pipeline to Font Awesome 5 (`fontawesome-free`).
- Removed iCheck integration and switched checkbox/radio handling to native `change` events.
- Replaced `Grid::editable()` internals with inline-edit component implementation.

### Frontend Dependency Changes

- Bootstrap JS path switched to `bootstrap.bundle.min.js` (AdminLTE plugins directory).
- Core AdminLTE assets switched to:
  - `dist/css/adminlte.min.css`
  - `dist/js/adminlte.min.js`
- Added `overlayScrollbars` as base dependency and removed `slimScroll`.
- Date/Datetime picker moved to `tempusdominus-bootstrap-4`.

### Test Updates

- Updated permission test assertions to avoid hard-coded IDs and counts.
- Updated UI assertions for `card-*`, `pushmenu`, and AdminLTE 3 asset paths.
- Added inline-edit rendering assertions for the new `Grid::editable()` implementation.
