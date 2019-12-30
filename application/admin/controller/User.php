<?php
namespace app\admin\controller;

use app\common\model\User as UserModel;
use app\common\model\Role as RoleModel;
use app\admin\validate\User as UserValidate;

class User extends Common {
    protected $title='用户管理';
    protected $nav=[
        'parents'=>[['url'=>'#','txt'=>'系统管理']],
        'current'=>['url'=>'#','txt'=>'用户管理']
    ];
    protected $sortFieldArr=['u.id'=>'desc'];
    protected $searchFieldArr=['u.id','u.username','u.nickname','u.email','u.mobile','u.status','r.name as role_name'];
    protected $editQueryField='u.id';
    protected $editFieldArr=['u.id','u.username','u.nickname','u.email','u.mobile','u.role_id','u.status'];
    protected $exportFieldNameArr=['用户名','昵称','邮箱','电话','状态','角色'];
    protected $exportFieldArr=['u.username','u.nickname','u.email','u.mobile','u.status','r.name as role_name'];

    protected function initialize(){
        parent::initialize();
        $this->model=new UserModel();
        $this->validate=new UserValidate();
    }

    protected function getSearchCondition($data){
        $whereArr=[];
        //采用严格比较
        if(isset($data['status'])&&in_array($data['status'],['0','1'],true)){
            $whereArr[]=['u.status','eq',$data['status']];
        }
        if(isset($data['role_id'])&&$data['role_id']!='all'){
            $whereArr[]=['u.role_id','eq',$data['role_id']];
        }
        if(isset($data['keywords'])&&$data['keywords']){
            $whereArr[]=['u.username|u.nickname|u.email|u.mobile','like','%'.$data['keywords'].'%'];
        }
        return $whereArr;
    }

    protected function setIndexAssignData(){
        parent::setIndexAssignData();
        $fieldArr=['id','name'];
        $whereArr=[['status','neq',0]];
        $this->indexAssignData['roles']=(new RoleModel())->getRows($fieldArr,$whereArr,['sort'=>'asc']);
    }

    protected function setAddAssignData(){
        parent::setAddAssignData();
        $fieldArr=['id','name'];
        $whereArr=[['status','neq',0]];
        $this->addAssignData['roles']=(new RoleModel())->getRows($fieldArr,$whereArr,['sort'=>'asc']);
    }

    protected function setEditAssignData(){
        parent::setEditAssignData();
        $fieldArr=['id','name'];
        $whereArr=[['status','neq',0]];
        $this->editAssignData['roles']=(new RoleModel())->getRows($fieldArr,$whereArr,['sort'=>'asc']);
    }

    protected function setDelWhereArr($data){
        $whereArr=[
            ['id','neq',1],
            ['id','eq',$data['id']],
        ];
        return $whereArr;
    }

    protected function setDelBatchWhereArr($data){
        $whereArr=[
            ['id','neq',1],
            ['id','in',$data['ids']],
        ];
        return $whereArr;
    }

    /**
     * Description: 查询个人信息
     * Author: Uncle-L
     * Date: 2019/9/3
     * Time: 16:09
     * @return mixed
     */
    public function info(){
        $fieldArr=['u.username','u.nickname','u.email','u.mobile','u.create_time','u.update_time','r.name as role_name'];
        $whereArr=[
            ['u.status','eq',1],
            ['u.id','eq',getUserData('uid')]
        ];
        $info=$this->model->getRow($fieldArr,$whereArr);
        $this->assign('info',$info);
        return $this->fetch();
    }
}