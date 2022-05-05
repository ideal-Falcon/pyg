<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];

//后台接口域名路由adminapi
Route::domain('adminapi',function(){
	//adminapi模块首页路由
	Route::get('/','adminapi/index/index');

	//获取验证码接口;//访问图片需要 
	Route::get('captcha/:id','\\think\\captcha\\CaptchaController@index');
	Route::get('captcha','adminapi/login/captcha');
	//登录接口
	Route::post('login','adminapi/login/login');

	//退出接口
	Rout::get('logout','adminapi/login/logout');

});
