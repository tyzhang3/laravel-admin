# 语言本地化

laravel-admin安装完成之后，如果界面显示的文字还是英文，说明你的框架语言设置为英文，需要在`config/app.php`里面修改为中文：

```
    'locale' => 'zh-CN',
```

## [表单验证错误本地化](#%E8%A1%A8%E5%8D%95%E9%AA%8C%E8%AF%81%E9%94%99%E8%AF%AF%E6%9C%AC%E5%9C%B0%E5%8C%96)

如果表单验证错误文字显示的是英文，你需要安装校验语言包，可以参考[Laravel-lang](https://github.com/caouecs/Laravel-lang)中的文档安装。

## [控制器字段label](#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%AD%97%E6%AE%B5label)

运行`admin:make`创建控制器之后，表格和表单的的label默认使用`__('Column name')`，如下：

```
    $grid->column('id', __('ID'));
    $grid->column('name', __('Name'));
    $grid->column('email', __('Email'));
    $grid->column('created_at', __('Created at'));
    $grid->column('updated_at', __('Updated at'));
```

你可以直接修改第二个参数为中文的字段label，但是最好的办法是去设置`字符串翻译`，到`resources/lang`目录下新建`resources/lang/zh-CN.json`，增加对应的翻译：

```
{
    "ID": "ID",
    "Created at":"创建时间",
    "Updated at":"更新时间",
    "name":"名字",
    "email":"邮箱"
}
```

这样的话，其它控制器也能复用这里的翻译。
