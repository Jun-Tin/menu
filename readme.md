# menu
<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## Learning Laravel

Laravel has the most extensive and thorough documentation and video tutorial library of any modern web application framework. The [Laravel documentation](https://laravel.com/docs) is thorough, complete, and makes it a breeze to get started learning the framework.

If you're not in the mood to read, [Laracasts](https://laracasts.com) contains over 900 video tutorials on a range of topics including Laravel, modern PHP, unit testing, JavaScript, and more. Boost the skill level of yourself and your entire team by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for helping fund on-going Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](http://patreon.com/taylorotwell):

- **[Vehikl](http://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[British Software Development](https://www.britishsoftware.co)**
- **[Styde](https://styde.net)**
- [Fragrantica](https://www.fragrantica.com)
- [SOFTonSOFA](https://softonsofa.com/)
- [User10](https://user10.com)
- [Soumettre.fr](https://soumettre.fr/)

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

<!-- 生成项目key -->
php artisan key:generate
<!-- 第一次安装passport通讯key -->
php artisan migrate passport:install
<!-- 重置数据库后安装 -->
php artisan passport:install --force
<!-- 出现如上错误是因为没有生成密钥，需要执行如下命令： -->
php artisan passport:keys --force

<!-- 记录数据库状态值（status） -->
					格式：表名，字段，注释 
	menu,		status,				0：售罄，1：正常
	place, 		status,				0：没人，1：有人，2：在打扫
	order, 		status,				0：未支付，2：已支付，3：已取消
	order,		finish,				0：未打扫，1：已打扫
	order,		state,				0：正在做，1：已做完，2：已上完
	order_detail, status,			0：未完成，1：正在做，2：已做完，3：准备上菜，4：已上菜，5：退菜，
	book,		status, 			0：未完成，1：已完成，2：逾期
	order, 		payment_method 		1：现金，2：微信，3：支付宝，4：Apple Pay，5：信用卡，6：其他，7：paypal
	user,		created_by			0：系统，{id}：用户id
	bill, 		method 				1：现金，2：微信，3：支付宝，4：Apple Pay，5：信用卡，6：其他，7：coins，8：paypal
	line,		status 				0：未叫号，1：正在叫号，2：切号


<!-- 设置socket registerAddress -->
修改vendor->workerman->gateway-worker->src->Lib->Gateway.php

<!-- 部署 Horizon -->
使用 php artisan horizon:terminate 来正常停止系统中的 Horizon 主进程，然后在 php artisan horizon 启动Horizon
php artisan horizon:pause 暂停
php artisan horizon:continue 恢复

<!-- 部署队列 queue -->
php artisan queue:work 启动
php artisan queue:restart 重启

<!-- socket消息事件 -->
退菜通知
'type' => 'retreat', 'message' => '退菜了！'
做饭通知
'type' => 'cooking', 'message' => '做饭了！'
更新上菜消息通知
'type' => 'update serving', 'message' => '更新上菜消息！'
上菜通知
'type' => 'serving', 'message' => '上菜了！'
菜品销售状态改变通知
'type' => 'saleStatus', 'message' => '菜品销售状态改变！'
更新排队列表
'type' => 'lining', 'message' => '更新排队列表！'
新菜品通知
'type' => 'new_dishes', 'message' => '新菜品通知！',
新排队通知
'type' => 'new_lines', 'message' => '新排队通知！',
