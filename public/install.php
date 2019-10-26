<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

//判断是否已安装，防止重复安装
if(is_file('./install.lock')){
    //判断是否访问成功页面
    if(!strstr($_SERVER['REQUEST_URI'],'/install.php/index/successView')&&!strstr($_SERVER['REQUEST_URI'],'/install.php/index/successview')){
        exit('您已安装本系统，重复安装会将数据库覆盖！！！');
    }
}

Container::get('app')->bind('install')->run()->send();
