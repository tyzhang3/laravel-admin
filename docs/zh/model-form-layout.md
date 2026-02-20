# 表单布局

> Since v1.7.6

![image](https://user-images.githubusercontent.com/9959927/62914561-5a229900-bdc3-11e9-8404-a595c7f589aa.png)

类似于上图的左右两列布局方式，可以参考下面的代码来实现

```
$form = new Form(new Document);

// 第一列占据1/2的页面宽度
$form->column(1/2, function ($form) {

    // 在这一列中加入表单项

    $form->text('title', __('Title'))->rules('min:10');

    $form->textarea('desc', __('Desc'))->required();

    $form->select('uploader_id', __('Uploader'))
        ->options(User::all()->pluck('name', 'id'))
        ->rules('required');
    ;

    $form->file('path', __('Path'))->required();
});

// 第二列占据右边1/2的页面宽度
$form->column(1/2, function ($form) {
    $form->number('view_count', __('View count'))->default(0);

    $form->number('download_count', __('Download count'))->default(0);

    $form->number('rate', __('Rate'))->default(0);

    $form->radio('privilege', __('Privilege'))
        ->options(Document::$privileges)
        ->stacked()
        ->default(1);

    $form->datetimeRange('created_at', 'updated_at');
});

return $form;
```

> 表单布局使用bootstrap的栅格布局系统进行布局显示，列的宽度比例可以设置为`1/2`、`1/3`、`1/4`、`1/6`
