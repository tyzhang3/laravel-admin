# 升级指南

## 2.0.0（AdminLTE 3.2）破坏性升级

本版本将前端框架从 AdminLTE 2.x 升级到 AdminLTE 3.2.0。

## 主要破坏性变更

- 布局组件从 `box-*` 迁移为 `card-*`。
- 侧边栏和菜单语义升级为 AdminLTE 3：
  - `data-widget="pushmenu"`
  - `nav-sidebar` + treeview 结构
- 图标升级为 Font Awesome 5（`fontawesome-free`）。
- 移除 `iCheck`，复选框/单选框统一使用原生 `change` 事件。
- `Grid::editable()` 内部实现改为内置 inline-edit 组件，不再依赖 bootstrap3-editable。
- 移除旧配置键：
  - `admin.skin`
  - 旧 `admin.layout` 语义
- 新增 UI 配置命名空间：
  - `admin.ui.body_class`
  - `admin.ui.navbar_class`
  - `admin.ui.sidebar_class`
  - `admin.ui.brand_class`
  - `admin.ui.content_class`
  - `admin.ui.login_class`

## 建议迁移步骤

1. 升级并重新发布资源：

```bash
composer require encore/laravel-admin -vvv
php artisan vendor:publish --tag=laravel-admin-assets --force
php artisan vendor:publish --tag=laravel-admin-lang --force
php artisan view:clear
```

2. 更新 `config/admin.php`：
   - 删除 `skin` 和旧 `layout` 配置
   - 使用 `admin.ui.*` 进行样式类配置

3. 如有自定义 Blade/组件，重点迁移：
   - `box-*` -> `card-*`
   - `btn-box-tool` -> `btn btn-tool`
   - `data-widget="collapse/remove"` -> `data-card-widget="collapse/remove"`

4. 如有自定义 checkbox/radio 前端逻辑，替换 `ifChecked/ifChanged` 与 `.iCheck(...)` 为原生 `change` 与 `.prop('checked', ...)`。

5. 如扩展过 `Grid::editable()`，请按 inline-edit 模板和脚本重新验证。

## 最小验证命令

```bash
./vendor/bin/phpunit --filter PermissionsTest
./vendor/bin/phpunit --filter AuthTest
./vendor/bin/phpunit --filter IndexTest
./vendor/bin/phpunit --testsuite all
```

## 真实浏览器回归（建议）

升级并发布资源后，建议在一个全新 Laravel 应用里真实安装并做浏览器回归（例如 Playwright）：

```bash
php artisan vendor:publish --provider="Encore\\Admin\\AdminServiceProvider" --force
php artisan admin:install
```

至少验证：

1. Dashboard：浏览器控制台无运行时 JS 错误。
2. 权限页面（`users/roles/menu/logs`）：菜单树、筛选、跳转正常。
3. 表单插件页：`select2`、`bootstrap-switch`、`ion-rangeSlider`、`duallistbox`、`fileinput`、`colorpicker`。
4. Grid 内联编辑页：popover 能正常弹出并提交。

## v2.0.0 实测注意事项

- `jquery-pjax` 需要兼容 jQuery 3（jQuery 3 已移除 `$.event.props`）。
- 日期组件需使用 AdminLTE 3 的 moment（`AdminLTE/plugins/moment/moment-with-locales.min.js`）才能兼容 Tempus Dominus。
- pjax 初始化失败时，前端刷新/跳转需要有降级路径（`reload`/`redirect`）。
- 动态脚本加载（`$.admin.loadScripts`）要用严格去重判断（`!== -1`），避免脚本被误判为“已加载”。
- `admin:install` 期间，路由加载要允许在业务侧认证控制器尚未生成时先回退到内置控制器。
