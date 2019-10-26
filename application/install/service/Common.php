<?php
namespace app\install\service;

use think\facade\Env;

class Common{
    protected $root_path;
    protected $app_path;
    protected $config_path;

    public function __construct() {
        $this->root_path = Env::get('root_path');
        $this->app_path = Env::get('app_path');
        $this->config_path = Env::get('config_path');
    }
}