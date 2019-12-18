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
            $sqlList = parseSql($sql, 0, ['yunj_' => $dbConfig['prefix']]);
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

        //生成session会话配置文件
        $this->generateSessionConfigFile($data);

        //生成cache配置文件
        $this->generateCacheConfigFile($data);

        file_put_contents($this->root_path.'public/install.lock', "如需重新安装，请手动删除此文件\n安装时间：".date('Y-m-d H:i:s'));
        returnJson(['msg'=>'安装成功','data'=>['url'=>url('Index/successView')]]);
    }

    /**
     * 生成session会话配置文件
     * Author: Uncle-L
     * Date: 2019/12/18
     * Time: 16:46
     * @param $data
     */
    private function generateSessionConfigFile($data){
        session(null);
        $code = <<<INFO
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

return [
    'type'       => 'redis',
    'prefix'     => 'module',
    'auto_start' => true,
     // redis主机
    'host'       => '{$data['redis_host']}',
     // redis端口
    'port'       => {$data['redis_port']},
     // 密码
    'password'   => '{$data['redis_password']}',
];
INFO;
        file_put_contents($this->config_path.'session.php', $code);
        // 判断写入是否成功
        $sessionConfig = include $this->config_path.'session.php';
        if (empty($sessionConfig['type']) || $sessionConfig['type'] != 'redis') {
            returnJson(['code'=>'99999','msg'=>'[config/session.php]会话配置写入失败！']);
        }
    }

    /**
     * 生成cache缓存配置文件
     * Author: Uncle-L
     * Date: 2019/12/18
     * Time: 16:46
     * @param $data
     */
    private function generateCacheConfigFile($data){
        $code = <<<INFO
<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 缓存配置为复合类型
    'type'  =>  'complex', 
    'default'	=>	[
      'type'	=>	'file',
      // 全局缓存有效期（0为永久有效）
      'expire'=>  0, 
      // 缓存前缀
      'prefix'=>  'yunj',
       // 缓存目录
      'path'  =>  '../runtime/cache/',
    ],
    'redis'	=>	[
      'type'	=>	'redis',
      'host'	=>	'{$data['redis_host']}',
      // 全局缓存有效期（0为永久有效）
      'expire'=>  0, 
      // 缓存前缀
      'prefix'=>  'yunj',
    ],    
    // 添加更多的缓存类型设置
];
INFO;
        file_put_contents($this->config_path.'cache.php', $code);
        // 判断写入是否成功
        $cacheConfig = include $this->config_path.'cache.php';
        if (empty($cacheConfig['type']) || $cacheConfig['type'] != 'redis') {
            returnJson(['code'=>'99999','msg'=>'[config/cache.php]缓存配置写入失败！']);
        }
    }
}