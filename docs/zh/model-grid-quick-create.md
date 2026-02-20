# 快捷创建

> Since v1.7.3

在表格中开启这个功能之后，会在表格头部增加一个form表单来创建数据，对于一些简单的表格页面，可以方便快速创建数据，不用跳转到创建页面操作

![WX20190722-004700@2x](https://user-images.githubusercontent.com/1479100/61594099-4b105700-ac1a-11e9-864a-6c5ee2312b78.png)

开启方式：

```
$grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
    $create->text('name', '名称');
    $create->email('email', '邮箱');
});
```

> 需要注意的是，快捷创建表单中的每一项，在form表单页面要设置相同类型的表单项。

表单支持的表单项有下面的几种类型

## [文本输入框](#%E6%96%87%E6%9C%AC%E8%BE%93%E5%85%A5%E6%A1%86)

文本输入框

```
$create->text('column_name', 'placeholder...');
```

## [邮箱输入框](#%E9%82%AE%E7%AE%B1%E8%BE%93%E5%85%A5%E6%A1%86)

邮箱输入框

```
$create->email('column_name', 'placeholder...');
```

## [IP输入框](#IP%E8%BE%93%E5%85%A5%E6%A1%86)

ip地址输入框

```
$create->ip('column_name', 'placeholder...');
```

## [URL输入框](#URL%E8%BE%93%E5%85%A5%E6%A1%86)

url输入框

```
$create->url('column_name', 'placeholder...');
```

## [密码输入框](#%E5%AF%86%E7%A0%81%E8%BE%93%E5%85%A5%E6%A1%86)

密码输入框

```
$create->password('column_name', 'placeholder...');
```

## [手机号输入框](#%E6%89%8B%E6%9C%BA%E5%8F%B7%E8%BE%93%E5%85%A5%E6%A1%86)

手机号输入框

```
$create->mobile('column_name', 'placeholder...');
```

## [整数输入框](#%E6%95%B4%E6%95%B0%E8%BE%93%E5%85%A5%E6%A1%86)

整形数字输入框

```
$create->integer('column_name', 'placeholder...');
```

## [单选框](#%E5%8D%95%E9%80%89%E6%A1%86)

单选框

```
$create->select('column_name', 'placeholder...')->options([
    1 => 'foo',
    2 => 'bar',
]);
```

## [多选框](#%E5%A4%9A%E9%80%89%E6%A1%86)

多选框

```
$create->multipleSelect('column_name', 'placeholder...')->options([
    1 => 'foo',
    2 => 'bar',
]);
```

## [日期时间选择](#%E6%97%A5%E6%9C%9F%E6%97%B6%E9%97%B4%E9%80%89%E6%8B%A9)

时间日期输入框

```
$create->datetime('column_name', 'placeholder...');
```

## [时间选择](#%E6%97%B6%E9%97%B4%E9%80%89%E6%8B%A9)

时间输入框

```
$create->time('column_name', 'placeholder...');
```

## [日期选择](#%E6%97%A5%E6%9C%9F%E9%80%89%E6%8B%A9)

```
$create->date('column_name', 'placeholder...');
```
