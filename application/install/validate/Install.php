<?php
namespace app\install\validate;

class Install extends Common {

    protected $rule = [
        'redis_host|Redis主机' => 'require',
        'redis_port|Redis端口' => 'require|number',
        'hostname|服务器地址' => 'require',
        'hostport|服务器端口' => 'require|number',
        'database|数据库名称' => 'require|regex:[0-9a-zA-Z_]{1,}',
        'username|数据库账号' => 'require',
        'password|数据库密码' => 'require',
        'prefix|数据库前缀' => 'require|regex:^[a-z0-9]{1,20}[_]{1}',
        'cover|覆盖数据库' => 'require|in:0,1',
        'admin_username|管理员账号' => 'require|min:4|max:20',
        'admin_password|管理员密码' => 'require|min:6|max:20',
    ];

    protected $message = [
        'redis_host.require'=>'不能为空',
        'redis_port.require'=>'不能为空',
        'redis_port.number'=>'只能是数字',
        'hostname.require'=>'不能为空',
        'hostport.require'=>'不能为空',
        'hostport.number'=>'只能是数字',
        'database.require'=>'不能为空',
        'database.regex'=>'格式错误',
        'username.require'=>'不能为空',
        'password.require'=>'不能为空',
        'prefix.require'=>'不能为空',
        'prefix.regex'=>'格式错误',
        'cover.in'=>'选择错误',
        'admin_username.require'=>'不能为空',
        'admin_username.min'=>'格式错误',
        'admin_password.require'=>'不能为空',
        'admin_password.min'=>'格式错误',
    ];

    protected $scene = [
        'redis_install'=>['redis_host','redis_port','redis_password'],
        'db_install'=>['hostname','hostport','database','username','password','prefix','cover'],
        'now_install'=>['admin_username','admin_password']
    ];

    /**
     * Description: 处理返回数据
     * Author: Uncle-L
     * Date: 2019/10/14
     * Time: 14:58
     * @param $_data
     * @param $scene
     * @return mixed
     */
    protected function handleData($_data, $scene) {
        $data=$_data;
        switch ($scene){
            case 'db_install':
                $data['type']='mysql';
                break;
        }
        return $data;
    }
}