# 关联关系处理

模型表单支持`一对一`、`一对多`、`多对多`等多种模型关联的数据处理

## [一对一](#%E4%B8%80%E5%AF%B9%E4%B8%80)

`users`表和`profiles`表通过`profiles.user_id`字段一对一关联

```
users
    id      - integer
    name    - string
    email   - string

profiles
    id      - integer
    user_id - integer
    age     - integer
    gender  - string
```

对应的数据模型为:

```
class User extends Model
{
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}

class Profile extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### [HasOne 关联](#HasOne%20%E5%85%B3%E8%81%94)

下面的代码可以在`User`模型的表单中直接编辑关联模型`profiles`中的`age`、`gender`字段

```
$form = new Form(new User);

$form->text('name');
$form->text('email');

// 关联模型Profile的字段
$form->text('profile.age');
$form->text('profile.gender');
```

### [BelongsTo 关联](#BelongsTo%20%E5%85%B3%E8%81%94)

同样反向关系也可以直接在`Profile`模型的表单中编辑归属模型`User`中的数据

```
$form = new Form(new Profile);

$form->text('age');
$form->text('gender');

// 关联模型Profile的字段
$form->text('user.name');
$form->text('user.email');
```

### [MorphOne 关联](#MorphOne%20%E5%85%B3%E8%81%94)

`MorphOne`关联的使用方法和上述`HasOne`、`BelongsTo`关联一致。

## [一对多](#%E4%B8%80%E5%AF%B9%E5%A4%9A)

### [HasMany](#HasMany)

`painters`和`paintings`有两张通过`painter_id`建立一对多关联：

```
painters
  id            - integer
  username      - string
  bio           - text

paintings
  id            - integer
  painter_id    - integer
  title         - string
  body          - text
  completed_at  - timestamp
```

表的模型为：

```
class Painter extends Model
{
    public function paintings()
    {
        return $this->hasMany(Painting::class, 'painter_id');
    }
}

class Painting extends Model
{
    public function painter()
    {
        return $this->belongsTo(Painter::class, 'painter_id');
    }
}
```

通过下面的表单构建代码来实现主表和字段字段的编辑：

```
// 主表字段
$form->text('username')->rules('required');
$form->textarea('bio')->rules('required');

// 子表字段
$form->hasMany('paintings', function (Form\NestedForm $form) {
    $form->text('title');
    $form->image('body');
    $form->datetime('completed_at');
});

// 可以使用第二个参数设置label
$form->hasMany('paintings', '画作', function (Form\NestedForm $form) {

});
```

> 注意：目前HasMany表单对复杂的表单组件(富文本等)支持不够友好。

### [MorphMany](#MorphMany)

多态一对多关联和HasMany关联的使用方法是一至的

`posts`和`comments`有两张通过`painter_id`建立多态一对多关联：

```
posts
  id                - integer
  content           - text

comments
  id                - integer
  body              - integer
  commentable_id    - integer
  commentable_type  - string
```

表的模型为：

```
class Post extends Model
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

class Comment extends Model
{
    public function commentable()
    {
        return $this->morphTo();
    }
}
```

通过下面的表单构建代码来实现主表和字段字段的编辑：

```
// 主表字段
$form->text('username')->rules('required');
$form->textarea('bio')->rules('required');

// 子表字段
$form->morphMany('paintings', function (Form\NestedForm $form) {
    $form->text('title');
    $form->image('body');
    $form->datetime('completed_at');
});

// 可以使用第二个参数设置label
$form->morphMany('paintings', '画作', function (Form\NestedForm $form) {

});
```

> 实际上morphMany方法是hasMany方法的别名，因关联名称的不同而做的区分，两者使用方法一致。

## [多对多](#%E5%A4%9A%E5%AF%B9%E5%A4%9A)

### [BelongsToMany](#BelongsToMany)

`users`表和`roles`表通过中间表`role_user`多对多关联，一个用户可以有多个角色，一个角色也可以属于多个用户

```
users
    id      - integer
    name    - string

roles
    id      - integer
    name    - integer

role_user
    user_id - integer
    role_id - integer
```

对应的数据模型为:

```
class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}

class Role extends Model
{
    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
```

那么有`multipleSelect`和`checkbox`两个组件支持`多对多关联关系`的选择

比如在用户的表单中选择角色：

```
$form = new Form(new User);

$form->multipleSelect('roles', '角色')->options(Role::all()->pluck('name', 'id'));
```

或者在角色的表单中选择用户：

```
$form = new Form(new Role);

$form->multipleSelect('users', '用户')->options(User::all()->pluck('name', 'id'));
```

`checkbox`也是一样的用法：

```
$form = new Form(new User);

$form->checkbox('roles', '角色')->options(Role::all()->pluck('name', 'id'));
```

## [归属选择](#%E5%BD%92%E5%B1%9E%E9%80%89%E6%8B%A9)

归属关系选择是`v1.8.0`新增的功能，也是比较常用的场景。

以下面的文章表和用户表为例，每一篇文章都归属于一个用户（作者）

```
articles
    id          - integer 
    author_id   - integer 
    title       - string
    body        - text

users
    id          - integer 
    name        - string
    email       - string
    avatar      - string
```

模型：

```
class Article extends Model
{
    public function author()
    {
        return $this->belongsTo(User::class);
    }
}

class User extends Model
{
}
```

如果要在文章的表单中选择作者，可以使用`select`组件

```
$form->select('author_id')->options(User::all()->pluck('name', 'id'));
```

`select`组件有两个比较明显的问题，第一、如果用户数量太多，select的选项会过长，第二，没办法在select的选项中展示更多的用户信息，比如`email`、`avatar`这两个字段。

下面的`BelongsTo`和`BelongsToMany`两个方法很好的解决了这个问题，使用`弹出列表`的形式来选择归属对象。

### [BelongsTo](#BelongsTo)

同样使用上面例子中的表结构和模型，使用方法如下

先定义列表选择类：

```
<?php

namespace App\Admin\Selectable;

use App\Models\User;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Grid\Selectable;

class Users extends Selectable
{
    public $model = User::class;

    public function make()
    {
        $this->column('id');
        $this->column('name');
        $this->column('email');
        $this->column('avatar', '头像')->image();
        $this->column('created_at');

        $this->filter(function (Filter $filter) {
            $filter->like('name');
        });
    }
}
```

列表选择类中，`$model`属性用来指定列表的模型，列表默认数据为`10`条，可以使用属性 `protected $perPage = 5;`来设置每页数量。

`make`方法用来构建列表，使用方法参考 [model-grid 文档](model-grid.md)

下面是在表单中使用它：

```
use App\Admin\Selectable\Users;

$form->belongsTo('author_id', Users::class, '作者');
```

使用效果如下：

![Kapture 2020-05-21 at 18 08 28](https://user-images.githubusercontent.com/1479100/82548684-43994180-9b8e-11ea-949e-23ff0323347c.gif)

### [BelongsToMany](#BelongsToMany)

用`belongsToMany`方法来替代`multipleSelect`，进行多对多关系的选择

以下面的文章表和用户表为例，每一遍文章都归属于多个用户（协作者）

```
articles
    id          - integer 
    title       - string
    body        - text

users
    id          - integer 
    name        - string
    email       - string
    avatar      - string

article_user
    user_id    - integer 
    article_id - integer
```

模型：

```
class Article extends Model
{
    public function collaborators()
    {
        return $this->belongsToMany(User::class);
    }
}

class User extends Model
{
}
```

下面是在表单中使用它：

```
// 使用上例中定义的列表选择类
use App\Admin\Selectable\Users;

$form->belongsToMany('collaborators', Users::class, __('协作者'));
```

使用效果如下：

![belongstomany](https://user-images.githubusercontent.com/1479100/82548917-b3a7c780-9b8e-11ea-8025-4cbfde209dd8.gif)

### [在列表页使用](#%E5%9C%A8%E5%88%97%E8%A1%A8%E9%A1%B5%E4%BD%BF%E7%94%A8)

只要在表单中使用了归属选择器，也同时可以在列表页中使用，实现行内编辑

```
// 使用上例中定义的列表选择类
use App\Admin\Selectable\Users;

$grid->column('author_id', 'Author')
    ->belongsTo(Users::class);

$grid->column('collaborators', 'Collaborators')
    ->belongsToMany(Users::class);
```

注意，在列表中使用时，**必须**在列表选择类`App\Admin\Selectable\Users`中增加一个`display`方法，以定义这两列在列表中的显示：

```
class Users extends Selectable
{
    ...

    public static function display()
    {
        return function ($value) {

            // 如果`$value`是数组，表示在`collaborators`列中使用，显示用分号`;`分隔的用户`name`字段
            if (is_array($value)) {
                return implode(';', array_column($value, 'name'));
            }

            // 否则为`author_id`列使用，直接显示用户的`name`字段
            return optional($this->author)->name;
        };
    }
}
```

使用效果如下：

![belongsto-inline](https://user-images.githubusercontent.com/1479100/82550220-c28f7980-9b90-11ea-88b3-f64108956c1e.gif)

## [不支持的关联](#%E4%B8%8D%E6%94%AF%E6%8C%81%E7%9A%84%E5%85%B3%E8%81%94)

以下几种关联不支持表单编辑

`HasOneThrough`、`HasManyThrough`、`MorphTo`、`MorphToMany`
