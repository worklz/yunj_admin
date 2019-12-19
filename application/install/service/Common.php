<?php
namespace app\install\service;

use think\facade\Env;

class Common{
    protected $root_path;
    protected $app_path;
    protected $config_path;

    //安装是否通过
    public $install_pass;

    //当前步骤下安装是否通过的状态key
    protected $install_pass_key;

    public function __construct() {
        $this->root_path = Env::get('root_path');
        $this->app_path = Env::get('app_path');
        $this->config_path = Env::get('config_path');

        $this->install_pass = true;
    }

    /**
     * Description: 输出
     * Author: Uncle-L
     * Date: 2019/10/12
     * Time: 15:51
     * @param string $code
     * @param $msg
     * @param null $data
     */
    protected function output($code='00000',$msg,$data=null){
        if($code!='00000'){
            $this->install_pass = false;
        }
        setInstallPassStatus($this->install_pass_key,$this->install_pass);
        returnJson(['code'=>$code,'msg'=>$msg,'data'=>$data]);
    }
}