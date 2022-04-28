<?php

namespace app\adminapi\controller;

use think\Controller;

class BaseApi extends Controller
{
    protected function _initialize()
    {
    	//处理跨域请求
    	//允许的源域名
	    header("Access-Control-Allow-Origin: *");
	    //允许的请求头信息
	    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
	    //允许的请求类型
	    header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
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
