<?php
namespace app\admin\controller;

use think\Controller;

/**
 * 其他
 */
class Other extends Controller{

    /**
     * Description: 图标
     * Author: Uncle-L
     * Date: 2019/8/23
     * Time: 17:54
     * @return mixed
     */
    public function icon(){
        return $this->fetch();
    }
}