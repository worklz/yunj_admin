<?php
namespace app\install\controller;

use think\Controller;
use think\facade\Env;
use app\install\validate\Install as InstallValidate;
use app\install\service\DbInstall as DbInstallService;
use app\install\service\NowInstall as NowInstallService;
use app\install\service\RedisInstall as RedisInstallService;
use app\install\service\CheckEnvironment as CheckEnvironmentService;

class Index extends Controller {

    /**
     * Description: 开始安装 - 第一步
     * Author: Uncle-L
     * Date: 2019/9/30
     * Time: 10:28
     * @return mixed
     */
    public function index() {
        //安装初始化
        installInit();
        return $this->fetch();
    }

    /**
     * Description: 环境检测 - 第二步
     * Author: Uncle-L
     * Date: 2019/9/30
     * Time: 10:51
     */
    public function checkEnvironment() {
        $service = new CheckEnvironmentService();
        $data=$service->checkData();
        //设置当前步骤校验通过状态
        setInstallPassStatus('environment',$service->install_pass);
        dump($service->install_pass);
        dump(installPassStatus('environment'));
        $this->assign(['data'=>$data]);
        return $this->fetch();
    }

    /**
     * Description: 参数配置 - 第三步
     * Author: Uncle-L
     * Date: 2019/9/30
     * Time: 16:11
     */
    public function systemConfig(){
        $environment_pass=installPassStatus('environment');
        if(!$environment_pass){
            $url=url('errorView')."?msg=环境检测未通过，不能进行安装操作！&url=".urlencode(url('checkEnvironment'));
            $this->redirect($url);
        }
        //判断redis是否连接成功
        $redis_pass=installPassStatus('redis');
        $redisData=$redis_pass?config('session'):[];
        //判断数据库是否连接成功
        $db_pass=installPassStatus('db');
        $dbData=$db_pass?config('database'):[];
        $this->assign([
            'redis_data'=>$redisData,
            'db_data'=>$dbData
        ]);
        return $this->fetch();
    }

    /**
     * Description: redis连接 - 第四步
     * Author: Uncle-L
     * Date: 2019/10/11
     * Time: 16:47
     */
    public function redisInstall(){
        if(request()->isAjax()){
            $environment_pass=installPassStatus('environment');
            if(!$environment_pass){
                returnJson(['code'=>'99999','msg'=>'环境检测未通过，不能进行Redis连接操作！']);
            }
            $data=input('post.');
            $InstallValidate=new InstallValidate();
            $data=$InstallValidate->checkAll($data,'redis_install');
            //执行
            $service=new RedisInstallService();
            $service->execution($data);
        }
        returnJson(['code'=>'99999','msg'=>'非法访问！']);
    }

    /**
     * Description: 数据库安装 - 第五步
     * Author: Uncle-L
     * Date: 2019/10/11
     * Time: 16:47
     */
    public function dbInstall(){
        if(request()->isAjax()){
            $environment_pass=installPassStatus('environment');
            $redis_pass=installPassStatus('redis');
            if(!$environment_pass){
                returnJson(['code'=>'99999','msg'=>'环境检测未通过，不能进行数据库连接操作！']);
            }
            if(!$redis_pass){
                returnJson(['code'=>'99999','msg'=>'请先通过测试Redis连接！']);
            }
            $data=input('post.');
            $InstallValidate=new InstallValidate();
            $data=$InstallValidate->checkAll($data,'db_install');
            //执行
            $service=new DbInstallService();
            $service->execution($data);
        }
        returnJson(['code'=>'99999','msg'=>'非法访问！']);
    }

    /**
     * Description: 立即安装 - 第六步
     * Author: Uncle-L
     * Date: 2019/10/12
     * Time: 16:03
     */
    public function nowInstall(){
        if(request()->isAjax()){
            $environment_pass=installPassStatus('environment');
            $redis_pass=installPassStatus('redis');
            $db_pass=installPassStatus('db');
            if(!$environment_pass){
                returnJson(['code'=>'99999','msg'=>'环境检测未通过！']);
            }
            if(!$redis_pass){
                returnJson(['code'=>'99999','msg'=>'请先通过测试Redis连接！']);
            }
            if(!$db_pass){
                returnJson(['code'=>'99999','msg'=>'请先通过测试数据库连接！']);
            }
            $data=input('post.');
            $InstallValidate=new InstallValidate();
            $data=$InstallValidate->checkAll($data,'now_install');
            //执行
            $service=new NowInstallService();
            $service->execution($data);
        }
        returnJson(['code'=>'99999','msg'=>'非法访问！']);
    }

    /**
     * Description: 成功页面
     * Author: Uncle-L
     * Date: 2019/10/9
     * Time: 10:31
     * @return mixed
     */
    public function successView(){
        $install_lock_path=Env::get('root_path').'public/install.lock';
        if(!is_file($install_lock_path)){
            $url=url('errorView')."?msg=系统未安装，不能访问此页面！";
            $this->redirect($url);
        }
        return $this->fetch();
    }

    /**
     * Description: 错误页面
     * Author: Uncle-L
     * Date: 2019/10/11
     * Time: 15:15
     * @return mixed
     */
    public function errorView(){
        $data=input('get.');
        $error=[
            'msg'=>isset($data['msg'])?$data['msg']:'未知错误！',
            'url'=>isset($data['url'])?$data['url']:null,
        ];
        $this->assign('error',$error);
        return $this->fetch();
    }
}
