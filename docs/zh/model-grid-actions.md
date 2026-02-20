# 数据操作

数据表格默认有3个行操作`编辑`、`查看`和`删除`，可以通过下面的方式关闭它们：

```
 $grid->actions(function ($actions) {

    // 去掉删除
    $actions->disableDelete();

    // 去掉编辑
    $actions->disableEdit();

    // 去掉查看
    $actions->disableView();
});

// 全部关闭
$grid->disableActions();
```

可以通过传入的`$actions`参数来获取当前行的数据：

```
 $grid->actions(function ($actions) {

    // 当前行的数据数组
    $actions->row;

    // 获取当前行主键值
    $actions->getKey();
});
```

默认情况下有一个批量删除的操作，有下面的一些使用方法

```
$grid->batchActions(function ($batch) {
    $batch->disableDelete();
});

// 去掉批量操作
$grid->disableBatchActions();
```

## [自定义操作](#%E8%87%AA%E5%AE%9A%E4%B9%89%E6%93%8D%E4%BD%9C)

> 从v1.7.3版本开始，以下文档废弃，请不要再使用。请使用[文档](model-grid-custom-actions).

如果有自定义的操作按钮，可以通过下面的方式添加：

```
$grid->actions(function ($actions) {

    // append一个操作
    $actions->append('<a href=""><i class="fa fa-eye"></i></a>');

    // prepend一个操作
    $actions->prepend('<a href=""><i class="fa fa-paper-plane"></i></a>');
});
```

如果有比较复杂的操作，可以参考下面的方式：

先定义操作类

```
<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class CheckRow
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.grid-check-row').on('click', function () {

    // Your code.
    console.log($(this).data('id'));

});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());

        return "<a class='btn btn-xs btn-success fa fa-check grid-check-row' data-id='{$this->id}'></a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
```

然后添加操作：

```
$grid->actions(function ($actions) {

    // 添加操作
    $actions->append(new CheckRow($actions->getKey()));
});
```
