<?php
namespace app\install\service;

class RedisInstall extends Common {

    public function __construct() {
        parent::__construct();
        $this->install_pass_key='redis';
    }

    /**
     * Description: 执行Redis链接
     * Author: Uncle-L
     * Date: 2019/10/11
     * Time: 17:52
     * @param $data
     */
    public function execution($data){
        //校验能否连接
        $redis = new \Redis();
        $redis->connect($data['redis_host'], $data['redis_port'], 5);
        $redis->auth($data['redis_password']);
        if(!$redis){
            $this->output('99999','会话配置写入失败！');
        }

        //生成session会话配置文件
        $this->generateSessionConfigFile($data);

        //生成cache配置文件
        $this->generateCacheConfigFile($data);

        $this->output('00000','Redis连接成功！');
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
            $this->output('99999','[config/session.php]会话配置写入失败！');
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
            $this->output('99999','[config/cache.php]缓存配置写入失败！');
        }
    }
}