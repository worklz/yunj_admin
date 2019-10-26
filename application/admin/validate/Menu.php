<?php
namespace app\admin\validate;

use app\admin\service\Menu as MenuService;

class Menu extends Common {
    protected $rule = [
        'pid' => 'require|isPositiveIntegerOrZero',
        'name' => 'require|max:50',
        'code' => 'require|max:50|unique:menu',
        'is_show' => 'require|in:0,1',
        'id' => 'require|isPositiveInteger',
        'status' => 'require|in:0,1',
        'sort' => 'require|isPositiveInteger',
        'ids' => 'require|isPositiveIntegerStr',
        'title' => 'require',
    ];

    protected $message = [
        'pid.require' => '请选择父级菜单',
        'name.require' => '请输入名称',
        'code.require' => '请输入控制器/方法',
        'code.unique' => '该控制器/方法已存在',
    ];

    protected $scene = [
        'search'=>[],
        'add' => ['pid','name','code','is_show'],
        'edit_query' => ['id'],
        'edit' => ['id','pid','name','code','is_show','status'],
        'status'=>['id','status'],
        'show'=>['id','is_show'],
        'sort'=>['id','sort'],
        'del_batch'=>['ids','title'],
    ];

    /**
     * Description: 移除编辑场景下name、code的唯一性验证
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 16:53
     * @return $this
     */
    public function sceneEdit() {
        return $this->only($this->scene['edit'])
            ->remove('code', 'unique');
    }

    protected function handleData($_data, $scene) {
        $data = $_data;
        $MenuService=new MenuService('Menu');
        switch ($scene) {
            case 'add':
                $data['icon']=$data['icon']?$data['icon']:'layui-icon layui-icon-app';
                $data['level']=$MenuService->getMenuLevel($data['pid']);
                break;
            case 'edit':
                $data['icon']=$data['icon']?$data['icon']:'layui-icon layui-icon-app';
                $data['level']=$MenuService->getMenuLevel($data['pid']);
                break;
        }
        return $data;
    }
}