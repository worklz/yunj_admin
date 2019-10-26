<?php
namespace app\common\model;

use think\Model;

class Common extends Model{
    //开启自动写入时间戳
    protected $autoWriteTimestamp = true;
    //设置时间戳字段
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    /**
     * Description: 根据当前条件，获取数据条数
     * Author: Uncle-L
     * Date: 2019/8/18
     * Time: 11:29
     * @param array $whereArr
     * @return float|int|string
     */
    public function getCount($whereArr=[]){
        $count=$this->lock(true)->where($whereArr)->count();
        return $count;
    }

    /**
     * Description: 根据当前条件，获取单条数据信息
     * Author: Uncle-L
     * Date: 2019/7/15
     * Time: 0:18
     * @param array $fieldArr
     * @param array $whereArr
     * @return array|false|mixed|null|\PDOStatement|string|Model
     */
    public function getRow($fieldArr=[],$whereArr=[]){
        $data=$this->lock(true)->field($fieldArr)->where($whereArr)->find();
        return $data;
    }

    /**
     * Description: 根据当前条件，获取本表单条数据信息
     * Author: Uncle-L
     * Date: 2019/9/7
     * Time: 11:14
     * @param array $fieldArr
     * @param array $whereArr
     * @return array|false|null|\PDOStatement|string|Model
     */
    public function getOwnRow($fieldArr=[],$whereArr=[]){
        $data=$this->lock(true)->field($fieldArr)->where($whereArr)->find();
        return $data;
    }

    /**
     * Description: 根据当前条件，获取多条数据信息
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 10:38
     * @param $fieldArr
     * @param $whereArr
     * @param array $sortFields
     * @param int $start
     * @param int $length
     * @return $this|array|\PDOStatement|string|\think\Collection
     */
    public function getRows($fieldArr,$whereArr,$sortFields=['id'=>'desc'],$start=0,$length=0){
        $datas=$this->lock(true)->field($fieldArr)->where($whereArr)->order($sortFields);
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

    /**
     * Description: 根据当前条件，获取本表多条数据信息
     * Author: Uncle-L
     * Date: 2019/9/7
     * Time: 11:15
     * @param $fieldArr
     * @param $whereArr
     * @param array $sortFields
     * @param int $start
     * @param int $length
     * @return $this|array|false|\PDOStatement|string|\think\Collection
     */
    public function getOwnRows($fieldArr,$whereArr,$sortFields=['id'=>'desc'],$start=0,$length=0){
        $datas=$this->lock(true)->field($fieldArr)->where($whereArr)->order($sortFields);
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

    /**
     * Description: 新增单条数据，若为自增逐渐ID，则返回结果id；反之返回影响行数
     * Author: Uncle-L
     * Date: 2019/8/18
     * Time: 11:35
     * @param $data
     * @param bool $isIncID [是否自增ID]
     * @return int|string
     */
    public function addRow($data,$isIncID=true){
        if(!array_key_exists('create_time',$data)){
            $data['create_time']=time();
        }
        if(!array_key_exists('update_time',$data)){
            $data['update_time']=time();
        }
        if($isIncID){
            $res=$this->strict(false)->insertGetId($data);
        }else{
            $res=$this->strict(false)->insert($data);
        }
        if($res) $this->addRowAfter();
        return $res;
    }

    /**
     * Description: 新增一条数据后执行
     * Author: Uncle-L
     * Date: 2019/9/12
     * Time: 16:44
     */
    protected function addRowAfter(){}

    /**
     * Description: 新增多条数据，并返回成功条数
     * Author: Uncle-L
     * Date: 2019/8/18
     * Time: 11:38
     * @param $data
     * @return int|string
     */
    public function addRows($data){
        foreach ($data as $k=>$v){
            if(!array_key_exists('create_time',$v)){
                $data[$k]['create_time']=time();
            }
            if(!array_key_exists('update_time',$v)){
                $data[$k]['update_time']=time();
            }
        }
        $res=$this->strict(false)->insertAll($data);
        if($res) $this->addRowsAfter();
        return $res;
    }

    /**
     * Description: 新增多条数据后执行
     * Author: Uncle-L
     * Date: 2019/9/12
     * Time: 16:44
     */
    protected function addRowsAfter(){}

    /**
     * Description: 根据条件，软删除条件数据
     * Author: Uncle-L
     * Date: 2019/8/18
     * Time: 11:38
     * @param array $whereArr
     * @return int|string
     */
    public function del($whereArr=[]){
        $res=$this->strict(false)->where($whereArr)->update(['update_time'=>time(),'status'=>0]);
        if($res) $this->delAfter();
        return $res;
    }

    /**
     * Description: 删除数据后执行
     * Author: Uncle-L
     * Date: 2019/9/12
     * Time: 16:44
     */
    protected function delAfter(){}

    /**
     * Description: 根据条件，更新数据
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 16:55
     * @param $data
     * @param array $whereArr
     * @return int|string|static
     */
    public function change($data,$whereArr=[]){
        if(!array_key_exists('update_time',$data)){
            $data['update_time']=time();
        }
        if($whereArr){
            $res=$this->strict(false)->where($whereArr)->update($data);
        }else{
            $res=$this->strict(false)->update($data);
        }
        if($res) $this->changeAfter();
        return $res;
    }

    /**
     * Description: 更新数据后执行
     * Author: Uncle-L
     * Date: 2019/9/12
     * Time: 16:44
     */
    protected function changeAfter(){}

    /**
     * Description: 批量更新数据
     * Author: Uncle-L
     * @param $data
     * @return \think\Collection
     * @throws \Exception
     */
    public function changeBatch($data){
        foreach ($data as $k=>$v){
            if(!array_key_exists('update_time',$v)){
                $data[$k]['update_time']=time();
            }
        }
        $res=$this->allowField(true)->saveAll($data);
        if($res) $this->changeBatchAfter();
        return $res;
    }

    /**
     * Description: 批量更新数据后执行
     * Author: Uncle-L
     * Date: 2019/9/12
     * Time: 16:44
     */
    protected function changeBatchAfter(){}
}