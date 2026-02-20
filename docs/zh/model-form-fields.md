# 表单组件

在`model-form`中内置了大量的form组件来帮助你快速的构建form表单

## [基础方法](#%E5%9F%BA%E7%A1%80%E6%96%B9%E6%B3%95)

#### 设置保存值

```
$form->text('title')->value('text...');
```

#### 设置默认值

```
$form->text('title')->default('text...');
```

#### 设置help信息

```
$form->text('title')->help('help...');
```

#### 设置属性

```
$form->text('title')->attribute(['data-title' => 'title...']);

$form->text('title')->attribute('data-title', 'title...');
```

#### 设置placeholder

```
$form->text('title')->placeholder('请输入。。。');
```

#### 设置必填

```
$form->text('title')->required();
```

#### 设置pattern

```
$form->text('title')->pattern('[A-z]{3}');
```

#### 设置readonly

```
$form->text('title')->readonly();
```

#### 设置disable

```
$form->text('title')->disable();
```

#### 设置autofocus

```
$form->text('title')->autofocus();
```

#### 标签页表单

如果表单元素太多,会导致form页面太长, 这种情况下可以使用tab来分隔form:

```
$form->tab('Basic info', function ($form) {

    $form->text('username');
    $form->email('email');

})->tab('Profile', function ($form) {

   $form->image('avatar');
   $form->text('address');
   $form->mobile('phone');

})->tab('Jobs', function ($form) {
     $form->hasMany('jobs', function ($form) {
         $form->text('company');
         $form->date('start_date');
         $form->date('end_date');
     });
  });
```

#### 设置表单项组合

用来将表单项分组显示

```
$form->fieldset('用户信息', function (Form $form) {
    $form->text('username');
    $form->email('email');
});
```

## [文本输入](#%E6%96%87%E6%9C%AC%E8%BE%93%E5%85%A5)

![text](https://user-images.githubusercontent.com/1479100/82288328-d3938b80-99d4-11ea-91ec-4915d48d6057.png)

```
$form->text($column, [$label]);

// 添加提交验证规则
$form->text($column, [$label])->rules('required|min:10');

// 设置FontAwesome图标
$form->text($column, [$label])->icon('fa-pencil');

// 设置datalist
$form->text($column, [$label])->datalist(['key' => 'value']);

// 设置inputmask, see https://github.com/RobinHerbots/Inputmask
$form->text('code')->inputmask(['mask' => '99-9999999']);
```

## [Textarea 输入](#Textarea%20%E8%BE%93%E5%85%A5)

![textarea](https://user-images.githubusercontent.com/1479100/82288329-d3938b80-99d4-11ea-9066-41e163824995.png)

```
$form->textarea($column[, $label])->rows(10);
```

## [Radio选择](#Radio%E9%80%89%E6%8B%A9)

![radio](https://user-images.githubusercontent.com/1479100/82288325-d1c9c800-99d4-11ea-8403-90b0b73526bf.png)

```
$form->radio($column[, $label])->options(['m' => 'Female', 'f'=> 'Male'])->default('m');

// 竖排
$form->radio($column[, $label])->options(['m' => 'Female', 'f'=> 'Male'])->stacked();
```

`Radio`组件有两个派生组件`RadioButton`和`RadioCard`, 以单选按钮和单选卡片的形式显示，使用方式和`Radio`组件完全一致：

```
$form->radioButton($column[, $label])->options(['m' => 'Female', 'f'=> 'Male'])->default('m');

$form->radioCard($column[, $label])->options(['m' => 'Female', 'f'=> 'Male'])->default('m');
```

## [Checkbox选择](#Checkbox%E9%80%89%E6%8B%A9)

![checkbox](https://user-images.githubusercontent.com/1479100/82288312-cd051400-99d4-11ea-86cb-dc1f36c1f1a5.png)

`checkbox`能处理两种数据存储情况，参考[多选](#%E5%A4%9A%E9%80%89)

`options()`方法用来设置选择项:

```
$form->checkbox($column[, $label])->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name']);

// 竖排
$form->checkbox($column[, $label])->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name'])->stacked();

// 通过闭包设置options
$form->checkbox($column[, $label])->options(function () {
    return [1 => 'foo', 2 => 'bar', 'val' => 'Option name'];
});

// 如果选项太多的话，可以在上面增加一个全选checkbox
$form->checkbox($column[, $label])->options([])->canCheckAll();
```

`Checkbox`组件有两个派生组件`CheckboxButton`和`CheckboxCard`, 以多选按钮和多选卡片的形式显示，使用方式和`Checkbox`组件完全一致：

```
$form->checkboxButton($column[, $label])->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name']);

$form->checkboxCard($column[, $label])->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name']);
```

## [Select单选](#Select%E5%8D%95%E9%80%89)

![select](https://user-images.githubusercontent.com/1479100/82288327-d2faf500-99d4-11ea-9b34-68386b1ebaf6.png)

```
$form->select($column[, $label])->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name']);
```

或者从api中获取选项列表：

```
$form->select($column[, $label])->options('/api/users');
```

其中api接口的格式必须为下面格式：

```
[
    {
        "id": 9,
        "text": "xxx"
    },
    {
        "id": 21,
        "text": "xxx"
    },
    ...
]
```

如果选项过多，可通过ajax方式动态分页载入选项：

```
$form->select('user_id')->options(function ($id) {
    $user = User::find($id);

    if ($user) {
        return [$user->id => $user->name];
    }
})->ajax('/admin/api/users');
```

API `/admin/api/users`接口的代码：

```
public function users(Request $request)
{
    $q = $request->get('q');

    return User::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
}
```

接口返回的数据结构为

```
{
    "total": 4,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1,
    "next_page_url": null,
    "prev_page_url": null,
    "from": 1,
    "to": 3,
    "data": [
        {
            "id": 9,
            "text": "xxx"
        },
        {
            "id": 21,
            "text": "xxx"
        },
        {
            "id": 42,
            "text": "xxx"
        },
        {
            "id": 48,
            "text": "xxx"
        }
    ]
}
```

### [Select 联动](#Select%20%E8%81%94%E5%8A%A8)

`select`组件支持父子关系的单向联动：

```
$form->select('province')->options(...)->load('city', '/api/city');

$form->select('city');
```

其中`load('city', '/api/city');`的意思是，在当前select的选项切换之后，会把当前选项的值通过参数`q`, 调用接口`/api/city`，并把api返回的数据填充为city选择的选项，其中api`/api/city`返回的数据格式必须符合:

```
[
    {
        "id": 9,
        "text": "xxx"
    },
    {
        "id": 21,
        "text": "xxx"
    },
    ...
]
```

控制器action的代码示例如下：

```
public function city(Request $request)
{
    $provinceId = $request->get('q');

    return ChinaArea::city()->where('parent_id', $provinceId)->get(['id', DB::raw('name as text')]);
}
```

## [Select多选](#Select%E5%A4%9A%E9%80%89)

![mselect](https://user-images.githubusercontent.com/1479100/82288323-d1313180-99d4-11ea-83f3-16c192e30ec2.png)

```
$form->multipleSelect($column[, $label])->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name']);
```

多选可以处理两种情况，第一种是`ManyToMany`的关系。

```
class Post extends Models
{
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

$form->multipleSelect('tags')->options(Tag::all()->pluck('name', 'id'));
```

第二种是将选项数组存储到单字段中，如果字段是字符串类型，那就需要在模型里面为该字段定义[访问器和修改器](https://laravel.com/docs/5.5/eloquent-mutators)来存储和读取了。

比如字段`tags`以字符串的形式存储，并且以逗号`,`分隔，那么像下面一样定义它的访问器和修改器：

```
class Post extends Model
{
    public function getTagsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = implode(',', $value);
    }
}
```

如果选项过多，可通过ajax方式动态分页载入选项：

```
$form->multipleSelect('friends')->options(function ($ids) {
    return User::find($ids)->pluck('name', 'id');
})->ajax('/admin/api/users');
```

API `/admin/api/users`接口的代码：

```
public function users(Request $request)
{
    $q = $request->get('q');

    return User::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
}
```

接口返回的数据结构为

```
{
    "total": 4,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1,
    "next_page_url": null,
    "prev_page_url": null,
    "from": 1,
    "to": 3,
    "data": [
        {
            "id": 9,
            "text": "xxx"
        },
        {
            "id": 21,
            "text": "xxx"
        },
        {
            "id": 42,
            "text": "xxx"
        },
        {
            "id": 48,
            "text": "xxx"
        }
    ]
}
```

## [穿梭多选](#%E7%A9%BF%E6%A2%AD%E5%A4%9A%E9%80%89)

![QQ20200519-130525](https://user-images.githubusercontent.com/1479100/82287107-f8d2ca80-99d1-11ea-90a2-8efa9c5ff224.png)

使用方法和`multipleSelect`类似

```
$form->listbox($column[, $label])->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name']);

// 设置高度
$form->listbox($column[, $label])->height(200);
```

## [邮箱输入](#%E9%82%AE%E7%AE%B1%E8%BE%93%E5%85%A5)

```
$form->email($column[, $label]);
```

## [密码输入](#%E5%AF%86%E7%A0%81%E8%BE%93%E5%85%A5)

![password](https://user-images.githubusercontent.com/1479100/82288324-d1c9c800-99d4-11ea-9085-c510ab007a20.png)

```
$form->password($column[, $label]);
```

## [URL 输入](#URL%20%E8%BE%93%E5%85%A5)

![URL](https://user-images.githubusercontent.com/1479100/82288331-d42c2200-99d4-11ea-9e68-39eb6341afdc.png)

```
$form->url($column[, $label]);
```

## [IP 输入](#IP%20%E8%BE%93%E5%85%A5)

![ip](https://user-images.githubusercontent.com/1479100/82288319-d0000480-99d4-11ea-96d8-92d8c7e7f1fe.png)

```
$form->ip($column[, $label]);
```

## [电话号码输入](#%E7%94%B5%E8%AF%9D%E5%8F%B7%E7%A0%81%E8%BE%93%E5%85%A5)

![mobile](https://user-images.githubusercontent.com/1479100/82288321-d0989b00-99d4-11ea-847a-818ce7d419db.png)

```
$form->mobile($column[, $label]);

// 自定义格式
$form->mobile($column[, $label])->options(['mask' => '999 9999 9999']);
```

## [颜色选择](#%E9%A2%9C%E8%89%B2%E9%80%89%E6%8B%A9)

![color](https://user-images.githubusercontent.com/1479100/82288314-ce364100-99d4-11ea-9fd6-f31991f013ad.png)

```
$form->color($column[, $label])->default('#ccc');
```

## [时间输入](#%E6%97%B6%E9%97%B4%E8%BE%93%E5%85%A5)

```
$form->time($column[, $label]);

// 设置时间格式，更多格式参考http://momentjs.com/docs/#/displaying/format/
$form->time($column[, $label])->format('HH:mm:ss');
```

## [日期输入](#%E6%97%A5%E6%9C%9F%E8%BE%93%E5%85%A5)

![datetime](https://user-images.githubusercontent.com/1479100/82288315-ceced780-99d4-11ea-98cc-cb332016137d.png)

```
$form->date($column[, $label]);

// 设置日期格式，更多格式参考http://momentjs.com/docs/#/displaying/format/
$form->date($column[, $label])->format('YYYY-MM-DD');
```

## [日期时间输入](#%E6%97%A5%E6%9C%9F%E6%97%B6%E9%97%B4%E8%BE%93%E5%85%A5)

```
$form->datetime($column[, $label]);

// 设置日期格式，更多格式参考http://momentjs.com/docs/#/displaying/format/
$form->datetime($column[, $label])->format('YYYY-MM-DD HH:mm:ss');
```

## [时间范围选择](#%E6%97%B6%E9%97%B4%E8%8C%83%E5%9B%B4%E9%80%89%E6%8B%A9)

`$startTime`、`$endTime`为开始和结束时间字段:

```
$form->timeRange($startTime, $endTime, 'Time Range');
```

## [日期范围选](#%E6%97%A5%E6%9C%9F%E8%8C%83%E5%9B%B4%E9%80%89)

`$startDate`、`$endDate`为开始和结束日期字段:

```
$form->dateRange($startDate, $endDate, 'Date Range');
```

## [时间日期范围选择](#%E6%97%B6%E9%97%B4%E6%97%A5%E6%9C%9F%E8%8C%83%E5%9B%B4%E9%80%89%E6%8B%A9)

`$startDateTime`、`$endDateTime`为开始和结束时间日期:

```
$form->datetimeRange($startDateTime, $endDateTime, 'DateTime Range');
```

## [货币输入](#%E8%B4%A7%E5%B8%81%E8%BE%93%E5%85%A5)

```
$form->currency($column[, $label]);

// 设置单位符号
$form->currency($column[, $label])->symbol('￥');
```

## [数字输入](#%E6%95%B0%E5%AD%97%E8%BE%93%E5%85%A5)

```
$form->number($column[, $label]);

// 设置最大值
$form->number($column[, $label])->max(100);

// 设置最小值
$form->number($column[, $label])->min(10);
```

## [比例输入](#%E6%AF%94%E4%BE%8B%E8%BE%93%E5%85%A5)

```
$form->rate($column[, $label]);
```

## [滑动选择](#%E6%BB%91%E5%8A%A8%E9%80%89%E6%8B%A9)

可以用来数字类型字段的选择，比如年龄：

```
$form->slider($column[, $label])->options([
    'max'       => 100,
    'min'       => 1,
    'step'      => 1,
    'postfix'   => 'years old'
]);
```

更多`options`请参考:<https://github.com/IonDen/ion.rangeSlider#settings>

## [富文本编辑](#%E5%AF%8C%E6%96%87%E6%9C%AC%E7%BC%96%E8%BE%91)

富文本编辑组件在v1.7.0版本之后移除，请选择使用下面的富文本编辑器扩展：

| 扩展 | URL |
| --- | --- |
| wangEditor | <https://github.com/laravel-admin-extensions/wangEditor> |
| wangEditor2 | <https://github.com/laravel-admin-extensions/wangEditor2> |
| UEditor | <https://github.com/laravel-admin-extensions/UEditor> |
| Summernote | <https://github.com/laravel-admin-extensions/summernote> |
| Quill | <https://github.com/laravel-admin-extensions/quill> |
| CKEditor | <https://github.com/laravel-admin-extensions/ckeditor> |
| Simditor | <https://github.com/laravel-admin-extensions/simditor> |

## [隐藏域](#%E9%9A%90%E8%97%8F%E5%9F%9F)

```
$form->hidden($column);
```

## [开关](#%E5%BC%80%E5%85%B3)

`on`和`off`对用开关的两个值`1`和`0`:

```
$states = [
    'on'  => ['value' => 1, 'text' => '打开', 'color' => 'success'],
    'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
];

$form->switch($column[, $label])->states($states);
```

## [经纬度选择](#%E7%BB%8F%E7%BA%AC%E5%BA%A6%E9%80%89%E6%8B%A9)

地图组件在v1.7.0版本之后移除，请使用[经纬度选择器插件](https://github.com/laravel-admin-extensions/latlong)代替

## [纯显示](#%E7%BA%AF%E6%98%BE%E7%A4%BA)

只显示字段，不做任何操作：

```
$form->display($column[, $label]);

//更复杂的显示
$form->display($column[, $label])->with(function ($value) {
    return "<img src="$value" />";
});
```

## [分割线](#%E5%88%86%E5%89%B2%E7%BA%BF)

```
$form->divider();

// OR

$form->divider('Title');
```

## [HTML显示](#HTML%E6%98%BE%E7%A4%BA)

插入html内容，参数可以是实现了`Htmlable`、`Renderable`或者实现了`__toString()`方法的类

```
$form->html('你的html内容'[, $label]);
```

## [标签输入](#%E6%A0%87%E7%AD%BE%E8%BE%93%E5%85%A5)

插入逗号(,)隔开的字符串`tags`

```
$form->tags('keywords'[, $label]);
```

## [图标选择](#%E5%9B%BE%E6%A0%87%E9%80%89%E6%8B%A9)

![icon](https://user-images.githubusercontent.com/1479100/82288317-cf676e00-99d4-11ea-92c5-a393bd4dfb64.png)

选择`font-awesome`图标

```
$form->icon('icon'[, $label]);
```

## [时区选择](#%E6%97%B6%E5%8C%BA%E9%80%89%E6%8B%A9)

```
$form->timezone('timezone'[, $label]);
```
