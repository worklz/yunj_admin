<?php
// +----------------------------------------------------------------------
// | 公共方法
// +----------------------------------------------------------------------

/**
 * Description: 从session中获取登录用户状态数据
 * Author: Uncle-L
 * Date: 2019/12/30
 * Time: 17:18
 * @param string $key   [用户字段key]
 * @param null $default [默认返回值]
 * @return mixed|null
 */
function getUserData($key='',$default=null){
    $session_login_user_key=config('common.session_login_user_key');
    $user=session($session_login_user_key);
    if(!$user) return $default;
    if($key){
        return isset($user[$key])?$user[$key]:$default;
    }
    return $user;
}

/**
 * Description: 在session中设置登录用户状态数据
 * Author: Uncle-L
 * Date: 2019/12/30
 * Time: 17:32
 * @param $param  [设置用户的字段key，或者key-value对应的数组，或者null]
 * @param null $val
 * @return mixed
 */
function setUserData($param,$val=null){
    $session_login_user_key=config('common.session_login_user_key');
    $datas=is_array($param)?$param:[];
    if($datas){
        $user=session($session_login_user_key);
        $user=$user?$user:[];
        foreach ($datas as $k=>$v){
            $user[$k]=$v;
        }
        return session($session_login_user_key,$user);
    }else{
        if(!$param){
            return session($session_login_user_key,null);
        }
        return session("{$session_login_user_key}.{$param}",$val);
    }
}

/**
 * Description: 验证用户是否有当前控制器/方法的权限
 * Author: Uncle-L
 * Date: 2019/8/23
 * Time: 9:56
 * @param $code [控制器/方法名]
 * @return bool
 */
function checkAuth($code){
    if(getUserData('uid')==1) return true;
    //不需要验证的控制器/方法名
    $no_check_controller_action=sconfig('base.no_check_controller_action');
    if(in_array($code,$no_check_controller_action)) return true;
    //根据获取当前的控制器方法名，获取当前对应菜单的id
    $MenuModel=new \app\common\model\Menu();
    $whereArr=[
        ['status','neq',0],
        ['code','eq',$code]
    ];
    //正式环境中应设置缓存
    $menuID=$MenuModel->where($whereArr)->value('id');
    if(!$menuID) return false;
    $menu_ids=session('menu_ids');
    $menu_ids=is_array($menu_ids)?$menu_ids:explode(',',$menu_ids);
    if(!in_array($menuID,$menu_ids)){
        return false;
    }
    return true;
}

/**
 * Description: 系统日志记录
 * Author: Uncle-L
 * Date: 2019/8/27
 * Time: 18:18
 * @param $description [日志简述]
 * @return bool
 */
function systemLog($description){
    if(!getUserData('uid')) return false;
    $request_content=[
        'url'=>request()->url(),
        'type'=>request()->method(),
        'params'=>input('param.')
    ];
    $data=[
        'uid'=>getUserData('uid'),
        'ip'=>getIP(),
        'description'=>$description,
        'request_content'=>json_encode($request_content,JSON_UNESCAPED_SLASHES),
    ];
    $res=model('Log')->addRow($data);
    return $res?true:false;
}