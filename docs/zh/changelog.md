# Release Notes

## [v1.8.9 (2020-11-02)](#v1.8.9%20(2020-11-02))

修复未授权访问安全漏洞，通过这个漏洞可以在不登陆的情况下执行后台定义的Action类。

## [v1.8.0 (2020-05-27)](#v1.8.0%20(2020-05-27))

参考 [CHANGELOG v1.8.0](/docs/zh/1.x/changelog-v1.8.0.md)

## [v1.7.6 (2019-08-30)](#v1.7.6%20(2019-08-30))

参考 [CHANGELOG v1.7.6](/docs/zh/1.x/changelog-v1.7.6.md)

## [v1.7.3 (2019-07-22)](#v1.7.3%20(2019-07-22))

参考 [CHANGELOG v1.7.3](/docs/zh/1.x/changelog-v1.7.3.md)

## [v1.7.2 (2019-06-23)](#v1.7.2%20(2019-06-23))

参考 [CHANGELOG v1.7.2](/docs/zh/1.x/changelog-v1.7.2.md)

## [v1.7.0 (2019-06-08)](#v1.7.0%20(2019-06-08))

参考 [CHANGELOG v1.7.0](/docs/zh/1.x/changelog-v1.7.0.md)

## [v1.6.13 (2019-05-05)](#v1.6.13%20(2019-05-05))

参考 [CHANGELOG v1.6.13](/docs/zh/1.x/changelog-v1.6.13.md)

## [v1.6.12 (2019-04-23)](#v1.6.12%20(2019-04-23))

参考 [CHANGELOG v1.6.12](/docs/zh/1.x/changelog-v1.6.12.md)

## [v1.6.10 (2019-03-10)](#v1.6.10%20(2019-03-10))

参考 [CHANGELOG v1.6.10](/docs/zh/1.x/changelog-v1.6.10.md)

## [v1.6.0 (2018-09-09)](#v1.6.0%20(2018-09-09))

参考 [CHANGELOG v1.6.0](/docs/zh/1.x/changelog-v1.6.0.md)

## [v1.5.19 (2018-08-22)](#v1.5.19%20(2018-08-22))

- 调整了生成控制器的代码结构，会根据表的字段生成对应的代码
- 调整了控制台命令，增加了三个命令, [参考](/docs/zh/1.x/commands.md)
- 增强了`admin:make`命令，将会根据表字段自动生成相应的代码
- 修复`v1.5.18`版本的bug

## [v1.5.18 (2018-08-10)](#v1.5.18%20(2018-08-10))

- 重构了grid过滤器的样式，增加`scope`查询支持
- `Model-show`支持将字段显示为文件样式
- 修改在`Model-grid`中的`editable`空字段的显示样式
- 支持`Model-grid`中的二维数组字段显示为表格

## [v1.5.16 (2018-08-3)](#v1.5.16%20(2018-08-3))

- 增加`Model-show`，支持显示数据详情

## [v1.2.9、v1.3.3、v1.4.1](#v1.2.9%E3%80%81v1.3.3%E3%80%81v1.4.1)

- 添加用户设置和修改头像功能
- model-form自定义工具[参考](/docs/zh/1.x/model-form.md?id=%E8%87%AA%E5%AE%9A%E4%B9%89%E5%B7%A5%E5%85%B7)
- 内嵌表单支持[参考](/docs/zh/1.x/model-form-fields.md?id=embeds)
- 支持自定义导航条（右上角）[参考](https://github.com/z-song/laravel-admin/issues/392)
- 添加脚手架、数据库命令行工具、web artisan帮助工具[参考](/docs/zh/1.x/helpers.md)
- 支持自定义登录页面和登录逻辑[参考](/docs/zh/1.x/qa.md?id=%E8%87%AA%E5%AE%9A%E4%B9%89%E7%99%BB%E5%BD%95%E9%A1%B5%E9%9D%A2%E5%92%8C%E7%99%BB%E5%BD%95%E9%80%BB%E8%BE%91)
- 表单支持设置宽度、设置action[参考](/docs/zh/1.x/model-form.md?id=%E5%85%B6%E5%AE%83%E6%96%B9%E6%B3%95)
- 优化表格过滤器
- 修复bug，优化代码和逻辑
