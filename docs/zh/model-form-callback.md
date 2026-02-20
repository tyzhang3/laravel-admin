# 模型表单回调

`model-form`目前提供了下面几个方法来接收回调函数：

```
// 在表单提交前调用
$form->submitted(function (Form $form) {
    //...
});

//保存前回调
$form->saving(function (Form $form) {
    //...
});

//保存后回调
$form->saved(function (Form $form) {
    //...
});
```

可以从回调参数`$form`中获取当前提交的表单数据：

```
$form->saving(function (Form $form) {

    dump($form->username);

});
```

或者给某一个表单项赋值：

```
$form->text('name');
$form->hidden('slug');

$form->saving(function (Form $form) {

    $form->slug = $form->name;

});
```

获取获取模型中的数据

```
$form->saved(function (Form $form) {

    $form->model()->id;

});
```

可以直接在回调中返回`Symfony\Component\HttpFoundation\Response`的实例，来跳转或进入页面：

```
$form->saving(function (Form $form) {

    // 返回一个简单response
    return response('xxxx');

});

$form->saving(function (Form $form) {

    // 跳转页面
    return redirect('/admin/users');

});

$form->saving(function (Form $form) {

    // 抛出异常
    throw new \Exception('出错啦。。。');

});
```

返回错误或者成功信息在页面上：

```
use Illuminate\Support\MessageBag;

// 抛出错误信息
$form->saving(function ($form) {

    $error = new MessageBag([
        'title'   => 'title...',
        'message' => 'message....',
    ]);

    return back()->with(compact('error'));
});

// 抛出成功信息
$form->saving(function ($form) {

    $success = new MessageBag([
        'title'   => 'title...',
        'message' => 'message....',
    ]);

    return back()->with(compact('success'));
});
```

## [删除前后](#%E5%88%A0%E9%99%A4%E5%89%8D%E5%90%8E)

> since v1.6.13, 从v1.7版本已经废弃，可使用模型的删除回调替代

在删除的前后增加了两个回调`deleting`和`deleted`。

可以直接抛出异常

```
$form->deleting(function () {
    ...
    throw new \Exception('产生错误！！');
});

$form->deleted(function () {
    ...
    throw new \Exception('hahaa');
});
```

直接返回一个json response，可以用来修改文案提示：

```
$form->deleting(function () {
    ...
    return response()->json([
        'status'  => false,
        'message' => '删除失败，请。。',
    ]);
});

$form->deleted(function () {
    ...
    return response()->json([
        'status'  => false,
        'message' => '删除失败，请。。',
    ]);
});
```
