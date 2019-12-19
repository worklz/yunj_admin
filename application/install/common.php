<?php
// +----------------------------------------------------------------------
// | 公共方法
// +----------------------------------------------------------------------

/**
 * Description: 安装初始化
 * Author: Uncle-L
 * Date: 2019/12/18
 * Time: 18:10
 */
function installInit(){
    $installPassStatusKey='install_pass_status';
    $installPassStatus=[
        'environment'=>false,
        'redis'=>false,
        'db'=>false,
        'account'=>false
    ];
    cache($installPassStatusKey,$installPassStatus);
}

/**
 * Description: 根据key获取当前key安装是否通过
 * Author: Uncle-L
 * Date: 2019/12/18
 * Time: 18:10
 * @param $key
 * @return bool
 */
function installPassStatus($key){
    $installPassStatusKey='install_pass_status';
    $installPassStatus=cache($installPassStatusKey);
    if(!$installPassStatus){
        return false;
    }
    if(!array_key_exists($key,$installPassStatus)){
        return false;
    }
    return $installPassStatus[$key];
}

/**
 * Description: 设置key的状态值
 * Author: Uncle-L
 * Date: 2019/12/18
 * Time: 18:10
 * @param $key
 * @param $status
 */
function setInstallPassStatus($key,$status){
    $installPassStatusKey='install_pass_status';
    $installPassStatus=cache($installPassStatusKey);
    if(!$installPassStatus){
        returnJson(['code'=>'99999','msg'=>'非法操作']);
    }
    $installPassStatus[$key]=$status;
    cache($installPassStatusKey,$installPassStatus);
}