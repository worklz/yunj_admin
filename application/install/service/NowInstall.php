<?php
namespace app\install\service;

use think\Db;
use app\common\model\User as UserModel;

class NowInstall extends Common {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Description: 执行立即安装
     * Author: Uncle-L
     * Date: 2019/10/11
     * Time: 17:52
     */
    public function execution($data){
        $dbConfig=include $this->root_path.'config/database.php';

        // 导入系统初始数据库结构
        $installSqlFilePath = $this->app_path.'install/sql/install.sql';
        if (file_exists($installSqlFilePath)) {
            $sql = file_get_contents($installSqlFilePath);
            $sqlList = parseSql($sql, 0, ['ss_' => $dbConfig['prefix']]);
            if ($sqlList) {
                $sqlList = array_filter($sqlList);
                foreach ($sqlList as $v) {
                    try {
                        Db::execute($v);
                    } catch(\Exception $e) {
                        returnJson(['code'=>'99999','msg'=>'请先测试数据库连接！']);
                    }
                }
            }
        }

        // 注册管理员账号
        $UserModel=new UserModel();
        $login_salt=randomStr();
        $login_pwd=md5(md5($data['admin_password']).md5($login_salt));
        $addData=[
            'username'=>$data['admin_username'],
            'nickname'=>'超级管理员',
            'login_pwd'=>$login_pwd,
            'login_salt'=>$login_salt,
            'email'=>'待填',
            'mobile'=>'待填',
            'role_id'=>'1',
            'last_login_ip'=>getIP(),
            'last_login_time'=>time()
        ];
        $res = $UserModel->addRow($addData);
        if (!$res) {
            returnJson(['code'=>'99999','msg'=>$UserModel->getError() ? $UserModel->getError() : '管理员账号设置失败！']);
        }
        file_put_contents($this->root_path.'public/install.lock', "如需重新安装，请手动删除此文件\n安装时间：".date('Y-m-d H:i:s'));
        returnJson(['msg'=>'安装成功','data'=>['url'=>url('Index/successView')]]);
    }
}