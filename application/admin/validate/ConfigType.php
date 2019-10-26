<?php
namespace app\admin\validate;

class ConfigType extends Common {
    protected $rule = [
        'page' => 'require|isPositiveInteger',
        'limit' => 'require|isPositiveInteger',
        'name' => 'require|max:255|unique:config_type',
        'type' => 'require|max:255|alpha|unique:config_type',
        'id' => 'require|isPositiveInteger',
        'status' => 'require|in:0,1',
        'sort' => 'require|isPositiveInteger',
        'ids' => 'require|isPositiveIntegerStr',
        'title' => 'require',
    ];

    protected $message = [
        'name.require' => '请输入名称',
        'name.unique' => '该名称已存在',
        'type.require' => '请输入代码',
        'type.unique' => '该代码已存在',
    ];

    protected $scene = [
        'search'=>['page','limit'],
        'add' => ['name','type'],
        'edit_query' => ['id'],
        'edit' => ['id','name','type','status'],
        'status'=>['id','status'],
        'sort'=>['id','sort'],
        'del_batch'=>['ids','title'],
    ];

    /**
     * Description: 移除编辑场景下name、type的唯一性验证
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 16:53
     * @return $this
     */
    public function sceneEdit() {
        return $this->only($this->scene['edit'])
            ->remove('name', 'unique')
            ->remove('type', 'unique');
    }
}