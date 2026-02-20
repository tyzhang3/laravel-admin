# JSON格式字段处理

`laravel-admin`的表单提供了下面几个组件来处理`JSON`格式的字段，方便用来处理`JOSN`格式的对象、一维数组、二维数组等对象。

在使用下面的组件之前，**必须**先在模型中设置该字段的格式转换

```
class Foo extends Model
{
    protected $casts = [
        'column_name' => 'json',
    ];
}
```

## [键值对象](#%E9%94%AE%E5%80%BC%E5%AF%B9%E8%B1%A1)

![QQ20200519-125825](https://user-images.githubusercontent.com/1479100/82286657-c70d3400-99d0-11ea-9ff0-c29ba06bcded.png)

如果你的字段存储的是不固定`键`的`{"field":"value"}`格式，可以用`keyValue`组件:

```
$form->keyValue('column_name');

// 设置校验规则
$form->keyValue('column_name')->rules('required|min:5');
```

## [固定键值对象](#%E5%9B%BA%E5%AE%9A%E9%94%AE%E5%80%BC%E5%AF%B9%E8%B1%A1)

![QQ20200519-130014](https://user-images.githubusercontent.com/1479100/82286671-cf656f00-99d0-11ea-8b48-6c49fe74731e.png)

用于处理`mysql`的`JSON`类型字段数据或者`mongodb`的`object`类型数据，也可以将多个field的数据值以`JSON`字符串的形式存储在`mysql`的字符串类型字段中

适用于有固定键值的JSON类型字段

```
$form->embeds('column_name', function ($form) {

    $form->text('key1')->rules('required');
    $form->email('key2')->rules('required');
    $form->datetime('key3');

    $form->dateRange('key4', 'key5', '范围')->rules('required');
});

// 自定义标题
$form->embeds('column_name', '字段标题', function ($form) {
    ...
});
```

回调函数里面构建表单元素的方法调用和外面是一样的。

## [一维数组](#%E4%B8%80%E7%BB%B4%E6%95%B0%E7%BB%84)

![QQ20200519-125926](https://user-images.githubusercontent.com/1479100/82286670-ce344200-99d0-11ea-8003-6481fa92410e.png)

如果你的字段是用来存储`["foo", "Bar"]`格式的一维数组, 可以使用`list`组件:

```
$form->list('column_name');

// 设置校验规则
$form->list('column_name')->rules('required|min:5');

// 设置最大和最小元素个数
$form->list('column_name')->max(10)->min(5);
```

## [二维数组](#%E4%BA%8C%E7%BB%B4%E6%95%B0%E7%BB%84)

> since v.16.13

![WX20190505-124413](https://user-images.githubusercontent.com/1479100/57188574-8a8ca880-6f33-11e9-8e64-6dc44976cf68.png)

如果某一个字段存储的是`json`格式的二维数组，可以使用`table`表单组件来实现快速的编辑：

```
$form->table('column_name', function ($table) {
    $table->text('key');
    $table->text('value');
    $table->text('desc');
});
```

同时需要在模型里面给这个字段设置访问器和修改器:

```
    public function getColumnNameAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setColumnNameAttribute($value)
    {
        $this->attributes['column_name'] = json_encode(array_values($value));
    }
```

这个组件类似于`hasMany`组件，不过是用来处理单个字段的情况，适用于简单的二维数据。
