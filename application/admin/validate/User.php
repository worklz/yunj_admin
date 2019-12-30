<?php
namespace app\admin\validate;

use app\common\model\User as UserModel;

class User extends Common {
    protected $rule = [
        'page' => 'require|isPositiveInteger',
        'limit' => 'require|isPositiveInteger',
        'username' => 'require|max:50|regex:[a-zA-Z][a-zA-Z0-9_]*|unique:user',
        'password' => 'require|regex:\w{6,18}|confirm',
        'password_confirm' => 'require',
        'nickname' => 'require|max:50|unique:user',
        'email' => 'require|max:60|email|unique:user',
        'mobile' => 'require|max:20|mobile|unique:user',
        'role_id' => 'require|isPositiveInteger',
        'id' => 'require|isPositiveInteger',
        'status' => 'require|in:0,1',
        'ids' => 'require|isPositiveIntegerStr',
        'title' => 'require',
    ];

    protected $message = [
        'pid.require' => '请选择父级菜单',
        'username.require' => '请输入登录账户',
        'username.regex' => '登录账户格式错误',
        'username.unique' => '该登录账户已存在',
        'password.require' => '请输入密码',
        'password.regex' => '密码规则错误',
        'password.confirm' => '确认密码错误',
        'password_confirm.require' => '请输入确认密码',
        'nickname.require' => '请输入用户昵称',
        'nickname.unique' => '该用户昵称已存在',
        'email.require' => '请输入邮箱',
        'email.unique' => '该邮箱已存在',
        'mobile.require' => '请输入手机',
        'mobile.unique' => '该手机已存在',
    ];

    protected $scene = [
        'search'=>['page','limit'],
        'add' => ['username','password','password_confirm','nickname','email','mobile','role_id'],
        'edit_query' => ['id'],
        'edit' => ['id','nickname','email','mobile','role_id','status'],
        'status'=>['id','status'],
        'del_batch'=>['ids','title'],
        'export'=>['title'],
    ];

    /**
     * Description: 移除编辑场景下nickname的唯一性验证
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 16:53
     * @return $this
     */
    public function sceneEdit() {
        return $this->only($this->scene['edit'])
            ->remove('nickname', 'unique');
    }

    protected function handleData($_data, $scene) {
        $data = $_data;
        switch ($scene) {
            case 'add':
                $login_salt=randomStr();
                $login_pwd=md5(md5($data['password']).md5($login_salt));
                $data=[
                    'username'=>$data['username'],
                    'nickname'=>$data['nickname'],
                    'login_pwd'=>$login_pwd,
                    'login_salt'=>$login_salt,
                    'email'=>$data['email'],
                    'mobile'=>$data['mobile'],
                    'role_id'=>$data['role_id'],
                ];
                break;
            case 'edit':
                $data=$this->handleDataByEdit($_data);
                break;
        }
        return $data;
    }

    private function handleDataByEdit($_data){
        $data=[
            'id'=>$_data['id'],
            'username'=>$_data['username'],
            'nickname'=>$_data['nickname'],
            'email'=>$_data['email'],
            'mobile'=>$_data['mobile'],
            'role_id'=>$_data['role_id'],
            'status'=>$_data['status'],
        ];
        //id=1时，status!=0
        if($_data['id']==1&&$_data['status']==0){
            returnJson(['code'=>'99999','msg'=>'超级管理员不能禁用']);
        }
        //是否设置新密码
        if($_data['password']){
            if(!preg_match_all('/^\w{6,18}$/',$_data['password'])){
                returnJson(['code'=>'99999','msg'=>'密码规则错误']);
            }
            if($_data['password']!=$_data['password_confirm']){
                returnJson(['code'=>'99999','msg'=>'确认密码错误']);
            }
            $login_salt=randomStr();
            $login_pwd=md5(md5($_data['password']).md5($login_salt));
            $data['login_pwd']=$login_pwd;
            $data['login_salt']=$login_salt;
        }
        //不是超级管理员需校验密码
        $uid=getUserData('uid');
        if($uid!=1){
            if(!$_data['password_check']){
                returnJson(['code'=>'99999','msg'=>'请输入校验密码']);
            }
            $fieldArr=['login_pwd','login_salt'];
            $whereArr=[['id','eq',$_data['id']]];
            $user=(new UserModel())->getRow($fieldArr,$whereArr);
            if($user['login_pwd']!=md5(md5($_data['password_check']).md5($user['login_salt']))){
                returnJson(['code'=>'99999','msg'=>'校验密码错误']);
            }
        }
        return $data;
    }
}