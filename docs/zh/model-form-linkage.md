# 表单联动

> Since v1.8.0

表单联动是指，在选择表单项的指定的选项时，联动显示其他的表单项。

![linkage](https://user-images.githubusercontent.com/1479100/82905667-ba6f7980-9f96-11ea-97f1-8f5d565b830c.gif)

目前支持的表单联动的组件有：

- `select`
- `multipleSelect`
- `belongsTo`
- `belongsToMany`
- `radio`
- `radioButton`
- `radioCard`
- `checkbox`
- `checkboxButton`
- `checkboxCard`

## [使用方法](#%E4%BD%BF%E7%94%A8%E6%96%B9%E6%B3%95)

可以将上面的组件分为单选和多选两种类型，其中`select`、`radio`、`belongsTo`、`radioButton`、`radioCard`为单选组件，其它为多选组件

### [单选组件](#%E5%8D%95%E9%80%89%E7%BB%84%E4%BB%B6)

下面的例子中，选择不同的国籍类型，将会切换选择不同的联动表单项：

```
$form->radio('nationality', '国籍')
    ->options([
        1 => '本国',
        2 => '外国',
    ])->when(1, function (Form $form) { 

        $form->text('name', '姓名');
        $form->text('idcard', '身份证');

    })->when(2, function (Form $form) { 

        $form->text('name', '姓名');
        $form->text('passport', '护照');

    });
```

上例中，方法`when(1, $callback)`等效于`when('=', 1, $callback)`, 如果用操作符`=`，则可以省略这个参数

同时也支持这些操作符，`=`、`>`、`>=`、`<`、`<=`、`!=`、`in`、`notIn`，使用方法如下：

```
$form->radio('check')
    ->when('>', 1, function () {

    })->when('>=', 2, function () {

    })->when('in', [5, 6], function () {

    })->when('notIn', [7, 8], function () {

    });
```

`select`、`belongsTo`、`radioButton`、`radioCard`等组件的使用方法和`radio`是一样的。

### [多选组件](#%E5%A4%9A%E9%80%89%E7%BB%84%E4%BB%B6)

多选组件支持三个操作符：`=`、`!=`、`has`、

```
$form->checkbox('nationality', '国籍')
    ->options([
        1 => '中国',
        2 => '外国',
    ])->when([1, 2], function (Form $form) { 

        $form->text('name', '姓名');
        $form->text('idcard', '身份证');

    })->when('has', 2, function (Form $form) { 

        $form->text('name', '姓名');
        $form->text('passport', '护照');

    });
```

`multipleSelect`、`belongsToMany`、`checkboxButton`、`checkboxCard`等组件的使用方法和`checkbox`是一样的。
