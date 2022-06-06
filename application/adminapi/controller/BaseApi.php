<?php

namespace app\adminapi\controller;

use think\Controller;

class BaseApi extends Controller
{
    //无需登录的请求
    protected $no_login=['login/captcha','login/login'];
    protected function _initialize()
    {
    	//处理跨域请求
    	//允许的源域名
	    header("Access-Control-Allow-Origin: *");
	    //允许的请求头信息
	    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
	    //允许的请求类型
	    header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');

        try{
            //登录检测
            //获取当前请求的控制器方法名
            $path=strtolower($this->request->controller()).'/'.$this->request->action();
            if(!in_array($path, $this->no_login)){
                //需要做登录检测
                //$user_id=\tools\jwt\Token::getUserId();
                //测试
                $user_id=1;
                if(empty($user_id)){
                    $this->fail('token验证失败',403);
                }
                //权限检测
                $auth_check=\app\adminapi\logic\AuthLogic::check($user_id);
                if(!$auth_check)
                    $this->fail('没有权限访问',303);
                //将得到的用户id放到请求信息中
                $this->request->get('user_id',$user_id);
                $this->request->post('user_id',$user_id);
            }
        }catch(\Exception $e){
            //token解析失败
            $this->fail('token解析失败',404);
        }
        
    }

    //通用响应
    public function response($code=200,$msg='success',$data=[])
    {
    	$res=[
    		'code'=>$code,
    		'msg'=>$msg,
    		'data'=>$data
    	];
    	//原生写法
    	//echo json_decode($res,JSON_UNESCAPED_UNICODE);die;//防止转换为Unicode编码
    	//框架写法
    	json($res)->send();
    }

    //成功响应
    public function ok($data=[],$code=200,$msg='success')
    {
    	$this->response($code,$msg,$data);
    }
    //失败响应
    public function fail($msg,$code=500,$data=[])
    {
    	$this->response($code,$msg,$data);
    }
}
