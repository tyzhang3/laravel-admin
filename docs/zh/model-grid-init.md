# 表格初始化

> Since v1.6.10 [#3054](https://github.com/z-song/laravel-admin/pull/3054)

在版本`v1.6.10`中，增加了表格的初始化设置功能，用来全局设置表格。

比如你需要在所有的表格中，禁用掉某些操作，可以在`app/Admin/bootstrap.php`加入下面的代码：

```
use Encore\Admin\Grid;

Grid::init(function (Grid $grid) {

    $grid->disableActions();

    $grid->disablePagination();

    $grid->disableCreateButton();

    $grid->disableFilter();

    $grid->disableRowSelector();

    $grid->disableColumnSelector();

    $grid->disableTools();

    $grid->disableExport();

    $grid->actions(function (Grid\Displayers\Actions $actions) {
        $actions->disableView();
        $actions->disableEdit();
        $actions->disableDelete();
    });
});
```

这样就不用在每一个控制器的代码中来设置了。

如果全局设置后，要在其中某一个表格中开启设置，比如开启显示操作列，在对应的实例上调用`$grid->disableActions(false);`就可以了
