<?php
namespace enum;

/**
 * 错误状态枚举
 */
class Error{
    const ERROR=[
        //默认404错误
        'DEFAULT_404'=>[
            'code'=>404,
            'isShowUrl'=>true,//是否显示链接
            'msg'=>'不好意思，您访问的页面不存在~'
        ],
        'LOGOUT'=>[
            'code'=>200,
            'isShowUrl'=>true,
            'msg'=>'您已成功退出平台！'
        ],
        'OVERDUE'=>[
            'code'=>401,
            'isShowUrl'=>true,
            'msg'=>'不好意思，您的登录已过期~'
        ],
        'TIMEOUT'=>[
            'code'=>408,
            'isShowUrl'=>true,
            'msg'=>'不好意思，您的操作已超时，请重新登录~'
        ],
        'NO_AUTH'=>[
            'code'=>401,
            'isShowUrl'=>true,
            'msg'=>'不好意思，您无权限访问...'
        ],
    ];

    /**
     * Description: 根据传入属性，获取对应信息
     * Author: Uncle-L
     * Date: 2019/7/14
     * Time: 18:07
     * @param $key
     * @return array
     */
    public static function get($key){
        $info=array_key_exists($key,self::ERROR)?self::ERROR[$key]:self::ERROR['DEFAULT_404'];
        if($key=='LOGOUT'){
            $info['msg'].='当前时间：'.date('Y-m-d H:i:s');
        }
        //首页地址
        $indexController='Index';
        switch (session('user_auth')){
            case 'admin':
                $indexController='Index';
                break;
        }
        $info['index_url']=url("{$indexController}/index");
        return $info;
    }
}