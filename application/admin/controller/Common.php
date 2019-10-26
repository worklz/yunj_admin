<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\User as UserModel;
use app\admin\validate\FileUpload as FileUploadValidate;

class Common extends Controller {
    //页面title
    protected $title='';
    //页面导航条
    protected $nav=[];
    //模型
    protected $model;
    //验证器
    protected $validate;
    //排序数组
    protected $sortFieldArr=['id'=>'desc'];
    //搜索/查询字段
    protected $searchFieldArr=[];
    //每页显示条数
    protected $pageSize=20;
    //获取编辑数据的字段
    protected $editQueryField='id';
    //编辑字段
    protected $editFieldArr=[];
    //导出字段名称
    protected $exportFieldNameArr=[];
    //导出字段
    protected $exportFieldArr=[];
    //控制器首页（列表）渲染数据
    protected $indexAssignData=[];
    //添加渲染数据
    protected $addAssignData=[];
    //编辑渲染数据
    protected $editAssignData=[];
    //额外渲染数据
    protected $extraAssignData=[];

    /**
     * Description: 初始化
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 17:38
     */
    protected function initialize(){
        //入口校验
        Entrance::check();
        //额外数据输出
        $this->assign([
            'title'=>$this->title,
            'nav'=>$this->nav,
            'extra'=>$this->extraAssignData,
        ]);
    }

    /**
     * Description: 控制器首页渲染数据设置
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 17:54
     */
    protected function setIndexAssignData(){
        $this->indexAssignData=[];
    }

    /**
     * Description: 控制器首页（列表）
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 17:54
     * @return mixed
     */
    public function index(){
        $this->setIndexAssignData();
        $this->assign($this->indexAssignData);
        return $this->fetch();
    }

    /**
     * Description: 获取查询/搜索条件
     * Author: Uncle-L
     * Date: 2019/8/21
     * Time: 16:11
     * @param $data
     * @return array
     */
    protected function getSearchCondition($data){
        return $data;
    }

    /**
     * Description: 查询/搜索
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 17:57
     */
    public function search(){
        if($this->request->isAjax()){
            $this->searchHandle();
        }
    }

    /**
     * Description: 查询/搜索的处理
     * Author: Uncle-L
     * Date: 2019/8/21
     * Time: 16:11
     */
    protected function searchHandle(){
        $res=['msg'=>'查询成功','data'=>['count'=>0, 'items'=>[]]];
        $data=input('param.');
        //验证数据有效性
        $data=$this->validate->checkAll($data,'search');
        //查询条件
        $whereArr=$this->getSearchCondition($data);
        $count=$this->model->getCount($whereArr);
        $start=($data['page']-1)*$data['limit'];
        $length=$data['limit'];
        $items=$this->model->getRows($this->searchFieldArr,$whereArr,$this->sortFieldArr,$start,$length);
        $res['data']['count']=$count;
        $res['data']['items']=$items;
        returnJson($res);
    }

    /**
     * Description: 添加渲染数据设置
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 18:00
     */
    protected function setAddAssignData(){
        $this->addAssignData=[];
    }

    /**
     * Description: 添加
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 17:59
     * @return mixed
     */
    public function add(){
        if ($this->request->isPost()) {
            $this->addHandle();
        }
        $this->setAddAssignData();
        $this->assign($this->addAssignData);
        return $this->fetch();
    }

    /**
     * Description: 添加操作的处理
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 18:00
     */
    protected function addHandle(){
        //获取数据
        $data=input('post.');
        //验证数据有效性
        $data=$this->validate->checkAll($data,'add');
        //数据添加
        $res=$this->model->addRow($data);
        //返回结果
        if($res){
            returnJson(['msg'=>'添加成功']);
        }
        returnJson(['code'=>'99999','msg'=>'添加失败']);
    }

    /**
     * Description: 编辑渲染数据设置
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 18:02
     */
    protected function setEditAssignData(){
        $this->editAssignData=[];
        $data=input('get.');
        $data=$this->validate->checkAll($data,'edit_query');
        $key=$this->editQueryField;
        if(strstr($this->editQueryField,".")){
            $key=substr($this->editQueryField,strpos($this->editQueryField,'.')+1);
        }
        $whereArr=[[$this->editQueryField,'eq',$data[$key]]];
        $info=$this->model->getRow($this->editFieldArr,$whereArr);
        $this->editAssignData['info']=$info;
    }

    /**
     * Description: 编辑
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 18:01
     * @return mixed
     */
    public function edit(){
        if ($this->request->isPost()) {
            $this->editHandle();
        }
        $this->setEditAssignData();
        $this->assign($this->editAssignData);
        return $this->fetch();
    }

    /**
     * Description: 编辑操作的处理
     * Author: Uncle-L
     * Date: 2019/8/20
     * Time: 11:06
     * @param string $scene [验证场景]
     * @param string $title [提示语]
     */
    protected function editHandle($scene='edit',$title='编辑'){
        //获取数据
        $data=input('post.');
        //验证数据有效性
        $data=$this->validate->checkAll($data,$scene);
        //数据编辑
        $res=$this->model->change($data);
        //返回结果
        if($res){
            returnJson(['msg'=>"{$title}成功"]);
        }
        returnJson(['code'=>'99999','msg'=>"{$title}失败"]);
    }

    /**
     * Description: 根据提交值设置删除条件
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 18:04
     * @param $data
     * @return array
     */
    protected function setDelWhereArr($data){
        $whereArr=[['id','eq',$data['id']]];
        return $whereArr;
    }

    /**
     * Description: 删除/禁用/失效...
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 18:04
     */
    public function del(){
        if ($this->request->isPost()) {
            $this->delHandle();
        }
    }

    /**
     * Description: 删除操作的处理
     * Author: Uncle-L
     * Date: 2019/8/16
     * Time: 18:05
     */
    protected function delHandle(){
        //获取数据
        $data=input('post.');
        //验证数据有效性
        $data=$this->validate->checkAll($data,'del');
        //数据修改
        $whereArr=$this->setDelWhereArr($data);
        $res=$this->model->del($whereArr);
        //返回结果
        if($res){
            returnJson(['msg'=>"{$data['title']}成功"]);
        }
        returnJson(['code'=>'99999','msg'=>"{$data['title']}失败"]);
    }

    /**
     * Description: 根据提交值设置批量删除条件
     * Author: Uncle-L
     * Date: 2019/8/20
     * Time: 23:50
     * @param $data
     * @return array
     */
    protected function setDelBatchWhereArr($data){
        $whereArr=[['id','in',$data['ids']]];
        return $whereArr;
    }

    /**
     * Description: 批量删除
     * Author: Uncle-L
     * Date: 2019/8/20
     * Time: 23:50
     */
    public function delBatch(){
        if($this->request->isAjax()){
            $this->delBatchHandle();
        }
    }

    /**
     * Description: 批量删除处理
     * Author: Uncle-L
     * Date: 2019/8/20
     * Time: 23:50
     */
    protected function delBatchHandle(){
        //获取数据
        $data=input('post.');
        //验证数据有效性
        $data=$this->validate->checkAll($data,'del_batch');
        //数据修改
        $whereArr=$this->setDelBatchWhereArr($data);
        $res=$this->model->del($whereArr);
        //返回结果
        if($res){
            returnJson(['msg'=>"{$data['title']}成功"]);
        }
        returnJson(['code'=>'99999','msg'=>"{$data['title']}失败"]);
    }

    /**
     * Description: 修改状态
     * Author: Uncle-L
     * Date: 2019/8/19
     * Time: 17:43
     */
    public function status(){
        if($this->request->isAjax()){
            $this->editHandle('status','更改状态');
        }
    }

    /**
     * Description: 排序
     * Author: Uncle-L
     * Date: 2019/8/20
     * Time: 9:22
     */
    public function sort(){
        if($this->request->isAjax()){
            $this->editHandle('sort','更改排序');
        }
    }

    /**
     * Description: 文件上传
     * Author: Uncle-L
     * Date: 2019/9/11
     * Time: 9:48
     */
    public function fileUpload(){
        if($this->request->isAjax()){
            $this->fileUploadHandle();
        }
    }

    /**
     * Description: 文件上传处理
     * Author: Uncle-L
     * Date: 2019/9/11
     * Time: 9:54
     */
    protected function fileUploadHandle(){
        //获取文件
        $file = request()->file('file');
        $FileUploadValidate=new FileUploadValidate();
        //返回的data包括：文件类型type、文件路径file_path、文件上传路径upload_file_path、文件本身file
        $data=$FileUploadValidate->checkAll(['file'=>$file],'upload');
        //上传
        $uploadFile = $data['file']->move($data['upload_file_path']);
        if (!is_file($data['upload_file_path'].$uploadFile->getSaveName())) {
            returnJson(['code'=>'99999','msg'=>'文件上传失败！'.$uploadFile->getError()]);
        }
        returnJson([
            'msg'=>'上传成功',
            'data'=>[
                'type'=>$data['type'],
                'src'=>$data['file_path'].str_replace("\\","/",$uploadFile->getSaveName())
            ]
        ]);
    }

    /**
     * Description: 导出数据
     * Author: Uncle-L
     * Date: 2019/9/18
     * Time: 9:19
     */
    public function export(){
        $this->exportHandle();
    }

    /**
     * Description: 导出数据处理。小于1000条，直接输出csv；大于1000条，每个csv处理1000条，压缩下载
     * Author: Uncle-L
     * Date: 2019/9/18
     * Time: 9:19
     */
    protected function exportHandle(){
        $data=input('get.');
        $data=$this->validate->checkAll($data,'export');
        $title=$data['title'];
        $whereArr=$this->getSearchCondition($data);
        //总量
        $count=$this->model->getCount($whereArr);
        $time=date('YmdHis');
        //导出文件数据量限制
        $export_file_data_number_limit=sconfig('base.export_file_data_number_limit');
        if($count<$export_file_data_number_limit){
            $fileName="{$title}_{$time}";
            $rows=$this->model->getRows($this->exportFieldArr,$whereArr,$this->sortFieldArr,0,$count);
            array_unshift($rows, $this->exportFieldNameArr);
            exportCsv($rows,$fileName);
        }else{
            $args=[
                'title'=>$title,
                'count'=>$count,
                'model'=>$this->model,
                'export_field_name_arr'=>$this->exportFieldNameArr,
                'export_field_arr'=>$this->exportFieldArr,
                'where_arr'=>$whereArr,
                'sort_field_arr'=>$this->sortFieldArr
            ];
            exportZipByCsv($args);
        }
    }
}