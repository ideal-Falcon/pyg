<?php

namespace app\adminapi\controller;

use think\Request;

class Admin extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //接收参数 keyword pagesize（每页显示数） page(paginate会自动接收page参数)
        $params=input();
        $where=[];
        //搜索条件
        if(!empty($params['keyword']))
        {
            $keyword=$params['keyword'];
            $where['username']=['like',"%$keyword%"];
        }
        //分页查询（包含搜索）MVC
        // $list=\app\common\model\Admin::where($where)->paginate(2,false,['query'=>['keyword'=>$keyword]]);

        //$list=\app\common\model\Admin::where($where)->paginate($params['pagesize']);

        $list=\app\common\model\Admin::alias('t1')
        ->join('pyg_role t2','t1.role_id=t2.id','left')
        ->field('t1.*,t2.role_name')
        ->where($where)
        ->paginate($params['pagesize']);
        $this->ok($list);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收参数
        $params=input();
        //参数检测
        $validate=$this->validate($params,[
            'username|用户名'=>'require|unique:admin,username',
            'email|邮箱'=>'require|email',
            'role_id|所属角色'=>'require|integer|gt:0',
            'password|密码'=>'length:6,20'
        ]);
        if($validate!==true){
            $this->fail($validate);
        }
        //添加数据
        if(empty($params['password'])){
            $params['password']='123456';
        }
        $params['password']=encrypt_password($params['password']);
        $params['nickname']=$params['username'];
        $info=\app\common\model\Admin::create($params,true);
        //查询添加的完整数据
        $admin=\app\common\model\Admin::find($info['id']);
        //返回数据
        $this->ok($admin);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //查询数据
        $info=\app\common\model\Admin::find($id);
        //返回数据
        $this->ok($info);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //接收数据
        $params=input();
        if(!empty($params['type'])&&$params['type']=='reset_pwd'){
            $password=encrypt_password('123456');
            \app\common\model\Admin::update(['password'=>$password],['id'=>$id],true);
        }else{
            //参数检测
            $validate=$this->validate($params,[
                'email|邮箱'=>'email',
                'role_id|所属角色'=>'integer|gt:0',
                'nickname|昵称'=>'max:50'
            ]);
            if($validate!==true){
                $this->fail($validate);
            }
            //修改数据
            unset($params['username']);
            unset($params['password']);
            \app\common\model\Admin::update($params,['id'=>$id],true);
        }
        
        $info=\app\common\model\Admin::find($id);
        //返回数据
        $this->ok($info);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //删除数据（不能删除自己）
        if($id==1){
            $this->fail('不能删除超级管理员');
        }
        if($id==input('id')){
            $this->fail('不能删除自己');
        }
        \app\common\model\Admin::destroy($id);
        //返回数据
        $this->ok();
    }
}
