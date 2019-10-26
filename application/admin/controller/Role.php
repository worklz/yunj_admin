<?php
namespace app\admin\controller;

use app\common\model\Role as RoleModel;
use app\admin\validate\Role as RoleValidate;
use app\admin\service\Menu as MenuService;

class Role extends Common {
    protected $title='角色管理';
    protected $nav=[
        'parents'=>[['url'=>'#','txt'=>'系统管理']],
        'current'=>['url'=>'#','txt'=>'角色管理']
    ];
    protected $sortFieldArr=['sort'=>'asc'];
    protected $searchFieldArr=['id','name','alias','description','sort','status'];
    protected $editFieldArr=['id','name','alias','description','menu_ids','status'];

    protected function initialize(){
        parent::initialize();
        $this->model=new RoleModel();
        $this->validate=new RoleValidate();
    }

    protected function getSearchCondition($data){
        $whereArr=[];
        //采用严格比较
        if(isset($data['status'])&&in_array($data['status'],['0','1'],true)){
            $whereArr[]=['status','eq',$data['status']];
        }
        if(isset($data['keywords'])&&$data['keywords']){
            $whereArr[]=['name|alias','like','%'.$data['keywords'].'%'];
        }
        return $whereArr;
    }

    protected function setAddAssignData(){
        parent::setAddAssignData();
        $this->addAssignData['menus']=(new MenuService('Menu',false))->getMenuTree();
    }

    protected function setEditAssignData(){
        parent::setEditAssignData();
        $this->editAssignData['menus']=(new MenuService('Menu',false))->getMenuTree();
    }
}