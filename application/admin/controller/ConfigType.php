<?php
namespace app\admin\controller;

use app\common\model\ConfigType as ConfigTypeModel;
use app\admin\validate\ConfigType as ConfigTypeValidate;

class ConfigType extends Common {
    protected $title='配置类型管理';
    protected $nav=[
        'parents'=>[['url'=>'#','txt'=>'系统管理'],['url'=>'#','txt'=>'系统配置']],
        'current'=>['url'=>'#','txt'=>'配置类型管理']
    ];
    protected $sortFieldArr=['sort'=>'asc'];
    protected $searchFieldArr=['id','name','type','sort','status'];
    protected $editFieldArr=['id','name','type','status'];

    protected function initialize(){
        parent::initialize();
        $this->model=new ConfigTypeModel();
        $this->validate=new ConfigTypeValidate();
    }

    protected function getSearchCondition($data){
        $whereArr=[];
        //采用严格比较
        if(isset($data['status'])&&in_array($data['status'],['0','1'],true)){
            $whereArr[]=['status','eq',$data['status']];
        }
        if(isset($data['keywords'])&&$data['keywords']){
            $whereArr[]=['name|type','like','%'.$data['keywords'].'%'];
        }
        return $whereArr;
    }
}