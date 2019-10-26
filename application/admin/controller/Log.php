<?php
namespace app\admin\controller;

use app\common\model\Log as LogModel;
use app\admin\validate\Log as LogValidate;

class Log extends Common {
    protected $title='系统日志';
    protected $nav=[
        'parents'=>[['url'=>'#','txt'=>'系统管理']],
        'current'=>['url'=>'#','txt'=>'系统日志']
    ];
    protected $sortFieldArr=['l.id'=>'desc'];
    protected $searchFieldArr=['l.ip','l.description','l.request_content','l.create_time','u.nickname'];
    protected $exportFieldNameArr=['记录时间','简述','操作人','IP地址','请求内容'];
    protected $exportFieldArr=['l.create_time','l.description','u.nickname','l.ip','l.request_content'];

    protected function initialize(){
        parent::initialize();
        $this->model=new LogModel();
        $this->validate=new LogValidate();
    }

    protected function getSearchCondition($data){
        $whereArr=[];
        if(isset($data['start_time'])&&$data['start_time']){
            $whereArr[]=['l.create_time','egt',strtotime($data['start_time'])];
        }
        if(isset($data['end_time'])&&$data['end_time']){
            $whereArr[]=['l.create_time','elt',strtotime($data['end_time'])];
        }
        if(isset($data['keywords'])&&$data['keywords']){
            $whereArr[]=['u.nickname|l.description','like','%'.$data['keywords'].'%'];
        }
        return $whereArr;
    }
}