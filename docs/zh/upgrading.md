# 版本升级

## [查看当前版本](#%E6%9F%A5%E7%9C%8B%E5%BD%93%E5%89%8D%E7%89%88%E6%9C%AC)

```
composer show encore/laravel-admin

// or 

php artisan admin
```

## [更新到最新版本](#%E6%9B%B4%E6%96%B0%E5%88%B0%E6%9C%80%E6%96%B0%E7%89%88%E6%9C%AC)

```
composer require encore/laravel-admin -vvv
```

## [更新到开发版本](#%E6%9B%B4%E6%96%B0%E5%88%B0%E5%BC%80%E5%8F%91%E7%89%88%E6%9C%AC)

```
composer require encore/laravel-admin:dev-master -vvv
```

## [更新指定版本](#%E6%9B%B4%E6%96%B0%E6%8C%87%E5%AE%9A%E7%89%88%E6%9C%AC)

```
composer require encore/laravel-admin:1.6.15 -vvv
```

> ##### 注意
>
> 由于每个版本的静态资源或者语言包都有可能会有更新，所以升级版本之后最好运行下面的命令

```
// 强制发布静态资源文件
php artisan vendor:publish --tag=laravel-admin-assets --force

// 强制发布语言包文件
php artisan vendor:publish --tag=laravel-admin-lang --force

// 清理视图缓存
php artisan view:clear
```

最后不要忘记清理浏览器缓存
