<?php
namespace enum;

/**
 * 日志类型枚举
 */
class Log{
    const ATTRIBUTE=[
        'OTHER'=>[
            'type'=>0,
            'name'=>'其他',
        ],
        'INSERT'=>[
            'type'=>1,
            'name'=>'增',
        ],
        'DELETE'=>[
            'type'=>2,
            'name'=>'删',
        ],
        'UPDATE'=>[
            'type'=>3,
            'name'=>'改',
        ],
        'SELECT'=>[
            'type'=>4,
            'name'=>'查',
        ],
    ];

    /**
     * Description: 根据传入参数，获取对应信息
     * Author: Uncle-L
     * Date: 2019/8/27
     * Time: 17:44
     * @param null $param {key|type|name，key返回key对应的数组]
     * @param bool $returnArr [是否返回完整键值对]
     * @return array|mixed|null
     */
    public static function get($param=null,$returnArr=false){
        if($param===null) return self::ATTRIBUTE;
        if(array_key_exists($param,self::ATTRIBUTE)) return self::ATTRIBUTE[$param];
        foreach (self::ATTRIBUTE as $v){
            if($v['type']==$param){
                $res=$returnArr?$v:$v['name'];
                break;
            }
            if($v['name']==$param){
                $res=$returnArr?$v:$v['type'];
                break;
            }
        }
        return isset($res)?$res:null;
    }
}