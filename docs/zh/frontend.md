# CSS / JavaScript

## [设置网站favicon](#%E8%AE%BE%E7%BD%AE%E7%BD%91%E7%AB%99favicon)

在`app/Admin/bootstrap.php`加入下面的代码来设置网站的favicon：

```
use Encore\Admin\Admin;

Admin::favicon('/your/favicon/path');
```

## [引入CSS和JavaScript文件](#%E5%BC%95%E5%85%A5CSS%E5%92%8CJavaScript%E6%96%87%E4%BB%B6)

如果你需要引入CSS或者JavaScript文件，可以在`app/Admin/bootstrap.php`加入下面的代码：

```
Admin::css('/your/css/path/style.css');
Admin::js('/your/javascript/path/js.js');
```

其中文件的路径为相对与`public`目录的路径，也支持引入外部文件：

```
Admin::js('https://cdn.bootcss.com/vue/2.6.10/vue.min.js');
```

## [页面插入JS脚本代码](#%E9%A1%B5%E9%9D%A2%E6%8F%92%E5%85%A5JS%E8%84%9A%E6%9C%AC%E4%BB%A3%E7%A0%81)

如果你要在当前的页面加入一段JS脚本代码，可以使用`Admin::script()`

```
use Encore\Admin\Admin;

Admin::script('console.log("hello world");');
```

这段代码可以在插入当前请求所运行到的代码文件的任何地方，比如表单项之间的一些联动效果，可以用插入JS脚本代码的方式来实现。

## [页面插入CSS样式](#%E9%A1%B5%E9%9D%A2%E6%8F%92%E5%85%A5CSS%E6%A0%B7%E5%BC%8F)

如果你要在当前的页面加入一段CSS代码，可以使用`Admin::style()`

```
use Encore\Admin\Admin;

Admin::style('.form-control {margin-top: 10px;}');
```

这段代码可以在插入当前请求所运行到的代码文件的任何地方。

## [页面插入HTML代码](#%E9%A1%B5%E9%9D%A2%E6%8F%92%E5%85%A5HTML%E4%BB%A3%E7%A0%81)

如果你要在当前的页面加入一段HTML代码，可以使用`Admin::html()`

```
use Encore\Admin\Admin;

Admin::html('<template>...</template>');
```

这段代码可以在插入当前请求所运行到的代码文件的任何地方, 比如要在当前页面加入一些隐藏的HTML代码的时候，可以使用这个方法来实现。

## [压缩CSS和JavaScript](#%E5%8E%8B%E7%BC%A9CSS%E5%92%8CJavaScript)

这个功能用来将后台引入的本地CSS和JS压缩，以加快后台的页面访问速度。

> ##### 依赖
>
> 这个功能依赖[[matthiasmullie/minify]](https://github.com/matthiasmullie/minify)作为压缩库，使用之前需要先安装好它：
>
> ```
> composer require matthiasmullie/minify --dev
> ```

## [压缩](#%E5%8E%8B%E7%BC%A9)

然后在项目根目录运行命令`php artisan admin:minify`：

```
$ php artisan admin:minify

JS and CSS are successfully minified:
  vendor/laravel-admin/laravel-admin.min.js
  vendor/laravel-admin/laravel-admin.min.css

Manifest successfully generated:
  vendor/laravel-admin/minify-manifest.json
```

这个命令会生成三个文件，查看后台页面源码就可以看到效果了。

## [清理](#%E6%B8%85%E7%90%86)

运行命令`php artisan admin:minify --clear`来清理掉上面生成的压缩文件，回到压缩之前的状态。

```
$ php artisan admin:minify --clear

Following files are cleared:
  vendor/laravel-admin/laravel-admin.min.js
  vendor/laravel-admin/laravel-admin.min.css
  vendor/laravel-admin/minify-manifest.json
```

## [配置](#%E9%85%8D%E7%BD%AE)

对于从低版本更新上来的用户，需要在`config/admin.php`增加一项配置:

```
    'minify_assets' => true,
```
