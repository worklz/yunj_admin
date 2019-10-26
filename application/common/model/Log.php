<?php
namespace app\common\model;

class Log extends Common {
    public function getCount($whereArr=[]){
        $count=$this->alias('l')
            ->join('user u','u.id=l.uid')
            ->lock(true)
            ->where($whereArr)
            ->count();
        return $count;
    }

    public function getRows($fieldArr,$whereArr,$sortFields=['l.id'=>'desc'],$start=0,$length=0){
        $datas=$this->alias('l')->join('user u','u.id=l.uid')->lock(true)->field($fieldArr)->where($whereArr)->order($sortFields);
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