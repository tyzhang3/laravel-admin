# 表格数据源

## [数据来自模型](#%E6%95%B0%E6%8D%AE%E6%9D%A5%E8%87%AA%E6%A8%A1%E5%9E%8B)

如果使用模型来获取数据，那么修改来源数据就非常简单：

```
// 添加默认查询条件
$grid->model()->where('id', '>', 100);

// 设置初始排序条件
$grid->model()->orderBy('id', 'desc');

...
```

其它查询方法可以参考`eloquent`的查询方法.

## [数据来自外部API](#%E6%95%B0%E6%8D%AE%E6%9D%A5%E8%87%AA%E5%A4%96%E9%83%A8API)

由于`model-grid`的数据是依赖`Eloquent model`来查询获取的，所以如果你的列表数据不是来自数据库而是外部API，那么就不能直接使用常规的方法来处理。

由于外部数据的获取方式没有统一的方式，所以laravel-admin中没有封装统一的方法来处理外部数据，但是我们可以通过覆盖`Eloquent model`的一些常用方法来实现数据的读取、查询、排序等操作。

下面的例子用`豆瓣电影`的API获取并展示数据：

```
<?php

namespace App\Models\Movie;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class InTheater extends Model
{
    public function paginate()
    {
        $perPage = Request::get('per_page', 10);

        $page = Request::get('page', 1);

        $start = ($page-1)*$perPage;

        $data = file_get_contents("https://api.douban.com/v2/movie/in_theaters?city=上海&start=$start&count=$perPage");

        $data = json_decode($data, true);

        extract($data);

        $movies = static::hydrate($subjects);

        $paginator = new LengthAwarePaginator($movies, $total, $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }

    public static function with($relations)
    {
        return new static;
    }

    // 覆盖`orderBy`来收集排序的字段和方向
    public function orderBy($column, $direction = 'asc')
    {

    }

    // 覆盖`where`来收集筛选的字段和条件
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {

    }

    ...
}
```

通过覆盖模型的`paginate`和`with`方法获取API数据, 通过`findOrFail`方法来获取单项数据展示在表单中

同理如果要在form表单中获取或者保存数据那么也可以通过覆盖相应的方法来实现：

```
// 获取单项数据展示在form中
public function findOrFail($id)
{
    $data = file_get_contents("http://api.douban.com/v2/movie/subject/$id");

    $data = json_decode($data, true);

    return static::newFromBuilder($data);
}

// 保存提交的form数据
public function save(array $options = [])
{
    $attributes = $this->getAttributes();

    // save $attributes
}
```

## [数据来自复杂SQL查询](#%E6%95%B0%E6%8D%AE%E6%9D%A5%E8%87%AA%E5%A4%8D%E6%9D%82SQL%E6%9F%A5%E8%AF%A2)

如果来源数据需要执行比较复杂的SQL语句获取，那么有两个办法, 第一个办法就是上面的方法，覆盖掉model的方法实现

```
public function paginate()
{
    $perPage = Request::get('per_page', 10);

    $page = Request::get('page', 1);

    $start = ($page-1)*$perPage;

    // 运行sql获取数据数组
    $sql = 'select * from ...';

    $result = DB::select($sql);

    $movies = static::hydrate($result);

    $paginator = new LengthAwarePaginator($movies, $total, $perPage);

    $paginator->setPath(url()->current());

    return $paginator;
}

public static function with($relations)
{
    return new static;
}
```

第二个方式是在数据库中建立视图和model绑定（未测试过，理论上可行）
