# 行内编辑

数据表格有一系列方法，来帮助你列表里面直接对数据进行编辑。

> 注意：每一个列编辑的设定，需要在form里面有一个相应的field

## [editable](#editable)

用`editable`方法，可以让你在表格中点击数据，在弹出的对话框里面编辑保存数据，使用方法如下

### [text输入](#text%E8%BE%93%E5%85%A5)

```
$grid->column('title')->editable();
```

### [textarea输入](#textarea%E8%BE%93%E5%85%A5)

```
$grid->column('title')->editable('textarea');
```

### [select选择](#select%E9%80%89%E6%8B%A9)

第二个参数是select选择的选项

```
$grid->column('title')->editable('select', [1 => 'option1', 2 => 'option2', 3 => 'option3']);
```

### [日期选择](#%E6%97%A5%E6%9C%9F%E9%80%89%E6%8B%A9)

```
$grid->column('birth')->editable('date');
```

### [日期时间选择](#%E6%97%A5%E6%9C%9F%E6%97%B6%E9%97%B4%E9%80%89%E6%8B%A9)

```
$grid->column('published_at')->editable('datetime');
```

### [年份选择](#%E5%B9%B4%E4%BB%BD%E9%80%89%E6%8B%A9)

```
$grid->column('year')->editable('year');
```

### [月份选择](#%E6%9C%88%E4%BB%BD%E9%80%89%E6%8B%A9)

```
$grid->column('month')->editable('month');
```

### [日选择](#%E6%97%A5%E9%80%89%E6%8B%A9)

```
$grid->column('day')->editable('day');
```

## [switch开关](#switch%E5%BC%80%E5%85%B3)

> 注意：在`grid`中对某字段设置了`switch`，同时需要在`form`里面对该字段设置同样的`switch`

快速将列变成开关组件，使用方法如下：

```
$grid->column('status')->switch();

// 设置text、color、和存储值
$states = [
    'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
    'off' => ['value' => 2, 'text' => '关闭', 'color' => 'default'],
];
$grid->column('status')->switch($states);
```

## [switchGroup 开关组](#switchGroup%20%E5%BC%80%E5%85%B3%E7%BB%84)

> 注意：在`grid`中对某些字段设置了`switch`，同时需要在`form`里面对这些字段设置同样的`switch`

快速将列变成开关组件组，使用方法如下：

```
$states = [
    'on' => ['text' => 'YES'],
    'off' => ['text' => 'NO'],
];

$grid->column('switch_group')->switchGroup([
    'hot'       => '热门',
    'new'       => '最新'
    'recommend' => '推荐',
], $states);
```

## [radio](#radio)

将该列设置为`radio`组件,同时需要在`form`方法里面对这些字段设置同样的`radio`

```
$grid->column('options')->radio([
    1 => 'Sed ut perspiciatis unde omni',
    2 => 'voluptatem accusantium doloremque',
    3 => 'dicta sunt explicabo',
    4 => 'laudantium, totam rem aperiam',
]);
```

## [checkbox](#checkbox)

将该列设置为`checkbox`组件,同时需要在`form`方法里面对这些字段设置同样的`checkbox`

```
$grid->column('options')->checkbox([
    1 => 'Sed ut perspiciatis unde omni',
    2 => 'voluptatem accusantium doloremque',
    3 => 'dicta sunt explicabo',
    4 => 'laudantium, totam rem aperiam',
]);
```
