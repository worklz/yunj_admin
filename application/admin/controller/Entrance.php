<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\User as UserModel;

/**
 * 入口
 */
class Entrance extends Controller{

    public static function check(){
        //校验登录
        self::checkLogin();
        //校验操作是否超时
        self::checkActionTimeout();
        //校验是否有权限
        self::checkAuth();
    }

    /**
     * Description: 错误跳转
     * Author: Uncle-L
     * Date: 2019/9/29
     * Time: 9:52
     * @param string $url
     * @param array $params
     * @return bool|void
     */
    private static function errorRedirect($url='',$params=[]){
        $Entrance=new Entrance();
        return $Entrance->redirect($url,$params);
    }

    /**
     * Description: 校验登录，验证签名是否正确
     * Author: Uncle-L
     * Date: 2019/9/27
     * Time: 18:26
     */
    private static function checkLogin(){
        //获取签名数据
        $uid=(int)getUserData('uid');
        $sign=getUserData('sign');
        if(!$uid||!$sign){
            return self::errorRedirect('Error/index',['enum'=>'OVERDUE']);
        }
        $UserModel=new UserModel();
        $fieldArr=['id','username','login_pwd','login_salt','last_login_ip','last_login_time','create_time'];
        $whereArr=[
            ['id','eq',$uid],
            ['status','eq',1]
        ];
        $data=$UserModel->getOwnRow($fieldArr,$whereArr);
        if(!$data){
            return self::errorRedirect('Error/index',['enum'=>'OVERDUE']);
        }else{
            $data=$data->toArray();
        }
        //验证签名是否正确
        $_sign=generateLoginSign($data);
        if($sign!=$_sign){
            return self::errorRedirect('Error/index',['enum'=>'OVERDUE']);
        }
        return true;
    }

    /**
     * Description: 校验操作是否超时
     * Author: Uncle-L
     * Date: 2019/9/27
     * Time: 18:25
     */
    private static function checkActionTimeout(){
        $lastActionTime=getUserData('action_time');
        if(!$lastActionTime){
            return self::errorRedirect('Error/index',['enum'=>'TIMEOUT']);
        }
        //时间差
        $timeDifference=time()-$lastActionTime;
        //操作超时时间
        $actionTimeout=sconfig('base.action_timeout');
        if($timeDifference<0||$timeDifference>$actionTimeout){
            return self::errorRedirect('Error/index',['enum'=>'TIMEOUT']);
        }
        //更新操作时间
        setUserData('action_time',time());
        return true;
    }

    /**
     * Description: 校验是否有权限
     * Author: Uncle-L
     * Date: 2019/9/27
     * Time: 18:26
     */
    private static function checkAuth(){
        //获取当前的控制器方法名
        $code=request()->controller() . '/' . request()->action();
        $res=checkAuth($code);
        if(!$res){
            return self::errorRedirect('Error/index',['enum'=>'NO_AUTH']);
        }
        return true;
    }
}