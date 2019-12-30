<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\validate\Login as LoginValidate;

class Login extends Controller{
    /**
     * Description: 登录
     * Author: Uncle-L
     * Date: 2019/8/22
     * Time: 22:36
     * @return mixed
     */
    public function index(){
        if(request()->isAjax()){
            $this->loginHandle();
        }else{
            $this->assign([
                'title'=>'登录'
            ]);
            return $this->fetch();
        }
    }

    /**
     * Description: 登录处理
     * Author: Uncle-L
     * Date: 2019/8/22
     * Time: 22:36
     */
    private function loginHandle(){
        $data=input('post.');
        $data=(new LoginValidate())->checkAll($data,'login');
        $signData=[
            'id'=>$data['uid'],
            'username'=>$data['username'],
            'login_pwd'=>$data['login_pwd'],
            'login_salt'=>$data['login_salt'],
            'last_login_ip'=>$data['last_login_ip'],
            'last_login_time'=>$data['last_login_time'],
            'create_time'=>$data['create_time']
        ];
        //处理session
        $loginUserData=[
            'uid'=>$data['uid'],
            'username'=>$data['username'],
            'nickname'=>$data['nickname'],
            'role_name'=>$data['role_name'],
            'role_alias'=>$data['role_alias'],
            'sign'=>generateLoginSign($signData),
            'menu_ids'=>$data['menu_ids'],
            'action_time'=>time()
        ];
        setUserData($loginUserData);
        //不同角色对应不同控制器，获取对应链接
        $controller='Error';
        switch ($data['role_alias']){
            case 'admin':
                $controller='Index';
                break;
            case 'admin_menu':
                $controller='Index';
                break;
        }
        returnJson([
            'msg'=>'登录成功',
            'data'=>['url'=>url("{$controller}/index")]
        ]);
    }

    /**
     * Description: 切换帐号
     * Author: Uncle-L
     * Date: 2019/8/22
     * Time: 22:36
     */
    public function change(){
        setUserData(null);
        $this->redirect('Login/index');
    }

    /**
     * Description: 退出
     * Author: Uncle-L
     * Date: 2019/7/15
     * Time: 19:49
     */
    public function logout(){
        setUserData(null);
        $this->redirect('Error/index',['enum'=>'LOGOUT']);
    }
}