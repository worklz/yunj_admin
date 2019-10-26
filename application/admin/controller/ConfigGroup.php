<?php
namespace app\admin\controller;

use app\common\model\ConfigGroup as ConfigGroupModel;
use app\admin\validate\ConfigGroup as ConfigGroupValidate;

class ConfigGroup extends Common {
    protected $title='配置组管理';
    protected $nav=[
        'parents'=>[['url'=>'#','txt'=>'系统管理'],['url'=>'#','txt'=>'系统配置']],
        'current'=>['url'=>'#','txt'=>'配置组管理']
    ];
    protected $sortFieldArr=['sort'=>'asc'];
    protected $searchFieldArr=['id','name','code','sort','status'];
    protected $editFieldArr=['id','name','code','status'];

    protected function initialize(){
        parent::initialize();
        $this->model=new ConfigGroupModel();
        $this->validate=new ConfigGroupValidate();
    }

    protected function getSearchCondition($data){
        $whereArr=[];
        //采用严格比较
        if(isset($data['status'])&&in_array($data['status'],['0','1'],true)){
            $whereArr[]=['status','eq',$data['status']];
        }
        if(isset($data['keywords'])&&$data['keywords']){
            $whereArr[]=['name|code','like','%'.$data['keywords'].'%'];
        }
        return $whereArr;
    }
}