# 列的显示

默认情况下，列显示数据库里面最原始的数据，如果需要修改列数据的显示，参考下面的方式

## [display回调](#display%E5%9B%9E%E8%B0%83)

`display()`方法来通过传入的回调函数来处理当前列的值：

```
$grid->column('title')->display(function ($title) {

    return "<span style='color:blue'>$title</span>";

});
```

在传入的匿名函数中可以通过任何方式对数据进行处理，另外匿名函数绑定了当前列的数据作为父对象，可以在函数中调用当前行的数据：

```
$grid->column('first_name');

$grid->column('last_name');

// 不存在的`full_name`字段
$grid->column('full_name')->display(function () {
    return $this->first_name . ' ' . $this->last_name;
});
```

> 注意：要尽量避免在回调函数中去查询数据库或者调用外部接口，这样会在每一行渲染的时候运行查询或者外部接口的调用，严重影响性能，一个好的办法是给模型建立关联关系，然就用模型的`with`方法将关联数据一并查询出来，

## [collection回调](#collection%E5%9B%9E%E8%B0%83)

这个方法和`display`回调不同的是，它可以批量修改数据, 参考下面实例中的几个使用场景：

```
use Illuminate\Database\Eloquent\Collection;

$grid->model()->collection(function (Collection $collection) {

    // 1. 可以给每一列加字段，类似上面display回调的作用
    foreach($collection as $item) {
        $item->full_name = $item->first_name . ' ' . $item->last_name;
    }

    // 2. 给表格加一个序号列
    foreach($collection as $index => $item) {
        $item->number = $index;
    }

    // 3. 从外部接口获取数据填充到模型集合中
    $ids = $collection->pluck('id');
    $data = getDataFromApi($ids);
    foreach($collection as $index => $item) {
        $item->column_name = $data[$index];
    }

    // 最后一定要返回集合对象
    return $collection;
});
```

`$collection`表示当前这一个表格数据的模型集合， 你可以根据你的需要来读取或者修改它的数据。

## [根据条件显示不同的组件](#%E6%A0%B9%E6%8D%AE%E6%9D%A1%E4%BB%B6%E6%98%BE%E7%A4%BA%E4%B8%8D%E5%90%8C%E7%9A%84%E7%BB%84%E4%BB%B6)

如果这一列要根据某些条件来显示为不同的组件

```
$grid->column('title')->display(function ($title, $column) {

    // 如果这一列的status字段的值等于1，直接显示title字段
    if ($this->status == 1) {
        return $title;
    }

    // 否则显示为editable
    return $column->editable();
});
```

## [内容映射](#%E5%86%85%E5%AE%B9%E6%98%A0%E5%B0%84)

> since v1.5.17

如果字段`gender`的取值为`f`、`m`，分别需要用`女`、`男`来显示

```
$grid->column('gender')->using(['f' => '女', 'm' => '男']);
```

## [内容替换](#%E5%86%85%E5%AE%B9%E6%9B%BF%E6%8D%A2)

如果需要将这一列的某些值替换为其它的内容来显示：

```
$grid->column('cost')->replace([0 => '-']);
```

## [列视图](#%E5%88%97%E8%A7%86%E5%9B%BE)

使用`view()`方法让当前列渲染一个视图显示输出, 比如有一个视图`resources/views/content.blade.php`

```
<p>

{{ $value }}

{{ $model->id }}

</p>
```

默认会传入视图的两个变量，`$model`为当前行的模型，`$value`为当前列的值

然后用下面的调用来渲染这个视图输出

```
$grid->column('content')->view('content');
```

用这个方法可以很好的渲染出复杂的列内容。

## [列展开](#%E5%88%97%E5%B1%95%E5%BC%80)

> Since v1.6.10

如果一行的字段比较多，可以通过列展开功能，来隐藏过多的内容。通过点击列来展开显示，或者点击展开相关的其它数据，比如下面的例子，用来展开一条文章下的10条最新评论：

```
use Encore\Admin\Widgets\Table;

$grid->column('title', '标题')->expand(function ($model) {

    $comments = $model->comments()->take(10)->get()->map(function ($comment) {
        return $comment->only(['id', 'content', 'created_at']);
    });

    return new Table(['ID', '内容', '发布时间'], $comments->toArray());
});
```

显示效果如下：

![Kapture 2019-03-10 at 23 46 20](https://user-images.githubusercontent.com/1479100/54087610-7abb7280-438f-11e9-842a-012ac2063a7a.gif)

闭包函数中可以返回任何可被渲染的内容。

### [异步加载](#%E5%BC%82%E6%AD%A5%E5%8A%A0%E8%BD%BD)

> Since v.18.0

先定义可渲染类

```
use Illuminate\Contracts\Support\Renderable;

class ShowUser implements Renderable
{
    public function render($key = null)
    {
        dump(User::find($key)->toArray());
    }
}
```

在`render`方法中，可以输出或者返回任何内容，将会显示在列表的`modal`弹出框中：

```
$grid->expand('user', '标题')->modal(ShowUser::class);
```

## [弹出模态框](#%E5%BC%B9%E5%87%BA%E6%A8%A1%E6%80%81%E6%A1%86)

> Since v1.6.10

和`列展开`功能类似，可以通过弹出模态框来显示更多内容

```
$grid->column('title', '标题')->modal('最新评论', function ($model) {

    $comments = $model->comments()->take(10)->get()->map(function ($comment) {
        return $comment->only(['id', 'content', 'created_at']);
    });

    return new Table(['ID', '内容', '发布时间'], $comments->toArray());
});
```

显示效果如下：

![Kapture 2019-03-10 at 23 43 49](https://user-images.githubusercontent.com/1479100/54087609-7abb7280-438f-11e9-8a44-7e68bb126cd1.gif)

### [异步加载](#%E5%BC%82%E6%AD%A5%E5%8A%A0%E8%BD%BD)

> Since v.18.0

先定义可渲染类

```
use Illuminate\Contracts\Support\Renderable;

class ShowUser implements Renderable
{
    public function render($key = null)
    {
        dump(User::find($key)->toArray());
    }
}
```

在`render`方法中，可以输出或者返回任何内容，将会显示在列表的`modal`弹出框中：

```
$grid->column('user', '标题')->modal(ShowUser::class);

// 或者
$grid->column('user', '标题')->modal('标题。。', ShowUser::class);
```

## [Gavatar头像](#Gavatar%E5%A4%B4%E5%83%8F)

如果这一列数据是邮箱，你想要显示为Gavatar头像：

```
$grid->column('email', '头像')->gravatar();

// 设置尺寸
$grid->column('email', '头像')->gravatar(45);
```

## [文件尺寸](#%E6%96%87%E4%BB%B6%E5%B0%BA%E5%AF%B8)

如果这一列的数据是表示文件大小的字节数，可以通过调用`filezise`方法来显示更有可读性的文字

```
$grid->column('file_size')->filesize();
```

这样数值`199812019`将会显示为`190.56 MB`

## [下载链接](#%E4%B8%8B%E8%BD%BD%E9%93%BE%E6%8E%A5)

如果这一列的数据存储的是上传文件的路径，那么可以通过调用`downloadable`方法来将这一列设置为可下载链接

```
$grid->column('file_path')->downloadable();
```

## [复制按钮](#%E5%A4%8D%E5%88%B6%E6%8C%89%E9%92%AE)

通过下面的调用，会在这一列的每一行文字前面出现一个复制icon，点击它可以复制它的值

```
$grid->column('title')->copyable();
```

## [二维码](#%E4%BA%8C%E7%BB%B4%E7%A0%81)

![WX20190830-002516](https://user-images.githubusercontent.com/1479100/63958324-b6243780-cabc-11e9-9572-827d04d6bd81.png)

通过下面的调用，会在这一列的每一行文字前面出现一个二维码icon，点击它可以展开一个小弹框，里面会显示这一列值的二维码编码图形

```
$grid->column('link')->qrcode();
```

## [显示图片](#%E6%98%BE%E7%A4%BA%E5%9B%BE%E7%89%87)

如果`picture`字段保存的是图片的完整地址，或者路径，可以通过下面的方式将该列渲染为图片显示

支持多图显示，需要字段输出为数组。

```
$grid->column('picture')->image();

//设置服务器和宽高
$grid->column('picture')->image('http://xxx.com', 100, 100);

// 显示多图
$grid->column('pictures')->display(function ($pictures) {

    return json_decode($pictures, true);

})->image('http://xxx.com', 100, 100);
```

## [显示label标签](#%E6%98%BE%E7%A4%BAlabel%E6%A0%87%E7%AD%BE)

![WX20190830-001812](https://user-images.githubusercontent.com/1479100/63958209-82491200-cabc-11e9-9144-760e69779608.png)

将字段显示为`label`标签, 如果字段输出为数组，会显示为多个`label`标签。

```
$grid->column('name')->label();

//设置颜色，默认`success`,可选`danger`、`warning`、`info`、`primary`、`default`、`success`
$grid->column('name')->label('danger');

// 接收数组
$grid->column('keywords')->label();
```

如果需要将`status`字段的不同的值显示为不同颜色的label

```
$grid->column('status')->label([
    1 => 'default',
    2 => 'warning',
    3 => 'success',
    4 => 'info',
]);
```

## [显示icon](#%E6%98%BE%E7%A4%BAicon)

将字段显示为`font-awesome`图标, 更多图标参考 <http://fontawesome.io/icons/>

```
$grid->column('status')->icon([
    0 => 'toggle-off',
    1 => 'toggle-on',
], $default = '');
```

## [链接](#%E9%93%BE%E6%8E%A5)

将字段显示为一个链接。

```
// link方法不传参数时，链接的`href`和`text`都是当前列的值
$grid->column('homepage')->link();

// 或者传入一个指定的href
$grid->column('homepage')->link($href);
```

## [表格](#%E8%A1%A8%E6%A0%BC)

将字段显示为一个表格，需要当前列的值为一个二维数组

```
// table方法不传参数时，表格的title为二维数组每一列的key
$grid->column('settings')->table();

// 可以通过下面的方法指定每一列的key
$grid->column('settings')->table(['key' => '键', 'val' => '值']);
```

## [进度条](#%E8%BF%9B%E5%BA%A6%E6%9D%A1)

![WX20190830-002033](https://user-images.githubusercontent.com/1479100/63958092-401fd080-cabc-11e9-9d51-f879e7708bcb.png)

将字段显示为一个进度条，需要当前列的值为一个数值，默认的最大值为100，

```
$grid->column('progress')->progressBar();

// 可选参数
$grid->column('progress')->progressBar($style = 'primary', $size = 'sm', $max = 100);
```

`$style`用来设置样式，可选值`danger`、`warning`、`info`、`primary`、`default`、`success`

`$size`用来设置尺寸, 可选值为`sm`、`xs`、`xxs`，`$max`用来设置最大范围。

## [loading状态](#loading%E7%8A%B6%E6%80%81)

```
$grid->column('status')->loading([1, 2, 3]);
```

如果status的值为`[1, 2, 3]`之一，会显示为一个loading加载icon。

显示其它的字段值显示情况

```
$grid->column('status')->loading([1, 2, 3], [
    4 => '完成'
]);
```

## [图片轮播](#%E5%9B%BE%E7%89%87%E8%BD%AE%E6%92%AD)

如果字段值为图片数组，可以用下面的调用显示为图片轮播组件

```
$grid->column('images')->carousel();

// 设置显示尺寸和图片服务器
$grid->column('images')->carousel($width = 300, int $height = 200, $server);
```

## [日期格式](#%E6%97%A5%E6%9C%9F%E6%A0%BC%E5%BC%8F)

> Since v1.7.3

如果字段值为时间戳，可以用`date`方法格式化输出

```
$grid->column('created_at')->date('Y-m-d');
```

其中格式参数请参考PHP的[date](http://php.net/manual/en/function.date.php)函数

## [布尔值显示](#%E5%B8%83%E5%B0%94%E5%80%BC%E6%98%BE%E7%A4%BA)

> Since v1.7.6

![WX20190830-002712](https://user-images.githubusercontent.com/1479100/63958756-5d08d380-cabd-11e9-9a00-1ad20173c28a.png)

将这一列转为bool值之后显示为`✓`和`✗`.

```
$grid->column('approved')->bool();
```

你也可以按照这一列的值指定显示，比如字段的值为`Y`和`N`表示`true`和`false`

```
$grid->column('approved')->bool(['Y' => true, 'N' => false]);
```

## [圆点前缀](#%E5%9C%86%E7%82%B9%E5%89%8D%E7%BC%80)

> Since v1.7.6

![WX20190830-001924](https://user-images.githubusercontent.com/1479100/63958090-3f873a00-cabc-11e9-9c9d-06de5e37ae54.png)

这个方法用来在列文字前面加上一个带颜色的圆点，以提供更直观清晰的视觉效果

```
$grid->column('status')->using([
    1 => '审核通过',
    2 => '草稿',
    3 => '发布',
    4 => '其它',
], '未知')->dot([
    1 => 'danger',
    2 => 'info',
    3 => 'primary',
    4 => 'success',
], 'warning');
```

按照上面的代码，使用`using`方法映射了显示文字，再使用`dot`设置圆点的颜色，颜色的取值有`danger`、`info`、`primary`、`success`、`warning`这五个选择。

## [列操作](#%E5%88%97%E6%93%8D%E4%BD%9C)

> Since v1.7.6

![WX20190830-001824](https://user-images.githubusercontent.com/1479100/63958089-3eeea380-cabc-11e9-9a3e-0f3a22ae9756.png)

> 注意：在使用这个方法之前，请先阅读[【自定义操作-行操作】](model-grid-custom-actions#%E8%A1%8C%E6%93%8D%E4%BD%9C)

这个功能可以将某一列显示为一个可操作的按钮，比如上图所示是一个`标星`和`取消标星`的列操作，点击这一列的星标图标之后, 后台会切换字段的状态，页面图标也跟着切换，具体实现代码如下：

```
<?php

namespace App\Admin\Actions\Document;

use App\Models\Document;
use Encore\Admin\Actions\RowAction;

class StarDocument extends RowAction
{
    // 在页面点击这一列的图表之后，发送请求到后端的handle方法执行
    public function handle(Document $document)
    {
        // 切换`star`字段的值并保存
        $document->star = (int) !$document->star;
        $document->save();

        // 保存之后返回新的html到前端显示
        $html = $document->star ? "<i class=\"fa fa-star-o\"></i>" : "<i class=\"fa fa-star\"></i>";

        return $this->response()->html($html);
    }

    // 这个方法来根据`star`字段的值来在这一列显示不同的图标
    public function display($star)
    {
        return $star ? "<i class=\"fa fa-star-o\"></i>" : "<i class=\"fa fa-star\"></i>";
    }
}
```

最后在列表中显示这一列：

```
use App\Admin\Actions\Document\StarDocument;

$grid->column('star')->action(StarDocument::class);
```
