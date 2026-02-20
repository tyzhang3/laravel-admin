# 数据软删除

> Since v1.7.9

先参考Laravel文档实现模型的[软删除](https://learnku.com/docs/laravel/6.x/eloquent/5176#soft-deleting):

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
}
```

这样在grid列表中显示的数据都是未被删除的数据

```
$grid = new Grid(new Post);

$grid->id('ID')->sortable();
$grid->title('Title');
$grid->created_at('Created at');
$grid->updated_at('Updated at');

return $grid;
```

## [回收站入口](#%E5%9B%9E%E6%94%B6%E7%AB%99%E5%85%A5%E5%8F%A3)

接下来需要增加一个入口，能让我们看到被软删除的数据，这里可以使用`model-grid`的`范围过滤器`来实现

```
$grid->filter(function () {

    // 范围过滤器，调用模型的`onlyTrashed`方法，查询出被软删除的数据。
    $filter->scope('trashed', '回收站')->onlyTrashed();

});
```

在表头的筛选按钮的下拉菜单中就会出现一个`回收站`按钮，点击它，就会调用模型的`onlyTrashed`方法，从表中查询出被删除的数据，也就是回收站中的数据。

![WX20191220-105515](https://user-images.githubusercontent.com/1479100/71235280-add75d00-2336-11ea-97f4-bb8d3f65b20c.png)

## [行恢复操作](#%E8%A1%8C%E6%81%A2%E5%A4%8D%E6%93%8D%E4%BD%9C)

按照下面的方法，我们可以在回收站中的每一行数据加上一个恢复操作，方便恢复数据

先定义操作类`app/Admin/Actions/Post/Restore.php`：

```
<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Restore extends RowAction
{
    public $name = '恢复';

    public function handle(Model $model)
    {
        $model->restore();

        return $this->response()->success('已恢复')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定恢复吗？');
    }
}
```

添加到行操作:

```
use App\Admin\Actions\Post\Restore;

$grid->actions(function ($actions) {

    if (\request('_scope_') == 'trashed') {
        $actions->add(new Restore());
    }

});
```

## [批量恢复操作](#%E6%89%B9%E9%87%8F%E6%81%A2%E5%A4%8D%E6%93%8D%E4%BD%9C)

先定义操作类`app/Admin/Actions/Post/BatchRestore.php`：

```
<?php

namespace App\Admin\Actions\Post;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchRestore extends BatchAction
{
    public $name = '恢复';

    public function handle(Collection $collection)
    {
        $collection->each->restore();

        return $this->response()->success('已恢复')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确定恢复吗？');
    }
}
```

添加到批量操作:

```
use App\Admin\Actions\Post\BatchRestore;

$grid->batchActions(function ($batch) {

    if (\request('_scope_') == 'trashed') {
        $batch->add(new BatchRestore());
    }

});
```
