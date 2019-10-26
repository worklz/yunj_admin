<?php
namespace app\admin\controller;

use think\Db;
use think\facade\App;
use app\common\model\Menu as MenuModel;

class Index extends Common {

    protected function setIndexAssignData(){
        parent::setIndexAssignData();
        //获取当前用户所有显示的菜单信息
        $menu_ids=session('menu_ids');
        $fieldArr=['id','pid','name','code','icon'];
        $whereArr=[
            ['status','neq',0],
            ['is_show','neq',0],
        ];
        if(session('uid')!=1){
            $whereArr[]=['id','in',$menu_ids];
        }
        $menus=(new MenuModel())->getRows($fieldArr,$whereArr,['sort'=>'asc']);
        $this->indexAssignData['menus']=$menus;
    }

    public function welcome(){
        $mysql = Db::query('SELECT VERSION() AS ver');
        $this->assign([
            'current_time'=>date('Y-m-d H:i:s'),
            'version'=>config('system.version'),
            'site_domain'=>sconfig('base.site_domain'),
            'os'=>php_uname('s'),
            'runtime_environment'=>$_SERVER["SERVER_SOFTWARE"],
            'php_version'=>PHP_VERSION,
            'mysql_version'=>$mysql[0]['ver'],
            'tp_version'=>App::version(),
            'max_upload_size' => ini_get('upload_max_filesize')
        ]);
        return $this->fetch();
    }
}