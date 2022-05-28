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

		// echo encrypt_password('123456');


	    echo $this->http_request('http://www.exb.cn/api/logout');

	}

	private function http_request($url,$data = null,$headers=array())
	{
	    $curl = curl_init();
	    if( count($headers) >= 1 ){
	        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    }
	    curl_setopt($curl, CURLOPT_URL, $url);

	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

	    if (!empty($data)){
	        curl_setopt($curl, CURLOPT_POST, 1);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	    }
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    $output = curl_exec($curl);
	    curl_close($curl);
	    return $output;
	}
}