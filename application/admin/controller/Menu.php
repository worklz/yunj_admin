<?php
namespace app\admin\controller;

use app\common\model\Menu as MenuModel;
use app\admin\validate\Menu as MenuValidate;
use app\admin\service\Menu as MenuService;

class Menu extends Common {
    protected $title='菜单管理';
    protected $nav=[
        'parents'=>[['url'=>'#','txt'=>'系统管理']],
        'current'=>['url'=>'#','txt'=>'菜单管理']
    ];
    protected $editFieldArr=['id','pid','name','code','icon','is_show','status'];

    protected function initialize(){
        parent::initialize();
        $this->model=new MenuModel();
        $this->validate=new MenuValidate();
    }

    protected function setAddAssignData(){
        parent::setAddAssignData();
        $this->addAssignData['menu_id']=input('get.menu_id/d'); //强制转换为整形
        $this->addAssignData['menus']=(new MenuService('Menu'))->getMenuTree();
    }

    protected function setEditAssignData(){
        parent::setEditAssignData();
        $this->editAssignData['menus']=(new MenuService('Menu'))->getMenuTree();
    }

    public function search(){
        $menus=(new MenuService('Menu'))->getMenuTree();
        $res=[
            'msg'=>'查询成功',
            'data'=>[
                'count'=>100,
                'items'=>$menus
            ]
        ];
        returnJson($res);
    }


    /**
     * Description: 修改是否显示
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 17:43
     */
    public function show(){
        if($this->request->isAjax()){
            $this->editHandle('show','更改显示状态');
        }
    }
}