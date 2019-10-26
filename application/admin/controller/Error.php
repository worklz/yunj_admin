<?php
namespace app\admin\controller;

use think\Controller;
use enum\Error as ErrorEnum;

/**
 * 处理异常
 */
class Error extends Controller{
    /**
     * Description: 根据enum值输出显示对应错误页面或者错误的json
     * Author: Uncle-L
     * Date: 2019/8/22
     * Time: 12:09
     * @return mixed
     */
    public function index(){
        $enum=input('enum')?input('enum'):'NO_AUTH';
        $info=ErrorEnum::get($enum);
        //判断是否ajax请求
        if(request()->isPost()||request()->isAjax()){
            returnJson(['code'=>'99999','msg'=>$info['msg']]);
        }
        $this->assign('info',$info);
        return $this->fetch();
    }
}