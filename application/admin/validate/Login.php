<?php
namespace app\admin\validate;

use app\common\model\User as UserModel;

class Login extends Common {
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'captcha_code|验证码' => 'require|captcha',
    ];

    protected $message = [
        'username.require' => '请输入账户名',
        'password.require' => '请输入密码',
    ];

    protected $scene = [
        'login'=>['username','password','captcha_code'],
    ];

    protected function handleData($_data, $scene) {
        $data = $_data;
        switch ($scene) {
            case 'login':
                $data=$this->handleDataByLogin($_data);
                break;
        }
        return $data;
    }

    /**
     * Description: 处理登录数据
     * Author: Uncle-L
     * Date: 2019/8/22
     * Time: 22:21
     * @param $_data
     * @return array
     */
    private function handleDataByLogin($_data){
        $fieldArr=['u.id','u.username','u.nickname','u.login_pwd','u.login_salt','u.create_time','r.name as role_name','r.alias as role_alias','r.menu_ids'];
        $whereArr=[['u.username','eq',$_data['username']],['u.status','neq',0]];
        $UserModel=new UserModel();
        $user=$UserModel->getRow($fieldArr,$whereArr);
        if(!$user){
            returnJson(['code'=>'99999','msg'=>'该用户不存在或已失效']);
        }
        //校验密码
        if($user['login_pwd']!=md5(md5($_data['password']).md5($user['login_salt']))){
            returnJson(['code'=>'99999','msg'=>'用户名或密码错误']);
        }
        $last_login_ip=getIP();
        $last_login_time=time();
        $changeData=[
            'id'=>$user['id'],
            'last_login_ip'=>$last_login_ip,
            'last_login_time'=>$last_login_time,
        ];
        $changeRes=$UserModel->change($changeData);
        if(!$changeRes){
            returnJson(['code'=>'99999','msg'=>'记录用户登录信息错误']);
        }
        $data=[
            'uid'=>$user['id'],
            'username'=>$user['username'],
            'password'=>$_data['password'],
            'remember'=>isset($_data['remember'])?$_data['remember']:null,
            'nickname'=>$user['nickname'],
            'login_pwd'=>$user['login_pwd'],
            'login_salt'=>$user['login_salt'],
            'last_login_ip'=>$last_login_ip,
            'last_login_time'=>$last_login_time,
            'create_time'=>$user['create_time'],
            'role_name'=>$user['role_name'],
            'role_alias'=>$user['role_alias'],
            'menu_ids'=>$user['menu_ids'],
        ];
        return $data;
    }
}