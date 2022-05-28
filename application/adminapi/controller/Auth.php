<?php

namespace app\adminapi\controller;
use think\Request;

class Auth extends BaseApi
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //接收参数
        $params=input();
        $where=[];
        if(!empty($params['keyword']))
        {
            $where['auth_name']=['like',"%{$params['keyword']}%"];
        }
        //查询数据
        $list=\app\common\model\Auth::where($where)->select();
        //转换为标准的二维数组
        $list=(new \think\Collection($list))->toArray();
        if(!empty($params['type'])&&$params['type']=='tree')
        {
            //父子级树状列表
            $list=get_tree_list($list);
        }
        else
        {
            //无限级分类列表
            $list=get_cate_list($list);
        }
        
        //返回数据
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
        //临时处理
        if(empty($params['pid'])){
            $params['pid']=0;
        }
        if(empty($params['is_nav'])){
            $params['is_nav']=$params['radio'];
        }
        //参数检测
        $validate=$this->validate($params,[
            'auth_name|权限名称'=>'require',
            'pid|上级权限'=>'require',
            'is_nav|菜单权限'=>'require'
        ]);
        if($validate!==true)
            $this->fail($validate,401);
        //添加数据（是否顶级，级别和pid_path处理）
        if($params['pid']==0)
        {
            $params['level']=0;
            $params['pid_path']=0;
            $params['auth_c']='';
            $params['auth_a']='';
        }
        else
        {
            //不是顶级
            //查询上级信息
            $p_info=\app\common\model\Auth::find($params['pid']);
            if(empty($p_info))
                $this->fail('数据异常');
            //设置级别+1  家族图谱拼接
            $params['level']=$p_info['level']+1;
            $params['pid_path']=$p_info['pid_path'].'_'.$p_info['id'];
        }
        $auth=\app\common\model\Auth::create($params,true);
        $this->ok();
        //返回数据
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    #read方法传入id会直接定位到这
    public function read($id)
    {
        //查询数据
        $auth=\app\common\model\Auth::find($id);
        //返回数据
        $this->ok($auth);
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
        //临时处理
        if(empty($params['pid'])){
            $params['pid']=0;
        }
        if(empty($params['is_nav'])){
            $params['is_nav']=$params['radio'];
        }
        //参数检测
        $validate=$this->validate($params,[
            'auth_name|权限名称'=>'require',
            'pid|上级权限'=>'require',
            'is_nav|菜单权限'=>'require'
        ]);
        if($validate!==true)
            $this->fail($validate,401);
        //修改数据
        $auth=\app\common\model\Auth::find($id);
        if(empty($auth))
            $this->fail("数据异常");
        if($params['pid']==0){
            $params['level']=0;
            $params['pid_auth']=0;
        }else if($params['pid']!=$auth['pid']){
            $p_auth=\app\common\model\Auth::find($params['pid']);
            if(empty($p_auth))
                $this->fail("数据异常");
            $params['level']=$p_auth['level']+1;
            $params['pid_auth']=$p_auth['pid_path'].'_'.$p_auth['id'];
        }
        \app\common\model\Auth::update($params,['id'=>$id],true);
        //返回数据
        $this->ok();
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //判断是否有子权限
        \app\common\model\Auth::where('pid',$id)->count();
        if($total>0){
            $this->fail('有子权限，无法删除');
        }
        //删除数据
        \app\common\model\Auth::destroy($id);
        //返回数据
        $this->ok();
    }

    //菜单权限
    public function nav()
    {
        //获取登录的管理员用户id
        $user_id=input('user_id');
        //查询管理员的角色id
        $info=\app\common\model\Admin::find($user_id);
        //判断是否超级管理员
        $role_id=$info['role_id'];
        if($role_id==1){
            //超级管理员，直接查询权限表  菜单权限 is_nav
            $data=\app\common\model\Auth::where('is_nav',1)->select();
        }else{
            //先查询角色表
            $role=\app\common\model\Role::find($role_id);
            $role_auth_ids=$role['role_auth_ids'];
            //再查询权限表
            $data=\app\common\model\Auth::where('is_nav',1)->where('id','in',$role_auth_ids)->select();
        }
        //先转化为标准的二维数组
        $data=(new \think\Collection($data))->toArray();
        //再转化为父子树状结构
        $data=get_tree_list($data);
        //返回
        $this->ok($data);

    }
}
