# 安装

首先确保安装好了`laravel`，并且数据库连接设置正确。

```
composer require encore/laravel-admin:1.*
```

然后运行下面的命令来发布资源：

```
php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider"
```

在该命令会生成配置文件`config/admin.php`，可以在里面修改安装的地址、数据库连接、以及表名，建议都是用默认配置不修改。

然后运行下面的命令完成安装：

```
php artisan admin:install
```

> ##### Notice
>
> 运行这个命令的时候，如果遇到了下面的错误:
>
> `SQLSTATE[42000]: Syntax error or access violation: 1071 Specified key was too long; max key length is 1000 bytes (SQL: alter table`users`add unique`users\_email\_unique`(`email`))`
>
> 参考这个issue来解决 <https://github.com/z-song/laravel-admin/issues/1541>

启动服务后，在浏览器打开 `http://localhost/admin/` ,使用用户名 `admin` 和密码 `admin`登录.

## [生成的文件](#%E7%94%9F%E6%88%90%E7%9A%84%E6%96%87%E4%BB%B6)

安装完成之后,会在项目目录中生成以下的文件:

### [配置文件](#%E9%85%8D%E7%BD%AE%E6%96%87%E4%BB%B6)

安装完成之后，`laravel-admin`所有的配置都在`config/admin.php`文件中。

### [后台项目文件](#%E5%90%8E%E5%8F%B0%E9%A1%B9%E7%9B%AE%E6%96%87%E4%BB%B6)

安装完成之后，后台的安装目录为`app/Admin`，之后大部分的后台开发编码工作都是在这个目录下进行。

```
app/Admin
├── Controllers
│   ├── ExampleController.php
│   └── HomeController.php
├── bootstrap.php
└── routes.php
```

`app/Admin/routes.php`文件用来配置后台路由。

`app/Admin/bootstrap.php` 是`laravel-admin`的启动文件, 使用方法请参考文件里面的注释.

`app/Admin/Controllers`目录用来存放后台控制器文件，该目录下的`HomeController.php`文件是后台首页的显示控制器，`ExampleController.php`为实例文件。

### [静态文件](#%E9%9D%99%E6%80%81%E6%96%87%E4%BB%B6)

后台所需的前端静态文件在`/public/vendor/laravel-admin`目录下.
