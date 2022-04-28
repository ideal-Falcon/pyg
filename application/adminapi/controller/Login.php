<?php

namespace app\adminapi\controller;

use think\Controller;

class Login extends BaseApi
{
    //验证码接口
    public function captcha()
    {
    	//验证码唯一标识
    	$uniqid=uniqid(mt_rand(100000,999999));
    	//生成验证码地址
    	$src=captcha_src($uniqid);
    	$res=[
    		'src'=>$src,
    		'uniqid'=>$uniqid
    	];
    	//返回数据
    	$this->ok($res);
    }
}
