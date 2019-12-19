<?php
namespace app\install\service;

use think\Db;

class DbInstall extends Common {

    public function __construct() {
        parent::__construct();
        $this->install_pass_key='db';
    }

    /**
     * Description: 执行数据库安装
     * Author: Uncle-L
     * Date: 2019/10/11
     * Time: 17:52
     */
    public function execution($data){
        $cover = $data['cover'];
        unset($data['cover']);

        //判断提交数据是否正确
        $dbConfig = include $this->root_path.'config/database.php';
        foreach ($data as $k => $v) {
            if (array_key_exists($k, $dbConfig) === false) {
                $this->output('99999','参数'.$k.'不存在！');
            }
        }

        // 不存在的数据库会导致连接失败
        $database = $data['database'];
        unset($data['database']);

        // 创建数据库连接
        $dbConnect = Db::connect($data);

        // 检测数据库连接
        try{
            $dbConnect->execute('select version()');
        }catch(\Exception $e){
            $this->output('99999','数据库连接失败，请检查数据库配置！');
        }

        // 不覆盖，检测是否已存在数据库
        if (!$cover) {
            $check = $dbConnect->execute("SELECT * FROM information_schema.schemata WHERE schema_name='{$database}'");
            if ($check) {
                $this->output('99999','该数据库已存在，如需覆盖请选择覆盖数据库！');
            }
        }

        // 创建数据库
        if (!$dbConnect->execute("CREATE DATABASE IF NOT EXISTS `{$database}` DEFAULT CHARACTER SET utf8")) {
            $this->output('99999',$dbConnect->getError());
        }
        $data['database'] = $database;

        // 生成数据库配置文件
        $this->generateDbConfigFile($data);

        $this->output('00000','数据库连接成功！');
    }

    /**
     * Description: 生成数据库配置文件
     * Author: Uncle-L
     * Date: 2019/10/11
     * Time: 18:08
     * @param $data
     */
    private function generateDbConfigFile($data){
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

return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '{$data['hostname']}',
    // 数据库名
    'database'        => '{$data['database']}',
    // 用户名
    'username'        => '{$data['username']}',
    // 密码
    'password'        => '{$data['password']}',
    // 端口
    'hostport'        => '{$data['hostport']}',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '{$data['prefix']}',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 自动读取主库数据
    'read_master'     => false,
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Builder类
    'builder'         => '',
    // Query类
    'query'           => '\\think\\db\\Query',
    // 是否需要断线重连
    'break_reconnect' => false,
    // 断线标识字符串
    'break_match_str' => [],
];

INFO;
        file_put_contents($this->config_path.'database.php', $code);
        // 判断写入是否成功
        $dbConfig = include $this->config_path.'database.php';
        if (empty($dbConfig['database']) || $dbConfig['database'] != $data['database']) {
            $this->output('99999','[config/database.php]数据库配置写入失败！！');
        }
    }
}