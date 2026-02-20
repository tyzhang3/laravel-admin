# 头部和脚部

`v1.6.10`版本之后，在表格的上增加了头部和脚部两个区块，可以在里面填入你想要的内容

```
$grid->header(function ($query) {
    return 'header';
});

$grid->footer(function ($query) {
    return 'footer'; 
});
```

其中闭包函数的参数`$query`为`\Illuminate\Database\Eloquent\Builder`类实例，可以用来执行一些自定义的查询来获取数据，下面是两个不同场景的使用举例

## [头部](#%E5%A4%B4%E9%83%A8)

比如要在用户数据模块的表格头部插入一个饼状图表，来显示用户性别比例，可以按照下面的方法来实现

先查询出性别比例数据：

```
$grid->header(function ($query) {

    $gender = $query->select(DB::raw('count(gender) as count, gender'))
        ->groupBy('gender')->get()->pluck('count', 'gender')->toArray();

    $doughnut = view('admin.chart.gender', compact('gender'));

    return new Box('性别比例', $doughnut);
});
```

然后使用图表插件[chartjs](https://github.com/laravel-admin-extensions/chartjs)，来构建图表, 视图文件`resources/views/admin/chart/gender.blade.php`如下

```
<canvas id="doughnut" width="200" height="200"></canvas>
<script>
$(function () {

    var config = {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [
                    {{ $gender['m'] }},
                    {{ $gender['f'] }},
                    {{ $gender[''] }}
                ],
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(255, 205, 86)'
                ]
            }],
            labels: [
                'Male',
                'Female',
                'Unknown'
            ]
        },
        options: {
            maintainAspectRatio: false
        }
    };

    var ctx = document.getElementById('doughnut').getContext('2d');
    new Chart(ctx, config);
});
</script>
```

实现效果如下:

![WX20190310-195204](https://user-images.githubusercontent.com/1479100/54084635-1b993600-436e-11e9-97ab-4cb5fa5cff87.png)

## [脚部](#%E8%84%9A%E9%83%A8)

一个比较常见的场景是在数据表格的脚部显示统计信息，比如在订单表格的脚部加入收入统计，可以参考下面的代码实现：

```
$grid->footer(function ($query) {

    // 查询出已支付状态的订单总金额
    $data = $query->where('status', 2)->sum('amount');

    return "<div style='padding: 10px;'>总收入 ： $data</div>";
});
```

如果有比较复杂的脚部需要显示，也可以像头部例子一样，使用视图对象来实现。
