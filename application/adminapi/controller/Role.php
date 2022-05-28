<?php

namespace app\adminapi\controller;

use think\Request;

class Role extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询数据(不需要查询超级管理员)
        $list=\app\common\model\Role::where('id','>',1)->select();
        //对每条角色数据，查询对应的权限，增加role_auths下班的数据（父子级树状结构）
        foreach ($list as &$v) {
            //查询权限表
            $auths=\app\common\model\Auth::where('id','in',$v['role_auth_ids'])->select();
            //先转化为标准的二维数组
            $auths=(new \think\Collection($auths))->toArray();
            //转化为父子级树状结构
            $auths=get_tree_list($auths);
            $v['role_auths']=$auths;
        }
        unset($v);//使用&引用，强烈建议使用unset释放引用内存
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
        //接收数据
        $params=input();
        //参数检测
        $validate=$this->validate($params,[
            'role_name'=>'require',
            'auth_ids'=>'require'
        ]);
        if($validate!==true){
            $this->fail($validate);
        }
        //添加数据
        $params['role_auth_ids']=$params['auth_ids'];
        $role=\app\common\model\Role::create($params,true);
        $info=\app\common\model\Role::find($role['id']);
        //返回数据
        $this->ok($info);
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
        $info=\app\common\model\Role::field('id,role_name,desc,role_auth_ids')->find($id);
        //返回数据
        $this->ok($info);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
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
        //参数检测
        $validate=$this->validate($params,[
            'role_name'=>'require',
            'auth_ids'=>'require'
        ]);
        if($validate!==true){
            $this->fail($validate);
        }
        //修改数据
        $params['role_auth_ids']=$params['auth_ids'];
        \app\common\model\Role::update($params,['id'=>$id],true);
        $info=\app\common\model\Role::find($id);
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
        //超级管理员无法删除
        if($id=1){
            $this->fail('该角色无法删除');
        }
        //如果角色下有管理员,无法删除
        //根据角色id查询管理员表的role_id字段
        $total=\app\common\model\Admin::where('role_id',$id)->count();
        if($total>0){
            $this->fail('角色正在使用中，无法删除');
        }
        //删除数据
        \app\common\model\Role::destroy($id);
        //返回数据
        $this->ok();

    }
}
