# 开发扩展

> since v1.6.0

`laravel-admin`支持安装扩展工具来帮助丰富你的后台功能，目前在<https://github.com/laravel-admin-extensions>下面已经有了几十个扩展可供安装使用。

如果大家在使用的过程中有在`laravel-admin`的基础上添加一些自己的功能或者组件，不妨做成一个`laravel-admin`扩展，这样可以给其它`laravel-admin`使用者提供帮助，并且在其它人的使用反馈中的提升扩展的质量。

这篇文档将会以开发一个`phpinfo`的扩展为例，一步一步的开发一个扩展，并且发布给他人使用，最终的效果参考[phpinfo](http://demo.laravel-admin.org/phpinfo)

## [创建composer包](#%E5%88%9B%E5%BB%BAcomposer%E5%8C%85)

`laravel-admin`的包将会用composer安装，所以先要创建一个composer包，如果你安装了`v1.6`或者`dev-master`版本，可以是用内置的`admin:extend`命令来生成一个扩展的骨架

> 运行命令的时候，可能会提示输入一个目录来存放你的扩展文件，你可以在`config/admin.php`里面增加一个配置`'extension_dir' => admin_path('extensions'),`，这样扩展文件将会存放在`app/Admin/extensions`目录下,当然你也可以放在任何其它目录

```
php artisan admin:extend laravel-admin-ext/phpinfo --namespace=Encore\\PHPInfo
```

其中`laravel-admin-ext/phpinfo`是包名，`namespace`选项是这个包使用的顶级命名空间，运行这个命令之后, 将会在在config/admin.php中设置的扩展目录中生成目录`laravel-admin-ext/phpinfo`和下面的文件结构

```
    ├── LICENSE
    ├── README.md
    ├── composer.json
    ├── database
    │   ├── migrations
    │   └── seeds
    ├── resources
    │   ├── assets
    │   └── views
    │       └── index.blade.php
    ├── routes
    │   └── web.php
    └── src
        ├── PHPInfo.php
        ├── PHPInfoServiceProvider.php
        └── Http
            └── Controllers
                └── PHPInfoController.php
```

`resources`用来放置视图文件和静态资源文件，`src`主要用来放置逻辑代码, `routes/web.php`用来存放这个扩展的路由设置，`database`用来放置数据库迁移文件和数据`seeders`。

## [功能开发](#%E5%8A%9F%E8%83%BD%E5%BC%80%E5%8F%91)

这个扩展的功能主要用来将PHP中的`phpinfo`函数所显示的页面，集成进`laravel-admin`中，它将会有一个路由和一个视图文件，没有数据库文件和静态资源文件，我们可以将没有用到的文件或目录清理掉，清理之后的目录文件为：

```
    ├── LICENSE
    ├── README.md
    ├── composer.json
    ├── resources
    │   └── views
    │       └── index.blade.php
    ├── routes
    │   └── web.php
    └── src
        ├── PHPInfo.php
        ├── PHPInfoServiceProvider.php
        └── Http
            └── Controllers
                └── PHPInfoController.php
```

> 生成完扩展框架之后，你可能需要一边调试一边开发，所以可以参考下面的[本地安装](#%E6%9C%AC%E5%9C%B0%E5%AE%89%E8%A3%85)，先把扩展安装进系统中，继续开发

### [添加路由](#%E6%B7%BB%E5%8A%A0%E8%B7%AF%E7%94%B1)

首先添加一个路由，在`routes/web.php`中已经自动生成好了一个路由配置

```
<?php
use Encore\PHPInfo\Http\Controllers\PHPInfoController;

Route::get('phpinfo', PHPInfoController::class.'@index');
```

访问路径`phpinfo`，将会由`Encore\PHPInfo\Http\Controllers\PHPInfoController`控制器的`index`方法来处理这个请求。

### [设置扩展属性](#%E8%AE%BE%E7%BD%AE%E6%89%A9%E5%B1%95%E5%B1%9E%E6%80%A7)

`src/PHPInfo.php`作为扩展类，用来设置扩展的属性

```
<?php

namespace Encore\PHPInfo;

use Encore\Admin\Extension;

class PHPInfo extends Extension
{
    public $name = 'phpinfo';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'Phpinfo',
        'path'  => 'phpinfo',
        'icon'  => 'fa-gears',
    ];
}
```

这个文件用来设置这个扩展的一些属性，`$name`是这个扩展的名字，如果这个扩展有视图文件需要渲染，则必须指定这个扩展的`$views`属性，同样如果有静态资源文件需要发布，则必须设置`$assets`属性， 如果需要在左侧边栏增加一项菜单按钮，设置`$menu`属性

可以根据需要去掉不必要的属性，修改之后`src/PHPInfo.php`文件的代码为：

```
<?php

namespace Encore\PHPInfo;

use Encore\Admin\Extension;

class PHPInfo extends Extension
{
    public $name = 'phpinfo';

    public $views = __DIR__ . '/../resources/views';

    public $menu = [
        'title' => 'PHP info',
        'path'  => 'phpinfo',
        'icon'  => 'fa-exclamation',
    ];
}
```

然后打开`src/PHPInfoServiceProvider.php`，这个`ServiceProvider`将会在`laravel`启动的时候运行，用来将这个扩展的一些数据注册进系统中

### [加载视图](#%E5%8A%A0%E8%BD%BD%E8%A7%86%E5%9B%BE)

如果这个扩展需要加载视图文件，在`src/PHPInfoServiceProvider.php`的`handle`方法中加入以下的代码：

```
if ($views = $extension->views()) {
    $this->loadViewsFrom($views, 'phpinfo');
}
```

`loadViewsFrom`方法的的第一个参数为在扩展类`src/PHPInfo.php`中设置的视图属性，第二个参数是视图文件目录的命名空间，设置为`phpinfo`之后，在控制器中用`view('phpinfo::index')`来加载`resources/views`目录下的视图文件

### [引入静态资源](#%E5%BC%95%E5%85%A5%E9%9D%99%E6%80%81%E8%B5%84%E6%BA%90)

如果你的项目中有静态资源文件需要引入，先把文件放在`resources/assets`目录中，比如放入`resources/assets/foo.js`和`resources/assets/bar.css`这两个文件

接着在扩展类`src/PHPInfo.php`中设置`$assets`属性

```
    public $assets = __DIR__.'/../resources/assets';
```

然后在`src/PHPInfoServiceProvider.php`的`handle`方法中设置发布目录

```
if ($this->app->runningInConsole() && $assets = $extension->assets()) {
    $this->publishes(
        [$assets => public_path('vendor/laravel-admin-ext/phpinfo')],
        'phpinfo'
    );
}
```

安装完成之后，运行`php artisan vendor:publish --provider=Encore\PHPInfo\PHPInfoServiceProvider`，文件将会复制到`public/vendor/laravel-admin-ext/phpinfo`目录中。

我们需要在`laravel-admin`启动的时候在页面里引入这两个文件，需要在`src/PHPInfoServiceProvider.php`的`handle`方法加入下面的代码

```
use use Encore\Admin\Admin;

...

Admin::booting(function () {
    Admin::js('vendor/laravel-admin-ext/phpinfo/foo.js');
    Admin::css('vendor/laravel-admin-ext/phpinfo/bar.css');
});
```

这样就完成了静态资源的引入，在`phpinfo`这个扩展中，由于没有静态资源需要引入，所以可以忽略掉这一步

### [代码逻辑开发](#%E4%BB%A3%E7%A0%81%E9%80%BB%E8%BE%91%E5%BC%80%E5%8F%91)

这个扩展的逻辑是将`phpinfo`函数显示的PHP配置数据提取出来, 然后渲染新的视图渲染输出。在这一步，参考了[nova-phpinfo](https://github.com/davidpiesse/nova-phpinfo)的核心代码, 修改后的代码参考[PHPInfo.php](https://github.com/laravel-admin-extensions/phpinfo/blob/master/src/PHPInfo.php#L19-L50)，

接下来就是在控制器中通过调用`PHPInfo::toCollection`方法获取phpinfo的配置数据，然后渲染到视图里面，在这个扩展中，我省略了控制器这一步，直接在路由文件`routes/web.php`配置中渲染视图输出

```
<?php

use Encore\Admin\Layout\Content;
use Encore\PHPInfo\PHPInfo;

$path = PHPInfo::config('path', 'phpinfo');

Route::get($path, function (Content $content, PHPInfo $info) {
    $info = $info->toCollection();

    return $content
        ->header('PHP\'s configuration')
        ->description(' ')
        ->body(view('phpinfo::phpinfo', compact('info')));
});
```

这样一个完整的扩展就开发完成了，最终完整的代码可以参考[phpinfo](https://github.com/laravel-admin-extensions/phpinfo)

### [修改 composer.json & README.md](#%E4%BF%AE%E6%94%B9%20composer.json%20&%20README.md)

代码部分完成之后，需要修改`composer.json`里面的内容，将`description`、`keywords`、`license`、`authors`等内容替换为你的信息，然后不要忘记完善`README.md`，补充使用文档等相关信息。

## [安装](#%E5%AE%89%E8%A3%85)

完成了扩展开发之后，根据情况可以用下面的的方式安装你的扩展

### [本地安装](#%E6%9C%AC%E5%9C%B0%E5%AE%89%E8%A3%85)

在开发的过程中，一般需要一边调试一边开发，所以先按照下面的方式进行本地安装

打开你的项目中`composer.json`文件，在加入下面的配置

```
"repositories": [
    {
        "type": "path",
        "url": "app/Admin/extensions/laravel-admin-ext/phpinfo"
    }
]
```

然后运行`composer require laravel-admin-ext/phpinfo`完成安装，如果有静态文件需要发布，运行下面的命令

```
php artisan vendor:publish --provider=Encore\PHPInfo\PHPInfoServiceProvider
```

这样就完成了安装，打开`http://localhost/admin/phpinfo`访问这个扩展

### [远程安装](#%E8%BF%9C%E7%A8%8B%E5%AE%89%E8%A3%85)

如果开发完成之后，希望开源出来给大家使用，按照下面的步骤进行

#### 上传到Github

先登录你的Github，创建一个仓库，然后按照页面上的提示把你的代码push上去

```
git init
git remote add origin https://github.com/<your-name>/<your-repository>.git
git add .
git commit -am "Initial commit."
git push origin master
```

#### 发布release

可以用下面的方式在本地发布版本

```
git tag 0.0.1 && git push --tags
```

也可以在Github的仓库页面的`Releases`页面手动设置

#### 发布到Packagist.org

接下来就是发布你的项目到`Packagist.org`，如果没有账号的话，先注册一个，然后打开顶部导航的[Submit](https://packagist.org/packages/submit), 填入仓库地址提交

默认情况下，当您推送新代码时，Packagist.org不会自动更新，所以，您需要创建一个[GitHub服务钩子](https://packagist.org/about#how-to-update-packages)， 你也可以使用点击页面上的“Update”按钮手动更新它，但我建议自动执行这个过程

> 提交之后，由于各地的镜像同步时间的延迟，可能在用composer安装的时候，会暂时找不到你的项目，这个时候可能需要等待同步完成

发布完成之后就可以通过composer安装你的扩展了

#### 加入到https://github.com/laravel-admin-extensions

如果想把你开发的扩展加入到[laravel-admin-extensions](https://github.com/laravel-admin-extensions)中，欢迎用各种方式联系我，这样可以让更多人看到并使用你开发的工具。

## [总结](#%E6%80%BB%E7%BB%93)

在`Laravel nova`发布的不到一个月的时间里，就已经有了上百个扩展，虽然`laravel-admin`在很久之前就已经支持扩展的开发，但是由于文档的不足和缺乏时间来推动，所以这一块的功能一直没有怎么发展

我希望通过这篇文档，大家把自己在`laravel-admin`的使用过程中开发出来的功能, 开发成扩展沉淀下来，`laravel-admin`需要更多人的参与才能形成更好的生态系统

另外大家如果有好的idea，也不妨分享出来，其它有时间有精力的同学或许可以和你一起实现它。

> 不久的将来，`laravel-admin`将会尝试参考[fastadmin](https://www.fastadmin.net/)的方式，将部分扩展组件商业化, 欢迎大家参与
