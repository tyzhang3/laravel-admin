# 模型表格统计行

> since v1.6.13

## [基本使用](#%E5%9F%BA%E6%9C%AC%E4%BD%BF%E7%94%A8)

如果某一列字段的值为数字，并且需要统计之后显示在表格的底部，可以用下面的方法。

```
$grid->column('quantity', '数量')->totalRow();

$grid->column('amount', '金额')->totalRow();
```

在相应的字段后面调用`totalRow()`方法即可。

## [参数](#%E5%8F%82%E6%95%B0)

对这一列调用了`totalRow()`方法之后，默认会调用`Eloquent`的`sum`方法来累加这一列的值显示，同时`totalRow()`方法可以接受一个可选参数，如果传入的参数是字符串或者数字，那么会在底部统计行直接显示，比如在`id`列下面直接显示`合计`这两个字：

```
$grid->column('id', 'ID')->sortable()->totalRow('合计');
```

如果你想改变统计行的显示样式，可以通过传入一个匿名函数来修改它：

```
$grid->column('amount', '金额')->totalRow(function ($amount) {

    return "<span class='text-danger text-bold'><i class='fa fa-yen'></i> {$amount} 元</span>"

});
```
