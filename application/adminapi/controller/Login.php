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

    //登录接口
    public function login()
    {
        //接收参数
        $params=input();
        
        //参数检测（表单验证）
        $validate=$this->validate($params,[
            'username|用户名'=>'require',
            'password|密码'=>'require',
            //'code|验证码'=>'require|captcha:'.$params['uniqid'],//验证码自动校验
            'uniqid|验证码标识'=>'require'
        ]);
        if($validate!==true)
        {
            //验证失败
            $this->fail($validate,401);
        }
        //从缓存中根据uniqid获取session_id,设置session_id,用于验证码校验
        $session_id=cache('session_id_'.$params['uniqid']);
        if($session_id)
            session_id($session_id);
        //校验验证码(手动校验)
        if(!captcha_check($params['code'],$params['uniqid']))
        {
            //验证码错误
            //$this->fail('验证码错误',402);//测试
        }
        //查询用户表进行认证
        $password=encrypt_password($params['password']);
        $info=\app\common\model\Admin::where('username',$params['username'])->where('password',$password)->find();
        if(empty($info))
        {
            //用户名或密码错误
            $this->fail('用户名或密码错误',403);
        }
        //生成token令牌
        $token=\tools\jwt\Token::getToken($info['id']);
        //返回数据
        $data=[
            'token'=>$token,
            'user_id'=>$info['id'],
            'username'=>$info['username'],
            'nickname'=>$info['nickname'],
            'email'=>$info['email']
        ];
        $this->ok($data);

    }

    public function logout()
    {
        //记录token为已退出
        //获取当前请求中的token
        $token=\tools\jwt\Token::getRequestToken();
        //从缓存中取出注销的token数组
        $delete_token=cache('delete_token') ?: [];
        //将当前token加入注销数组
        $delete_token[]=$token;
        //将新数组重新存入缓存
        cache('delete_token',$delete_token);
        //返回数据
        $this->ok();
    }
}
