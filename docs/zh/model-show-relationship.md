# 关联关系

## [一对一关系](#%E4%B8%80%E5%AF%B9%E4%B8%80%E5%85%B3%E7%B3%BB)

`users`表和上面的`posts`表为一对一关联关系，通过`posts.author_id`字段关联，`users`表结构如下：

```
users
    id      - integer
    name    - string
    email   - string

posts
    id          - integer
    author_id   - integer
    content     - text
```

模型定义为：

```
class User extends Model
{
}

class Post extends Model
{
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
```

那么可以用下面的方式显示`post`所属的用户的详细：

```
$show->author('作者信息', function ($author) {

    $author->setResource('/admin/users');

    $author->id();
    $author->name();
    $author->email();
});
```

其中`$author`对象也是`Show`实例，同样可以使用上面的各种方法

> 注意：为了能够正常使用这个面板右上角的工具，必须用`setResource()`方法设置用户资源的url访问路径

## [一对多或多对多关系](#%E4%B8%80%E5%AF%B9%E5%A4%9A%E6%88%96%E5%A4%9A%E5%AF%B9%E5%A4%9A%E5%85%B3%E7%B3%BB)

一对多或多对多关系的关联数据会以`Model-grid`的方式呈现，下面是简单的例子

`posts`表和评论表`comments`为一对多关系(一条`post`有多条`comments`)，通过`comments.post_id`字段关联

```
comments
    id      - integer
    post_id - integer
    content - string
```

模型定义为：

```
class Post extends Model
{
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

class Comment extends Model
{
}
```

那么评论的显示通过下面的代码实现：

```
$show->comments('评论', function ($comments) {

    $comments->resource('/admin/comments');

    $comments->id();
    $comments->content()->limit(10);

    $comments->filter(function ($filter) {
        $filter->like('content');
    });
});
```

`$comments`是一个`Encore\Admin\Grid`实例，详细的使用方法可以参考[model-grid](model-grid.md)

> 注意：为了能够正常使用这个数据表格的功能，必须用`resource()`方法设置`comments`资源的url访问路径
