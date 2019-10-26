<?php
namespace app\admin\validate;

class ConfigItem extends Common {
    protected $rule = [
        'page' => 'require|isPositiveInteger',
        'limit' => 'require|isPositiveInteger',
        'group_id' => 'require|isPositiveInteger',
        'name' => 'require|max:255',
        'code' => 'require|max:255|regex:[a-zA-Z][a-zA-Z0-9_]*',
        'value' => 'max:255',
        'tip' => 'max:500',
        'options' => 'max:500',
        'type_id' => 'require|isPositiveInteger',
        'id' => 'require|isPositiveInteger',
        'status' => 'require|in:0,1',
        'sort' => 'require|isPositiveInteger',
        'ids' => 'require|isPositiveIntegerStr',
        'title' => 'require',
    ];

    protected $message = [
        'name.require' => '请输入名称',
        'name.unique' => '该名称已存在',
        'code.require' => '请输入代码',
        'code.unique' => '该代码已存在',
        'code.regex' => '代码格式错误',
    ];

    protected $scene = [
        'search'=>['page','limit','group_id'],
        'add' => ['name','code','value','tips','options','group_id','type_id'],
        'edit_query' => ['id'],
        'edit' => ['id','name','code','value','tips','options','group_id','type_id','status'],
        'status'=>['id','status'],
        'sort'=>['id','sort'],
        'del_batch'=>['ids','title'],
    ];

    protected function handleData($_data, $scene) {
        $data = $_data;
        switch ($scene) {
            case 'add':
                $data=[
                    'name'=>$data['name'],
                    'code'=>$data['code'],
                    'value'=>$data['value'],
                    'tips'=>$data['tips'],
                    'options'=>$this->getOptions($data['options']),
                    'group_id'=>$data['group_id'],
                    'type_id'=>$data['type_id'],
                ];
                break;
            case 'edit':
                $data=[
                    'id'=>$data['id'],
                    'name'=>$data['name'],
                    'code'=>$data['code'],
                    'value'=>$data['value'],
                    'tips'=>$data['tips'],
                    'options'=>$this->getOptions($data['options']),
                    'group_id'=>$data['group_id'],
                    'type_id'=>$data['type_id'],
                    'status'=>$data['status'],
                ];
                break;
        }
        return $data;
    }

    private function getOptions($options){
        $optionsArr=[];
        if($options){
            $_options=explode(PHP_EOL,$options);
            foreach ($_options as $v){
                if(!$v)continue;
                list($key,$val)=explode(":",$v);
                $optionsArr[]=[
                    'key'=>$key,
                    'value'=>$val
                ];
            }
        }
        return $options?json_encode($optionsArr):$options;
    }
}