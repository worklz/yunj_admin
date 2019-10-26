<?php
namespace app\common\model;

class ConfigItem extends Common {
    //更新配置缓存
    protected function addRowAfter() {
        parent::addRowAfter();
        sconfig('',null,true);
    }
    protected function addRowsAfter() {
        parent::addRowsAfter();
        sconfig('',null,true);
    }
    protected function delAfter() {
        parent::delAfter();
        sconfig('',null,true);
    }
    protected function changeAfter() {
        parent::changeAfter();
        sconfig('',null,true);
    }
    protected function changeBatchAfter() {
        parent::changeBatchAfter();
        sconfig('',null,true);
    }

    public function getCount($whereArr=[]){
        $count=$this->alias('ci')
            ->join('config_type ct','ci.type_id=ct.id')
            ->lock(true)
            ->where($whereArr)
            ->count();
        return $count;
    }

    public function getRows($fieldArr,$whereArr,$sortFields=['ci.sort'=>'asc'],$start=0,$length=0){
        $datas=$this->alias('ci')->join('config_type ct','ci.type_id=ct.id')->lock(true)->field($fieldArr)->where($whereArr)->order($sortFields);
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

    //连接配置项表+配置组表+配置类型表
    public function getRowsLinkGroupType($fieldArr,$whereArr){
        $datas=$this->alias('ci')
            ->join('config_group cg','ci.group_id=cg.id')
            ->join('config_type ct','ci.type_id=ct.id')
            ->lock(true)
            ->field($fieldArr)
            ->where($whereArr)
            ->order(['cg.sort'=>'asc','ci.sort'=>'asc'])
            ->select();
        return $datas;
    }
}