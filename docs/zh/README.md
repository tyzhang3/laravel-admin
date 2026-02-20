# laravel-admin

[![Build Status](https://travis-ci.org/z-song/laravel-admin.svg?branch=master)](https://travis-ci.org/z-song/laravel-admin)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/z-song/laravel-admin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/z-song/laravel-admin/?branch=master)
[![Packagist](https://img.shields.io/packagist/l/encore/laravel-admin.svg?maxAge=2592000)](https://packagist.org/packages/encore/laravel-admin)
[![Total Downloads](https://img.shields.io/packagist/dt/encore/laravel-admin.svg?style=flat-square)](https://packagist.org/packages/encore/laravel-admin)

`laravel-admin` 是一个可以快速帮你构建后台管理的工具，它提供的页面组件和表单元素等功能，能帮助你使用很少的代码就实现功能完善的后台管理功能。

> ##### 注意
>
> 当前版本(v1.7.\*)需要安装`PHP 7+`和`Laravel 5.5+|6.x`

## [特性](#%E7%89%B9%E6%80%A7)

- 内置用户和权限系统
- `model-grid`支持快速构建数据表格
- `model-form`支持快速构建数据表单
- `model-tree`支持快速构建树状数据
- 内置40+种form元素组件、以及支持扩展组件
- 支持`Laravel`的多种模型关系
- `mysql`、`mongodb`、`pgsql`等多数据库支持
- 支持引入第三方前端库
- 数据库和artisan命令行工具的web实现
- 支持自定义图表
- 多种常用web组件
- 支持本地和oss文件上传

## [谁在使用](#%E8%B0%81%E5%9C%A8%E4%BD%BF%E7%94%A8)

如果你的公司和产品使用了laravel-admin，欢迎到[这里](https://github.com/z-song/laravel-admin/issues/3043)留言。

|  |  |  |  |  |  |
| --- | --- | --- | --- | --- | --- |
| [腾讯](https://www.tencent.com) | [百度](https://www.baidu.com) | [灵机文化](https://www.linghit.com) | [科零](http://www.trc-china.com/) | [成都聚米粒](http://www.jmltek.net) | [深圳墨兔科技](https://mall-to.com) |
| [模拟考试助手](https://okrd.cn) | [秋点科技](https://www.qiudian.net) | [上海彼邑网络科技](http://www.beats.net.cn) | [高灯科技创新中心](https://ai.wetax.com.cn) | [北京牙联惠](http://www.yalianhui.com) | [高策智慧零售数据平台](https://www.golcer.com) |
| [小于号网络科技](http://www.7rcv.com) | [中国光大集团](http://www.ebchina.com) | [苏州通润驱动设备](http://www.torindrive.com) | [上海钜石信息科技](https://www.rockfintech.com) | [广州量子云科技](https://www.quanta-nimbus.com) | [珠海谷猫科技](http://www.igumao.com) |
| [程序员精选导航](http://daohang.mianshia.com) | [四川文鼎文化科技](http://www.iwdjy.com) | [支付猫](https://www.paycats.cn) | [馨科智科技（深圳）](https://www.xinkezhi.com) | [八兔软件科技（上海）](http://www.brtoo.cn) | [四川卓谷科技](https://www.zoogooo.com) |
| [福州辑思文化](http://editideas.cn) | [美味学院](http://www.meiweixueyuan.cn) | [深圳企程网络](http://hiqicheng.com) | [北京路酷出行](http://www.lukus.cn) | [哈尔滨银行](http://www.hrbcb.com.cn) | [上海二三四五网络科技](http://www.2345.com) |
| [贵州国卫信安科技](#) | [Y2B downloader](https://www.y2b.xyz/) | [无锡元田信息科技](https://yuantian.tech) | [山东潍坊玖樱传媒](https://jfcat.cn) | [重庆初心文化传媒](https://www.fheart.cn) | [广西智汇云网络科技](http://www.gxsww.com) |
| [江苏艾克斯](#) | [成都希格斯网络科技](http://higgses.com/) | [十年之约项目组](https://www.foreverblog.cn) | [五宫格信息科技](https://www.wggai.com/) | [北京云中融信](https://www.rongcloud.cn) | [湖南希文网络科技](http://shanyue88.com/) |
| [广州数佰特信息](http://surebetter.cn) | [厦门鹅卵石网络](http://www.elscare.com/) | [上海拾芳信息](https://shifang.tech) | [长沙欣诺网络](http://www.csxino.cn/) | [陕西唯科网络](https://www.wkwang.cn/) | [OranMe Ltd.](https://oran.me) |

## [Demo](#Demo)

打开`http://demo.laravel-admin.org/`，用账号密码`admin/admin`登录

## [依赖](#%E4%BE%9D%E8%B5%96)

`laravel-admin` 基于以下组件或者服务:

- [Laravel](https://laravel.com/)
- [AdminLTE](https://almsaeedstudio.com/)
- [Datetimepicker](http://eonasdan.github.io/bootstrap-datetimepicker/)
- [font-awesome](http://fontawesome.io)
- [moment](http://momentjs.com/)
- [Google map](https://www.google.com/maps)
- [Tencent map](http://lbs.qq.com/)
- [bootstrap-fileinput](https://github.com/kartik-v/bootstrap-fileinput)
- [jquery-pjax](https://github.com/defunkt/jquery-pjax)
- [Nestable](http://dbushell.github.io/Nestable/)
- [toastr](http://codeseven.github.io/toastr/)
- [X-editable](http://github.com/vitalets/x-editable)
- [bootstrap-number-input](https://github.com/wpic/bootstrap-number-input)
- [fontawesome-iconpicker](https://github.com/itsjavi/fontawesome-iconpicker)

## [交流](#%E4%BA%A4%E6%B5%81)

QQ群:278455482(已满)、635881319(已满)、533701919(已满)、821831984(已满)

## [支持](#%E6%94%AF%E6%8C%81)

如果觉得这个项目帮你节约了时间，不妨支持一下;)

![-1](https://cloud.githubusercontent.com/assets/1479100/23287423/45c68202-fa78-11e6-8125-3e365101a313.jpg)

## [License](#License)

`laravel-admin` is licensed under [The MIT License (MIT)](LICENSE).
