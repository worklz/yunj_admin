<?php
namespace app\admin\controller;

use app\common\model\ConfigItem as ConfigItemModel;
use app\admin\validate\ConfigItem as ConfigItemValidate;
use app\common\model\ConfigGroup as ConfigGroupModel;
use app\common\model\ConfigType as ConfigTypeModel;

class ConfigItem extends Common {
    protected $title='配置项列表';
    protected $nav=[
        'parents'=>[['url'=>'#','txt'=>'系统管理'],['url'=>'#','txt'=>'系统配置'],['url'=>'#','txt'=>'配置组管理']],
        'current'=>['url'=>'#','txt'=>'配置项列表']
    ];
    protected $sortFieldArr=['ci.sort'=>'asc'];
    protected $searchFieldArr=['ci.id','ci.name','ci.code','ci.sort','ci.status','ct.name as type_name'];
    protected $editFieldArr=['id','name','code','value','tips','options','group_id','type_id','status'];

    protected function initialize(){
        parent::initialize();
        $this->model=new ConfigItemModel();
        $this->validate=new ConfigItemValidate();
    }

    protected function getSearchCondition($data){
        $whereArr=[];
        //采用严格比较
        if(isset($data['status'])&&in_array($data['status'],['0','1'],true)){
            $whereArr[]=['ci.status','eq',$data['status']];
        }
        if(isset($data['group_id'])&&$data['group_id']){
            $whereArr[]=['ci.group_id','eq',$data['group_id']];
        }
        if(isset($data['keywords'])&&$data['keywords']){
            $whereArr[]=['ci.name|ci.code','like','%'.$data['keywords'].'%'];
        }
        return $whereArr;
    }

    protected function setIndexAssignData(){
        parent::setIndexAssignData();
        $group_id=input("get.group_id/d");
        $fieldArr=['id','name'];
        $whereArr=[['status','eq',1], ['id','eq',$group_id]];
        $ConfigGroupModel=new ConfigGroupModel();
        $group=$ConfigGroupModel->getOwnRow($fieldArr,$whereArr);
        if(!$group) $this->redirect('Error/index',['enum'=>'DEFAULT_404']);
        $this->indexAssignData['title']="配置项列表-{$group['name']}";
        $this->indexAssignData['nav']=[
            'parents'=>[['url'=>'#','txt'=>'系统管理'],['url'=>'#','txt'=>'系统配置'],['url'=>'#','txt'=>'配置组管理']],
            'current'=>['url'=>'#','txt'=>"配置项列表-{$group['name']}"]
        ];
        $this->indexAssignData['group_id']=$group['id'];
    }

    protected function setAddAssignData(){
        parent::setAddAssignData();
        $group_id=input("get.group_id/d");
        $fieldArr=['id','name'];
        $whereArr=[['status','eq',1]];
        $ConfigGroupModel=new ConfigGroupModel();
        $groups=$ConfigGroupModel->getOwnRows($fieldArr,$whereArr,['sort'=>'asc']);
        $ConfigTypeModel=new ConfigTypeModel();
        $fieldArr=['id','name','type'];
        $types=$ConfigTypeModel->getOwnRows($fieldArr,$whereArr,['sort'=>'asc']);
        $this->addAssignData['groups']=$groups;
        $this->addAssignData['types']=$types;
        $this->addAssignData['group_id']=$group_id;
    }

    protected function setEditAssignData(){
        parent::setEditAssignData();
        $options=json_decode($this->editAssignData['info']['options'],true);
        if($options){
            $options_str='';
            foreach ($options as $v){
                $options_str.=$v['key'].":".$v['value']."\r\n";
            }
            $options=$options_str;
        }
        $this->editAssignData['info']['options']=$options;
        $fieldArr=['id','name'];
        $whereArr=[['status','eq',1]];
        $ConfigGroupModel=new ConfigGroupModel();
        $groups=$ConfigGroupModel->getOwnRows($fieldArr,$whereArr,['sort'=>'asc']);
        $ConfigTypeModel=new ConfigTypeModel();
        $fieldArr=['id','name','type'];
        $types=$ConfigTypeModel->getOwnRows($fieldArr,$whereArr,['sort'=>'asc']);
        $this->editAssignData['groups']=$groups;
        $this->editAssignData['types']=$types;
    }
}