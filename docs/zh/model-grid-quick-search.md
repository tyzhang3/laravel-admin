# 快捷搜索

> Since v1.7.0

`快捷搜索`是除了`filter`之外的另一个表格数据搜索方式，用来快速过滤你想要的数据，开启方式如下：

```
$grid->quickSearch();
```

这样表头会出现一个搜索框:

![WX20190608-093334](https://user-images.githubusercontent.com/1479100/59140479-8e976480-89d0-11e9-8496-db958c012128.png)

通过给`quickSearch`方法传入不同的参数，来设置不同的搜索方式，有下面几种使用方法

## [Like搜索](#Like%E6%90%9C%E7%B4%A2)

第一种方式，通过设置字段名称来进行简单的`like`查询

```
$grid->quickSearch('title');

// 提交后模型会执行下面的查询

$model->where('title', 'like', "%{$input}%");
```

或者对多个字段做`like`查询:

```
$grid->quickSearch('title', 'desc', 'content');

// 提交后模型会执行下面的查询

$model->where('title', 'like', "%{$input}%")
    ->orWhere('desc', 'like', "%{$input}%")
    ->orWhere('content', 'like', "%{$input}%");
```

## [自定义搜索](#%E8%87%AA%E5%AE%9A%E4%B9%89%E6%90%9C%E7%B4%A2)

第二种方式可以让你更灵活的控制搜索条件

```
$grid->quickSearch(function ($model, $query) {
    $model->where('title', $query)->orWhere('desc', 'like', "%{$query}%");
});
```

其中闭包的参数`$query`为你填入搜索框中的内容，提交之后进行闭包中的查询。

## [快捷语法搜索](#%E5%BF%AB%E6%8D%B7%E8%AF%AD%E6%B3%95%E6%90%9C%E7%B4%A2)

第三种方式参考了`Github`的搜索语法，来进行快捷搜索，调用方式：

```
// 不传参数
$grid->quickSearch();
```

填入搜索框的内容按照以下的语法，在提交之后会进行相应的查询 :

### [比较查询](#%E6%AF%94%E8%BE%83%E6%9F%A5%E8%AF%A2)

`title:foo` 、`title:!foo`

```
$model->where('title', 'foo');

$model->where('title', '!=', 'foo');
```

`rate:>10`、`rate:<10`、`rate:>=10`、`rate:<=10`

```
$model->where('rate', '>', 10);

$model->where('rate', '<', 10);

$model->where('rate', '>=', 10);

$model->where('rate', '<=', 10);
```

### [In、NotIn查询](#In%E3%80%81NotIn%E6%9F%A5%E8%AF%A2)

`status:(1,2,3,4)`、`status:!(1,2,3,4)`

```
$model->whereIn('status', [1,2,3,4]);

$model->whereNotIn('status', [1,2,3,4]);
```

### [Between查询](#Between%E6%9F%A5%E8%AF%A2)

`score:[1,10]`

```
$model->whereBetween('score', [1, 10]);
```

### [时间日期函数查询](#%E6%97%B6%E9%97%B4%E6%97%A5%E6%9C%9F%E5%87%BD%E6%95%B0%E6%9F%A5%E8%AF%A2)

`created_at:date,2019-06-08`

```
$model->whereDate('created_at', '2019-06-08');
```

`created_at:time,09:57:45`

```
$model->whereTime('created_at', '09:57:45');
```

`created_at:day,08`

```
$model->whereDay('created_at', '08');
```

`created_at:month,06`

```
$model->whereMonth('created_at', '06');
```

`created_at:year,2019`

```
$model->whereYear('created_at', '2019');
```

### [Like查询](#Like%E6%9F%A5%E8%AF%A2)

`content:%Laudantium%`

```
$model->where('content', 'like', 'Laudantium');
```

### [正则查询](#%E6%AD%A3%E5%88%99%E6%9F%A5%E8%AF%A2)

`username:/song/`

```
$model->where('username', 'REGEXP', 'song');
```

> 这里请使用MYSQL正则语法

### [多条件组合搜索](#%E5%A4%9A%E6%9D%A1%E4%BB%B6%E7%BB%84%E5%90%88%E6%90%9C%E7%B4%A2)

用逗号隔开多个搜索语句就可以实现多个字段的AND查询，比如`username:%song% status:(1,2,3)`, 提交之后会运行下面的搜索

```
$model->where('username', 'like', '%song%')->whereIn('status', [1, 2, 3]);
```

如果某一个条件是`OR`查询, 只需要在语句单元前增加一个`|`符号即可： `username:%song% |status:(1,2,3)`

```
$model->where('username', 'like', '%song%')->orWhereIn('status', [1, 2, 3]);
```

如果填入的查询文字中包含空格，需要放在双引号里面：`updated_at:"2019-06-08 09:57:45"`

### [Label作为查询字段名称](#Label%E4%BD%9C%E4%B8%BA%E6%9F%A5%E8%AF%A2%E5%AD%97%E6%AE%B5%E5%90%8D%E7%A7%B0)

不方便得到字段名的情况下，可以直接使用`label`名称作为查询字段

```
 // 比如设置了`user_status`的表头列名为`用户状态`
$grid->column('user_status', '用户状态');
```

那么可以填入`用户状态:(1,2,3)`来执行下面的查询

```
$model->whereIn('user_status', [1, 2, 3]);
```
