<?php
namespace app\install\service;

class CheckEnvironment extends Common{

    public function __construct() {
        parent::__construct();
        $this->install_pass_key='environment';
    }

    public function checkData() {
        $data = [
            'env' => [
                'title' => '运行环境',
                'cols' => ['环境名称', '所需配置', '当前配置'],
                'items' => $this->checkEnv()
            ],
            'func' => [
                'title' => '函数/扩展',
                'cols' => ['函数/扩展', '类型', '结果'],
                'items' => $this->checkFunc()
            ],
            'dir' => [
                'title' => '目录/文件',
                'cols' => ['目录/文件', '所需权限', '当前权限'],
                'items' => $this->checkDir()
            ]
        ];
        return $data;
    }

    /**
     * Description: 检测运行环境
     * Author: Uncle-L
     * Date: 2019/9/30
     * Time: 14:31
     * @return array
     */
    private function checkEnv() {
        $items = [
            'os' => [
                'title' => '操作系统',
                'demand' => '类Unix',
                'current' => null,
                'pass' => true
            ],
            'php' => [
                'title' => 'PHP版本',
                'demand' => '5.6及以上',
                'current' => null,
                'pass' => true
            ],
            'gd' => [
                'title' => 'GD库',
                'demand' => '2.0及以上',
                'current' => null,
                'pass' => true
            ]
        ];
        //os
        $items['os']['current'] = PHP_OS;
        //php
        $items['php']['current'] = PHP_VERSION;
        if ($items['php']['current'] < 5.6) {
            $items['php']['pass'] = false;
            $this->install_pass = false;
        }
        //gd
        $tmp = function_exists('gd_info') ? gd_info() : [];
        if (empty($tmp['GD Version'])) {
            $items['gd']['current'] = '未安装';
            $items['gd']['pass'] = false;
            $this->install_pass = false;
        } else {
            $items['gd']['current'] = $tmp['GD Version'];
        }
        return $items;
    }

    /**
     * Description: 函数扩展
     * Author: Uncle-L
     * Date: 2019/9/30
     * Time: 14:31
     * @return array
     */
    private function checkFunc() {
        $items = [
            'pdo'=>[
                'title'=>'pdo',
                'demand' => '类',
                'current' => null,
                'pass' => true
            ],
            'pdo_mysql'=>[
                'title'=>'pdo_mysql',
                'demand' => '模块',
                'current' => null,
                'pass' => true
            ],
            'zip'=>[
                'title'=>'zip',
                'demand' => '模块',
                'current' => null,
                'pass' => true
            ],
            'fileinfo'=>[
                'title'=>'fileinfo',
                'demand' => '模块',
                'current' => null,
                'pass' => true
            ],
            'curl'=>[
                'title'=>'curl',
                'demand' => '模块',
                'current' => null,
                'pass' => true
            ],
            'redis'=>[
                'title'=>'redis',
                'demand' => '模块',
                'current' => null,
                'pass' => true
            ],
            'xml'=>[
                'title'=>'xml',
                'demand' => '函数',
                'current' => null,
                'pass' => true
            ],
            'file_put_contents'=>[
                'title'=>'file_put_contents',
                'demand' => '函数',
                'current' => null,
                'pass' => true
            ],
            'mb_strlen'=>[
                'title'=>'mb_strlen',
                'demand' => '函数',
                'current' => null,
                'pass' => true
            ],
            'gzopen'=>[
                'title'=>'gzopen',
                'demand' => '函数',
                'current' => null,
                'pass' => true
            ],
        ];
        foreach ($items as &$v) {
            if(('类'==$v['demand'] && !class_exists($v['title']))
                ||('模块'==$v['demand'] && !extension_loaded($v['title']))
                ||('函数'==$v['demand'] && !function_exists($v['title']))){
                $v['current']='不支持';
                $v['pass']=false;
                $this->install_pass=false;
            }else{
                $v['current']='支持';
            }
        }

        return $items;
    }

    /**
     * Description: 目录文件权限
     * Author: Uncle-L
     * Date: 2019/9/30
     * Time: 14:30
     * @return array
     */
    private function checkDir() {
        $items = [
            'config'=>[
                'title'=>'config',
                'type'=>'dir',
                'path'=>$this->root_path.'config',
                'demand' => '读/写',
                'current' => '读/写',
                'pass' => true
            ],
            'public'=>[
                'title'=>'public',
                'type'=>'dir',
                'path'=>$this->root_path.'public',
                'demand' => '读/写',
                'current' => '读/写',
                'pass' => true
            ],
            'runtime'=>[
                'title'=>'runtime',
                'type'=>'dir',
                'path'=>$this->root_path.'runtime',
                'demand' => '读/写',
                'current' => '读/写',
                'pass' => true
            ]
        ];

        foreach ($items as &$v) {
            if ($v['type'] == 'dir') {
                // 文件夹
                if(!is_writable($v['path'])) {
                    if(is_dir($v['path'])) {
                        $v['current'] = '不可写';
                    } else {
                        $v['current'] = '不存在';
                    }
                    $v['pass']=false;
                    $this->install_pass=false;
                }
            } else {
                // 文件
                if(!is_writable($v['path'])) {
                    $v['current'] = '不可写';
                    $v['pass']=false;
                    $this->install_pass=false;
                }
            }
        }
        return $items;
    }
}