<?php
namespace app\admin\validate;

class Config extends Common {
    protected $rule = [
        'group_id' => 'require|isPositiveInteger',
        'status' => 'require|in:0,1',
        'data' => 'require|array',
    ];

    protected $message = [

    ];

    protected $scene = [
        'search'=>['status','group_id'],
        'edit'=>['data'],
    ];

    protected function handleData($_data, $scene) {
        $data = $_data;
        switch ($scene) {
            case 'edit':
                $data=$data['data'];
                if(isset($data['file']))unset($data['file']);
                $data=$this->handleDataByEdit($data);
                break;
        }
        return $data;
    }

    private function handleDataByEdit($_data){
        $data=[];
        $time=time();
        foreach ($_data as $k=>$v){
            list($id,$type)=explode("_",$k);
            if($type=="checkbox"){
                $value=implode(',',$v);
            }else{
                $value=$v;
            }
            $data[]=['id'=>$id,'value'=>$value,'update_time'=>$time];
        }
        return $data;
    }
}