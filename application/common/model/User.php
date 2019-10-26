<?php
namespace app\common\model;

class User extends Common {
    public function getCount($whereArr=[]){
        $count=$this->alias('u')
            ->join('role r','u.role_id=r.id')
            ->lock(true)
            ->where($whereArr)
            ->count();
        return $count;
    }

    public function getRow($fieldArr=[],$whereArr=[]){
        $data=$this->alias('u')
            ->join('role r','u.role_id=r.id')
            ->lock(true)->field($fieldArr)->where($whereArr)->find();
        return $data;
    }

    public function getRows($fieldArr,$whereArr,$sortFields=['u.id'=>'desc'],$start=0,$length=0){
        $datas=$this->alias('u')->join('role r','u.role_id=r.id')->lock(true)->field($fieldArr)->where($whereArr)->order($sortFields);
        if($length==0){
            $datas=$datas->select();
        }else{
            $datas=$datas->limit($start,$length)->select();
        }
        if($datas!=null){
            $datas=$datas->toArray();
        }
        return $datas;
    }
}