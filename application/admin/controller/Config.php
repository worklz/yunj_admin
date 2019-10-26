<?php
namespace app\admin\controller;

use app\common\model\ConfigItem as ConfigItemModel;
use app\common\model\ConfigGroup as ConfigGroupModel;
use app\admin\validate\Config as ConfigValidate;

class Config extends Common {
    protected $title='系统配置';
    protected $nav=[
        'parents'=>[['url'=>'#','txt'=>'系统管理']],
        'current'=>['url'=>'#','txt'=>'系统配置']
    ];
    protected $sortFieldArr=['ci.sort'=>'asc'];
    protected $searchFieldArr=['ci.id','ci.name','ci.code','ci.value','ci.tips','ci.options','ct.type'];

    protected function initialize(){
        parent::initialize();
        $this->model=new ConfigItemModel();
        $this->validate=new ConfigValidate();
    }

    protected function setIndexAssignData(){
        parent::setIndexAssignData();
        $fieldArr=['id','name','code'];
        $whereArr=[];
        $ConfigGroupModel=new ConfigGroupModel();
        $groups=$ConfigGroupModel->getOwnRows($fieldArr,$whereArr,['sort'=>'asc']);
        $this->indexAssignData['groups']=$groups;
    }

    protected function getSearchCondition($data){
        $whereArr=[];
        if(isset($data['status'])&&in_array($data['status'],['0','1'],true)){
            $whereArr[]=['ci.status','eq',$data['status']];
        }
        if(isset($data['group_id'])&&$data['group_id']){
            $whereArr[]=['ci.group_id','eq',$data['group_id']];
        }
        return $whereArr;
    }

    protected function searchHandle(){
        $data=input('get.');
        $data=$this->validate->checkAll($data,'search');
        $whereArr=$this->getSearchCondition($data);
        $data=$this->model->getRows($this->searchFieldArr,$whereArr,$this->sortFieldArr);
        returnJson(['code'=>'00000', 'msg'=>'查询成功', 'data'=>$data]);
    }

    protected function editHandle($scene='edit',$title='配置'){
        $data=input('post.');
        $data=['data'=>$data];
        $data=$this->validate->checkAll($data,$scene);
        $res=$this->model->changeBatch($data);
        if($res){
            returnJson(['msg'=>"{$title}成功"]);
        }
        returnJson(['code'=>'99999','msg'=>"{$title}失败"]);
    }
}