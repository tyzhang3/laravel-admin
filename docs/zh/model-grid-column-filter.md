# 列过滤器

> Since v.1.7.2

这个功能在表头给相应的列设置一个过滤器，可以更方便的根据这一列进行数据表格过滤操作

![WX20190623-190256](https://user-images.githubusercontent.com/1479100/59975300-843cb380-95e9-11e9-9479-bf3f7329cffb.png)

过滤器有下面三类形式：

## [字符串比较查询](#%E5%AD%97%E7%AC%A6%E4%B8%B2%E6%AF%94%E8%BE%83%E6%9F%A5%E8%AF%A2)

```
$grid->column('code')->filter();
```

上面的调用可以给`code`这一列的头部加上一个`input`类型的过滤器，点击过滤器icon展开过滤器，输入查询提交后，会对这一列执行`等于`查询。

如果需要like查询:

```
$grid->column('title')->filter('like');
```

![WX20190623-192038](https://user-images.githubusercontent.com/1479100/59975592-3ecdb580-95ec-11e9-94f5-a6d5a33fb889.png)

如果字段是时间、日期相关的字段，可以使用下面的方法

```
$grid->column('date')->filter('date');

$grid->column('time')->filter('time');

$grid->column('datetime')->filter('datetime');
```

## [多选查询](#%E5%A4%9A%E9%80%89%E6%9F%A5%E8%AF%A2)

假设需要在表格数据中通过`status`字段过滤一个或者多个状态的数据，使用`多选过滤`可以非常方便的实现

```
$grid->column('status', '状态')->filter([
    0 => '未知',
    1 => '已下单',
    2 => '已付款',
    3 => '已取消',
]);
```

![WX20190623-192234](https://user-images.githubusercontent.com/1479100/59975605-79cfe900-95ec-11e9-8d29-c4d169d9dcff.png)

## [范围查询](#%E8%8C%83%E5%9B%B4%E6%9F%A5%E8%AF%A2)

假设需要通过`price`字段过滤出某个价格范围内的数据

```
$grid->column('price', '价格')->filter('range');
```

![WX20190623-192707](https://user-images.githubusercontent.com/1479100/59975636-de8b4380-95ec-11e9-82f9-ddd45d05152f.png)

或者是时间、日期范围的过滤

```
$grid->column('date')->filter('range', 'date');

$grid->column('time')->filter('range', 'time');

$grid->column('datetime')->filter('range', 'datetime');
```

![WX20190623-192109](https://user-images.githubusercontent.com/1479100/59975593-3ecdb580-95ec-11e9-9cfc-32dbe27a175a.png)
