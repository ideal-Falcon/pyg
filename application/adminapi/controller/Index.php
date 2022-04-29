<?php

namespace app\adminapi\controller;

/**
 * 
 */
class Index extends BaseApi
{
	
	public function index()
	{
		//测试token类
		// $token=\tools\jwt\Token::getToken(100);
		// dump($token);
		// $user_id=\tools\jwt\Token::getUserId($token);
		// dump($user_id);die;

		echo encrypt_password('123456');
	}
}