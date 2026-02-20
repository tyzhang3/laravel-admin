# 列的基本使用

`model-grid`内置了很多对于列的操作方法，可以通过这些方法很灵活的操作列数据。

## [列属性](#%E5%88%97%E5%B1%9E%E6%80%A7)

列对象的`setAttributes()`方法用来给当前这一列的每一行添加HTML属性, 比较有用的一个场景是给当前列增加样式

```
$grid->column('title')->setAttributes(['style' => 'color:red;']);
```

基于`setAttributes()`方法封装了`style()`方法，直接添加样式，比如限制列的宽度：

```
$grid->column('desc')->style('max-width:200px;word-break:break-all;');
```

## [固定列](#%E5%9B%BA%E5%AE%9A%E5%88%97)

> Since v1.7.3

如果表格的字段比较多，挤压了列的显示，可以用过`fixColumns`方法来设置固定列

```
$grid->fixColumns(3, -2);
```

第一个参数表示固定从头开始的前三列，第二个参数表示固定从后往前数的两列，（第二个参数可不传，默认为-1）

## [设置列宽](#%E8%AE%BE%E7%BD%AE%E5%88%97%E5%AE%BD)

```
$grid->column('title')->width(200);
```

## [设置列颜色](#%E8%AE%BE%E7%BD%AE%E5%88%97%E9%A2%9C%E8%89%B2)

```
$grid->column('title')->color('#ffff00');
```

## [设置表头帮助信息](#%E8%AE%BE%E7%BD%AE%E8%A1%A8%E5%A4%B4%E5%B8%AE%E5%8A%A9%E4%BF%A1%E6%81%AF)

```
$grid->column('title')->help('这一列是...');
```

这一列的表头标题右边会出现一个问号问号icon，鼠标hover上去会弹出设置的help信息。

## [默认隐藏列](#%E9%BB%98%E8%AE%A4%E9%9A%90%E8%97%8F%E5%88%97)

```
$grid->column('created_at')->hide();
```

这一列默认会被隐藏，可以在右上角的列选择器开启显示

## [列排序](#%E5%88%97%E6%8E%92%E5%BA%8F)

使用`sortable()`方法把当前列设置为可排序列

```
$grid->column('created_at')->sortable();
```

这样列头部就会出现一个排序icon, 点击进行排序

## [帮助方法](#%E5%B8%AE%E5%8A%A9%E6%96%B9%E6%B3%95)

### [字符串操作](#%E5%AD%97%E7%AC%A6%E4%B8%B2%E6%93%8D%E4%BD%9C)

如果当前里的输出数据为字符串，那么可以通过链式方法调用`Illuminate\Support\Str`的方法。

比如有如下一列，显示`title`字段的字符串值:

```
$grid->column('title');
```

在`title`列输出的字符串基础上调用`Str::limit()`方法

```
$grid->column('title')->limit(30);
```

调用方法之后输出的还是字符串，所以可以继续调用`Illuminate\Support\Str`的方法：

```
$grid->column('title')->limit(30)->ucfirst();

$grid->column('title')->limit(30)->ucfirst()->substr(1, 10);
```

### [数组操作](#%E6%95%B0%E7%BB%84%E6%93%8D%E4%BD%9C)

如果当前列输出的是数组，可以直接链式调用`Illuminate\Support\Collection`方法。

比如`tags`列是从一对多关系取出来的数组数据：

```
$grid->column('tags');

array (
  0 =>
  array (
    'id' => '16',
    'name' => 'php',
    'created_at' => '2016-11-13 14:03:03',
    'updated_at' => '2016-12-25 04:29:35',
  ),
  1 =>
  array (
    'id' => '17',
    'name' => 'python',
    'created_at' => '2016-11-13 14:03:09',
    'updated_at' => '2016-12-25 04:30:27',
  ),
)
```

调用`Collection::pluck()`方法取出数组的中的`name`列

```
$grid->column('tags')->pluck('name');

array (
    0 => 'php',
    1 => 'python',
  ),
```

取出`name`列之后输出的还是数组，还能继续调用用`Illuminate\Support\Collection`的方法

```
$grid->column('tags')->pluck('name')->map('ucwords');

array (
    0 => 'Php',
    1 => 'Python',
  ),
```

将数组输出为字符串

```
$grid->column('tags')->pluck('name')->map('ucwords')->implode('-');

"Php-Python"
```

### [混合使用](#%E6%B7%B7%E5%90%88%E4%BD%BF%E7%94%A8)

在上面两种类型的方法调用中，只要上一步输出的是确定类型的值，便可以调用类型对应的方法，所以可以很灵活的混合使用。

比如`images`字段是存储多图片地址数组的JSON格式字符串类型：

```
$grid->column('images');

"['foo.jpg', 'bar.png']"

// 链式方法调用来显示多图
$grid->column('images')->display(function ($images) {

    return json_decode($images, true);

})->map(function ($path) {

    return 'http://localhost/images/'. $path;

})->image();
```

## [扩展列功能](#%E6%89%A9%E5%B1%95%E5%88%97%E5%8A%9F%E8%83%BD)

可以通过两种方式扩展列功能，第一种是通过匿名函数的方式。

在`app/Admin/bootstrap.php`加入以下代码:

```
use Encore\Admin\Grid\Column;

Column::extend('color', function ($value, $color) {
    return "<span style='color: $color'>$value</span>";
});
```

然后在`model-grid`中使用这个扩展：

```
$grid->column('title')->color('#ccc');
```

如果列显示逻辑比较复杂，可以通过扩展类来实现。

扩展类`app/Admin/Extensions/Popover.php`:

```
<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class Popover extends AbstractDisplayer
{
    public function display($placement = 'left')
    {
        Admin::script("$('[data-toggle=\"popover\"]').popover()");

        return <<<EOT
<button type="button"
    class="btn btn-secondary"
    title="popover"
    data-container="body"
    data-toggle="popover"
    data-placement="$placement"
    data-content="{$this->value}"
    >
  弹出提示
</button>

EOT;

    }
}
```

然后在`app/Admin/bootstrap.php`注册扩展类：

```
use Encore\Admin\Grid\Column;
use App\Admin\Extensions\Popover;

Column::extend('popover', Popover::class);
```

然后就能在`model-grid`中使用了：

```
$grid->desciption()->popover('right');
```
