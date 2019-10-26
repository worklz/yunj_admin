<?php
namespace app\admin\validate;

class Role extends Common {
    protected $rule = [
        'page' => 'require|isPositiveInteger',
        'limit' => 'require|isPositiveInteger',
        'name' => 'require|max:60|unique:role',
        'alias' => 'require|max:60|alpha|unique:role',
        'menus' => 'array',
        'id' => 'require|isPositiveInteger',
        'status' => 'require|in:0,1',
        'sort' => 'require|isPositiveInteger',
        'ids' => 'require|isPositiveIntegerStr',
        'title' => 'require',
    ];

    protected $message = [
        'pid.require' => '请选择父级菜单',
        'name.require' => '请输入名称',
        'name.unique' => '该名称已存在',
        'alias.require' => '请输入别名',
        'alias.unique' => '该别名已存在',
    ];

    protected $scene = [
        'search'=>['page','limit'],
        'add' => ['name','alias','menus'],
        'edit_query' => ['id'],
        'edit' => ['id','name','alias','menus'],
        'status'=>['id','status'],
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
            ->remove('name', 'unique')
            ->remove('alias', 'unique');
    }

    protected function handleData($_data, $scene) {
        $data = $_data;
        switch ($scene) {
            case 'add':
                $data['menu_ids']=implode(',',$data['menus']);
                break;
            case 'edit':
                $data['menu_ids']=implode(',',$data['menus']);
                break;
        }
        return $data;
    }
}