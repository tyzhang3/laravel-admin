# 详情组件

## [分隔线](#%E5%88%86%E9%9A%94%E7%BA%BF)

如果要在字段之间添加一条分隔线：

```
$show->divider();
```

## [修改显示内容](#%E4%BF%AE%E6%94%B9%E6%98%BE%E7%A4%BA%E5%86%85%E5%AE%B9)

用下面的方法修改显示内容

```
$show->title()->as(function ($title) {
    return "<{$title}>";
});

$show->contents()->as(function ($content) {
    return "<pre>{$content}</pre>";
});
```

下面是通过`as`方法内置实现的几个常用的显示样式.

## [image](#image)

字段`avatar`的内容是图片的路径或者url，可以将它显示为图片：

```
$show->avatar()->image();
```

`image()`方法的参数参考[Field::image()](https://github.com/z-song/laravel-admin/blob/8c1888392b063a56b0f096d3bb2a7c72aa846f31/src/Show/Field.php#L200)

## [file](#file)

字段`document`的内容是文件的路径或者url，可以将它显示为文件：

```
$show->avatar()->file();
```

`file()`方法的参数参考[Field::file()](https://github.com/z-song/laravel-admin/blob/8c1888392b063a56b0f096d3bb2a7c72aa846f31/src/Show/Field.php#L235)

## [link](#link)

字段`homepage`的内容是url链接，可以将它显示为HTML链接：

```
$show->homepage()->link();
```

`link()`方法的参数参考[Field::link()](https://github.com/z-song/laravel-admin/blob/8c1888392b063a56b0f096d3bb2a7c72aa846f31/src/Show/Field.php#L289)

## [label](#label)

将字段`tag`的内容显示为label：

```
$show->tag()->label();
```

`label()`方法的参数参考[Field::label()](https://github.com/z-song/laravel-admin/blob/8c1888392b063a56b0f096d3bb2a7c72aa846f31/src/Show/Field.php#L305)

## [badge](#badge)

将字段`rate`的内容显示为badge：

```
$show->rate()->badge();
```

`badge()`方法的参数参考[Field::badge()](https://github.com/z-song/laravel-admin/blob/8c1888392b063a56b0f096d3bb2a7c72aa846f31/src/Show/Field.php#L325)

## [json](#json)

将字段`extra`的内容显示为json格式输出：

```
$show->extra()->json();
```

`json()`方法的参数参考[Field::json()](https://github.com/z-song/laravel-admin/blob/8c1888392b063a56b0f096d3bb2a7c72aa846f31/src/Show/Field.php#L343)

## [using](#using)

如果字段`gender`的取值为`f`、`m`，分别需要用`女`、`男`来显示

```
$show->gender()->using(['f' => '女', 'm' => '男']);
```

### [图片轮播](#%E5%9B%BE%E7%89%87%E8%BD%AE%E6%92%AD)

如果字段值为图片数组，可以用下面的调用显示为图片轮播组件

```
$show->field('images')->carousel();

// 设置显示尺寸和图片服务器
$show->field('images')->carousel($width = 300, int $height = 200, $server);
```

## [显示文件尺寸](#%E6%98%BE%E7%A4%BA%E6%96%87%E4%BB%B6%E5%B0%BA%E5%AF%B8)

如果字段数据是表示文件大小的字节数，可以通过调用`filezise`方法来显示更有可读性的文字

```
$show->field('file_size')->filesize();
```

这样数值`199812019`将会显示为`190.56 MB`
