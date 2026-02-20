# 数据模型详情

> v1.5.16版本及以上支持

从表格操作列的`眼睛`图表按钮或者`显示`点击进入数据的详情显示页面

以下面的`posts`表为例：

```
posts
    id          - integer
    author_id   - integer
    content     - text
    title       - string
    content     - text
    rate        - integer
    release_at  - timestamp
```

对应的数据模型为`App\Models\Post`，下面的代码可以显示`posts`表的数据的详情：

```
<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Encore\Admin\Show;

class PostController extends Controller
{
    protected function detail($id)
    {
        $show = new Show(Post::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('title', '标题');
        $show->field('content', '内容');
        $show->field('rate');
        $show->field('created_at');
        $show->field('updated_at');
        $show->field('release_at');

        return $show;
    }
}
```

> 如果你的控制器中没有`detail`方法, 参考上面的代码，加入这个方法

如果要直接显示所有的字段，可以用下面的简单方式：

```
$content->body(Admin::show(Post::findOrFail($id)));
```

如果要直接显示指定的字段：

```
$content->body(Admin::show(Post::findOrFail($id), ['id', 'title', 'content']));
```

或者指定每一个字段的label:

```
$content->body(Admin::show(Post::findOrFail($id), [
    'id'        => 'ID',
    'title'     => '标题',
    'content'   => '内容'
]));
```

## [基本使用方法](#%E5%9F%BA%E6%9C%AC%E4%BD%BF%E7%94%A8%E6%96%B9%E6%B3%95)

### [内容转义](#%E5%86%85%E5%AE%B9%E8%BD%AC%E4%B9%89)

为了防止XSS攻击, 默认输出的内容都会使用HTML转义，如果你不想转义输出HTML，可以调用`unescape`方法：

```
$show->avatar()->unescape()->as(function ($avatar) {

    return "<img src='{$avatar}' />";

});
```

### [修改面板的样式和标题](#%E4%BF%AE%E6%94%B9%E9%9D%A2%E6%9D%BF%E7%9A%84%E6%A0%B7%E5%BC%8F%E5%92%8C%E6%A0%87%E9%A2%98)

```
$show->panel()
    ->style('danger')
    ->title('post基本信息...');
```

`style`的取值可以是`primary`、`info`、`danger`、`warning`、`default`

### [面板工具设置](#%E9%9D%A2%E6%9D%BF%E5%B7%A5%E5%85%B7%E8%AE%BE%E7%BD%AE)

面板右上角默认有三个按钮`编辑`、`删除`、`列表`，可以分别用下面的方式关掉它们：

```
$show->panel()
    ->tools(function ($tools) {
        $tools->disableEdit();
        $tools->disableList();
        $tools->disableDelete();
    });;
```
