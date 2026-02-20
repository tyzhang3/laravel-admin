# 数据导出

`model-grid`内置了csv格式文件的导出，默认将列表中显示的数据导出为csv格式的文件。

> since v1.7.16

使用默认的csv导出，有下面一些可供使用的方法。

```
$grid->export(function ($export) {

    $export->filename('Filename.csv');

    $export->except(['column1', 'column2' ...]);

    $export->only(['column3', 'column4' ...]);

    $export->originalValue(['column1', 'column2' ...]);

    $export->column('column_5', function ($value, $original) {
        return $value;
    });
});
```

`$export->filename($filename);`用来指定导出文件的名称，不设置的话默认为`表名.csv`

`$export->except([]);`用来指定哪些列不需要被导出，指定了之后，相关的列将不会被导出, 反之，使用`$export->only([]);`方法用来指定只能导出哪些列。

很多情况下某些列会被修改之后显示在页面上，比如对列使用了`$grid->column('name')->label()`方法之后，那么导出的列内容会是一段HTML，如果需要某些列导出存在数据库中的原始内容，使用`originalValue`方法

```
$export->originalValue(['name']);
```

最后，如果你想自定义某些列的导出内容，使用`column`方法

```
$export->column('column_5', function ($value, $original) {
    // return $value;
)};
```

其中传入闭包函数中的`$value`和`$original`为该列的原始值和应用过某些方法之后被修改之后的值，你可以在闭包函数中实现自己的逻辑。

## [Laravel-Excel v3.\*](#Laravel-Excel%20v3.*)

在`v1.6.12`版本之后增加了对`Laravel-Excel 3.*`版本的导出支持。

首先按照文档安装好[Laravel-Excel](https://github.com/Maatwebsite/Laravel-Excel)。

然后创建导出类：

```
<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\ExcelExporter; 

class PostsExporter extends ExcelExporter
{
    protected $fileName = '文章列表.xlsx';

    protected $columns = [
        'id'      => 'ID',
        'title'   => '标题',
        'content' => '内容',
    ];
}
```

然后在Grid中使用这个导出类：

```
use App\Admin\Extensions\PostsExporter;

$grid->exporter(new PostsExporter());
```

通过上面的`$columns`设置，导出时只会导出三个指定的三个字段，如果你要导出所有的字段，指定按照顺序指定每一个字段的名称即可

```
class PostsExporter extends ExcelExporter
{
    protected $fileName = '文章列表.xlsx';

    protected $headings = ['ID', '标题', '内容' ... ];
}
```

### [数据修改](#%E6%95%B0%E6%8D%AE%E4%BF%AE%E6%94%B9)

按照上面的方法，会将数据库中存储的原始数据导出，如果你希望对数据进行预处理，参考下面的方式：

```
<?php

namespace App\Admin\Extensions\Export;

use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExporter extends ExcelExporter implements WithMapping
{
    protected $fileName = '用户列表.xlsx';

    protected $columns = [
        'id'                    => 'ID',
        'name'                  => '姓名',
        'status'                => '状态',
        'profile.homepage'      => '主页',
    ];

    public function map($user) : array
    {
        return [
            $user->id,
            $user->name,
            $user->status ? 'yes' : 'no';           // 字段数据替换
            data_get($user, 'profile.homepage'),    // 读取关联关系数据
        ];
    }
}
```

## [Laravel-Excel v2.\*](#Laravel-Excel%20v2.*)

本示例用[Laravel-Excel](https://github.com/Maatwebsite/Laravel-Excel)作为excel操作库，当然也可以使用任何其他excel库

首先安装好它：

```
composer require maatwebsite/excel:~2.1.0

php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

然后新建自定义导出类，比如`app/Admin/Extensions/ExcelExpoter.php`:

```
<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    public function export()
    {
        Excel::create('Filename', function($excel) {

            $excel->sheet('Sheetname', function($sheet) {

                // 这段逻辑是从表格数据中取出需要导出的字段
                $rows = collect($this->getData())->map(function ($item) {
                    return array_only($item, ['id', 'title', 'content', 'rate', 'keywords']);
                });

                $sheet->rows($rows);

            });

        })->export('xls');
    }
}
```

然后在`model-grid`中使用这个导出类：

```
use App\Admin\Extensions\ExcelExpoter;

$grid->exporter(new ExcelExpoter());
```

有关更多`Laravel-Excel`的使用方法，参考[laravel-excel/docs](http://www.maatwebsite.nl/laravel-excel/docs)
