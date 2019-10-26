<?php
namespace app\admin\validate;

use think\facade\Env;

class FileUpload extends Common {
    protected $rule = [
        'file' => 'require',
    ];

    protected $message = [
        'name.require' => '未找到上传的文件(文件大小可能超过php.ini限制)！',
    ];

    protected $scene = [
        'upload'=>['file'],
    ];

    protected function handleData($_data, $scene) {
        $data = $_data;
        switch ($scene) {
            case 'upload':
                $data=$this->handleDataByUpload($data);
                break;
        }
        return $data;
    }

    /**
     * Description: 处理上传数据
     * Author: Uncle
     * Date: 2019/9/25
     * Time: 11:52
     * @param $_data
     * @return array
     */
    private function handleDataByUpload($_data){
        $file=$_data['file'];
        if ($file->getMime() == 'text/x-php' || $file->getMime() == 'text/html'){
            returnJson(['code'=>'99999','msg'=>'禁止上传php,html文件！']);
        }
        //格式、大小校验
        if ($file->checkExt(sconfig('upload.image_ext'))) {
            $type = 'image';
            if (sconfig('upload.image_size') > 0 && !$file->checkSize(sconfig('upload.image_size')*1024)) {
                returnJson(['code'=>'99999','msg'=>'上传的图片大小超过系统限制['.sconfig('upload.image_size').']KB！']);
            }
        } else {
            returnJson(['code'=>'99999','msg'=>'非系统允许的上传格式！']);
        }
        //文件路径
        $filePath = "/upload/{$type}/";
        //文件上传路径
        $uploadFilePath=Env::get('root_path').'public'.$filePath;
        //返回数据
        $data=[
            'type'=>$type,
            'file_path'=>$filePath,
            'upload_file_path'=>$uploadFilePath,
            'file'=>$file
        ];
        return $data;
    }
}