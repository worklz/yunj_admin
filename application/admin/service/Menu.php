<?php
namespace app\admin\service;

class Menu {
    //菜单模型
    private $model;
    //是否是菜单列表页
    private $isMenuPage;

    public function __construct($modelName='Menu',$isMenuPage=true) {
        $this->model=model($modelName);
        $this->isMenuPage=$isMenuPage;
    }

    /**
     * Description: 获取菜单树
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 10:19
     * @param $menus [菜单]
     * @param $pid
     * @return array
     */
    public function getMenuTree($menus=[],$pid=0){
        //定义一个静态数组存储菜单树
        static $arr=[];
        if(!$menus){
            $fieldArr=['id','pid','name','code','icon','level','sort','is_show','create_time','status'];
            $whereArr=$this->isMenuPage?[]:[['status','neq',0]];
            $menus=$this->model->getRows($fieldArr,$whereArr,['sort'=>'asc']);
        }
        foreach ($menus as $k=>$v){
            if($v['pid']==$pid){
                //不是菜单列表页，需要获取上级菜单id的字符串，用于复选框的联动
                if(!$this->isMenuPage) $v['dataid']=$this->getParentId($v['id']);
                //将该一维数组添加到静态数组里面去
                $arr[]=$v;
                $this->getMenuTree($menus,$v['id']);
            }
        }
        return $arr;
    }


    /**
     * Description: 得到上级菜单id
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 10:20
     * @param $menuID
     * @return string
     */
    public function getParentId($menuID){
        $menus=$this->model->field(["id","pid"])->select();
        return $this->_getParentId($menus,$menuID,true);
    }
    //递归的思想
    public function _getParentId($menus,$menuID,$clear=false){
        static $arr = array();
        if($clear){
            $arr=array();   //清空数组
        }
        foreach ($menus as $k => $v) {
            if ($v['id'] == $menuID) {
                $arr[] = $v['id'];
                $this->_getParentId($menus, $v['pid'],false);
            }
        }
        //数组排序
        asort($arr);
        //将数组组合成1-2-3的字符串
        $arrStr=implode('-',$arr);
        return $arrStr;
    }


    /**
     * Description: 根据菜单pid，获取当前菜单的level
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 14:11
     * @param $pid
     * @return int
     */
    public function getMenuLevel($pid){
        $level=0;
        if($pid!='0'){
            //获取上级权限level
            $pLevel=$this->model->field(["level"])->find($pid);
            $level=$pLevel['level']+1;
        }
        return $level;
    }
}