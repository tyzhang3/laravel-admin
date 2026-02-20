# Change log

## v2.0.0（2026-02-20）

- 前端框架升级到 AdminLTE 3.2.0。
- 图标体系升级到 Font Awesome 5。
- 移除 `admin.skin` 与旧 `admin.layout` 语义。
- 新增 `admin.ui.*` 配置命名空间。
- 布局类从 `box-*` 迁移到 `card-*`。
- 移除 iCheck，统一使用原生 `change` 事件。
- `Grid::editable()` 内部实现切换为 inline-edit 组件。
- 测试升级：资源路径、布局类与交互断言同步到 AdminLTE 3。
- 修复 `admin:install` 安装期路由加载问题：当 `admin.auth.controller` 尚未生成时，自动回退到内置认证控制器。
- 修复 `jquery-pjax` 在 jQuery 3 下的兼容性问题（`$.event.props` 已移除）。
- 修复 pjax 不可用时的运行时初始化问题：`laravel-admin.js` 对 pjax 初始化/刷新/跳转增加保护。
- 修复 `$.admin.loadScripts` 去重判断逻辑，避免动态脚本被误跳过。
- 修复 Tempus Dominus 依赖链：日期组件改为使用 AdminLTE 3 自带 moment（`AdminLTE/plugins/moment/moment-with-locales.min.js`）。

## v1.2.9、v1.3.3、v1.4.1

- 添加用户设置和修改头像功能
- model-form自定义工具[参考](zh/model-form.md?id=自定义工具)
- 内嵌表单支持[参考](zh/model-form-fields.md?id=embeds)
- 支持自定义导航条（右上角）[参考](https://github.com/z-song/laravel-admin/issues/392)
- 添加脚手架、数据库命令行工具、web artisan帮助工具[参考](zh/helpers.md)
- 支持自定义登陆页面和登陆逻辑[参考](zh/qa.md?id=自定义登陆页面和登陆逻辑)
- 表单支持设置宽度、设置action[参考](zh/model-form.md?id=其它方法)
- 优化表格过滤器
- 修复bug，优化代码和逻辑
