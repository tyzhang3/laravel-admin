# 表单验证

`model-form`使用`Laravel`的验证规则来验证表单提交的数据：

```
$form->text('title')->rules('required|min:3');

// 复杂的验证规则可以在回调里面实现
$form->text('title')->rules(function ($form) {
    // 如果不是编辑状态，则添加字段唯一验证
    if (!$id = $form->model()->id) {
        return 'unique:users,email_address';
    }
});
```

也可以给验证规则自定义错误提示消息：

```
$form->text('code')->rules('required|regex:/^\d+$/|min:10', [
    'regex' => 'code必须全部为数字',
    'min'   => 'code不能少于10个字符',
]);
```

如果要允许字段为空，首先要在数据库的表里面对该字段设置为`NULL`，然后

```
$form->text('title')->rules('nullable');
```

更多规则请参考[Validation](https://laravel.com/docs/5.5/validation).

## [创建页面规则](#%E5%88%9B%E5%BB%BA%E9%A1%B5%E9%9D%A2%E8%A7%84%E5%88%99)

只在创建表单提交时生效

```
$form->text('title')->creationRules('required|min:3');
```

## [更新页面规则](#%E6%9B%B4%E6%96%B0%E9%A1%B5%E9%9D%A2%E8%A7%84%E5%88%99)

只在更新表单提交时生效

```
$form->text('title')->updateRules('required|min:3');
```

## [数据库unique检查](#%E6%95%B0%E6%8D%AE%E5%BA%93unique%E6%A3%80%E6%9F%A5)

一个比较常见的场景是提交表单是检查数据是否已经存在，可以使用下面的方式：

```
$form->text('username')
    ->creationRules(['required', "unique:user_table"])
    ->updateRules(['required', "unique:user_table,username,{{id}}"]);
```
