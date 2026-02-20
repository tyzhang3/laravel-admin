# 页面内容 & 布局

后台页面的整体布局如下:

```
---------------------------------------
|                头部                  |
|-------------------------------------|
|    |                                |
|    |                                |
| 菜 |              内容               |
| 单 |                                |
| 栏 |                                |
|    |--------------------------------|
|    |           脚部                  |
---------------------------------------
```

其中头部，菜单栏、脚部三部分有框架维护，开发工作主要放在*内容*区域，参考下面的文档来定义内容区域的显示。

## [页面内容](#%E9%A1%B5%E9%9D%A2%E5%86%85%E5%AE%B9)

`laravel-admin`的布局可参考后台首页的布局文件[HomeController.php](https://github.com/z-song/laravel-admin/blob/master/src/Console/stubs/HomeController.stub)的`index()`方法。

`Encore\Admin\Layout\Content`类用来实现内容区的布局。`Content::body($content)`方法用来添加页面内容：

一个简单的后台页面代码如下：

```
public function index(Content $content)
{
    // 选填
    $content->title('填写页面头标题');

    // 选填
    $content->description('填写页面描述小标题');

    // 添加面包屑导航 since v1.5.7
    $content->breadcrumb(
        ['text' => '首页', 'url' => '/admin'],
        ['text' => '用户管理', 'url' => '/admin/users'],
        ['text' => '编辑用户']
    );

    // 填充页面body部分，这里可以填入任何可被渲染的对象
    $content->body('hello world');

    // 在body中添加另一段内容
    $content->body('foo bar');

    // `row`是`body`方法的别名
    $content->row('hello world');

    // 直接渲染视图输出，Since v1.6.12
    $content->view('dashboard', ['data' => 'foo']);

    return $content;
}
```

其中`$content->body();`方法可以接受任何可字符串化的对象作为参数，可以是字符串、数字、包含了`__toString`方法的对象，实现了`Renderable`、`Htmlable`接口的对象，包括laravel的视图。

### [自定义页面内容](#%E8%87%AA%E5%AE%9A%E4%B9%89%E9%A1%B5%E9%9D%A2%E5%86%85%E5%AE%B9)

内容区域完全可以自定义显示，假设你要自定义模型详情页的显示，先在控制器中定义show方法:

```
use App\Models\Product;
use Encore\Admin\Layout\Content;

class ProductController
{
    public function show($id, Content $content)
    {
        $product = Product::find($id);

        return $content->title('详情')
            ->description('简介')
            ->view('product.show', $product->toArray());
    }
}
```

然后定义视图文件`resources/product/show.blade.php`:

```
<div class="box box-solid">
    <div class="box-header with-border">
        <i class="fa fa-text-width"></i>
        <h3 class="box-title">用户详情</h3>
    </div>
    <div class="box-body">
        <dl class="dl-horizontal">
            <dt>姓名</dt>
            <dd>{{ $name }}</dd>
            <dt>注册时间</dt>
            <dd>{{ $created_at }}</dd>
        </dl>
    </div>
</div>
```

`laravel-admin`使用`adminlte2`作为前端框架，视图文件的样式，可以参考[adminte](https://adminlte.io/themes/AdminLTE/pages/UI/general.html)编写。

## [布局](#%E5%B8%83%E5%B1%80)

`laravel-admin`的布局使用bootstrap的栅格系统，每行的长度是12，下面是几个简单的布局示例：

添加一行内容:

```
$content->row('hello');

---------------------------------
|hello                          |
|                               |
|                               |
|                               |
|                               |
|                               |
---------------------------------
```

行内添加多列：

```
$content->row(function(Row $row) {
    $row->column(4, 'foo');
    $row->column(4, 'bar');
    $row->column(4, 'baz');
});
----------------------------------
|foo       |bar       |baz       |
|          |          |          |
|          |          |          |
|          |          |          |
|          |          |          |
|          |          |          |
----------------------------------

$content->row(function(Row $row) {
    $row->column(4, 'foo');
    $row->column(8, 'bar');
});
----------------------------------
|foo       |bar                  |
|          |                     |
|          |                     |
|          |                     |
|          |                     |
|          |                     |
----------------------------------
```

列中添加行：

```
$content->row(function (Row $row) {

    $row->column(4, 'xxx');

    $row->column(8, function (Column $column) {
        $column->row('111');
        $column->row('222');
        $column->row('333');
    });
});
----------------------------------
|xxx       |111                  |
|          |---------------------|
|          |222                  |
|          |---------------------|
|          |333                  |
|          |                     |
----------------------------------
```

列中添加行, 行内再添加列：

```
$content->row(function (Row $row) {

    $row->column(4, 'xxx');

    $row->column(8, function (Column $column) {
        $column->row('111');
        $column->row('222');
        $column->row(function(Row $row) {
            $row->column(6, '444');
            $row->column(6, '555');
        });
    });
});
----------------------------------
|xxx       |111                  |
|          |---------------------|
|          |222                  |
|          |---------------------|
|          |444      |555        |
|          |         |           |
----------------------------------
```
