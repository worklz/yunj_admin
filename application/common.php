<?php
// +----------------------------------------------------------------------
// | 公共方法
// +----------------------------------------------------------------------

/**
 * Description: 返回json数据
 * Author: Uncle-L
 * Date: 2019/8/18
 * Time: 11:48
 * @param array $args [code=00000表示正常]
 */
function returnJson($args=[]){
    $result=[
        'code'=>isset($args['code'])?$args['code']:'00000',
        'msg'=>isset($args['msg'])?$args['msg']:'处理成功',
        'data'=>isset($args['data'])?$args['data']:null,
    ];
    //记录日志
    systemLog($result['msg']);
    $response = \think\Response::create($result, 'json');
    throw new \think\exception\HttpResponseException($response);
}

/**
 * Description: 获取指定长度的随机字符串
 * Author: Uncle-L
 * Date: 2019/8/22
 * Time: 11:05
 * @param int $length [默认6]
 * @return string
 */
function randomStr($length=6){
    $characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //因为字符串的顺序是从0开始的，下面的mt_rand()从0开始取，所以最后一个字符串要取到就只能减一
    $characters_length=strlen($characters)-1;
    $str='';
    for($i=$length;$i>0;$i--){
        $str.=$characters[mt_rand(0,$characters_length)];
    }
    return $str;
}

/**
 * Description: 获取客户端ip
 * Author: Uncle-L
 * Date: 2019/8/27
 * Time: 18:19
 * @return array|false|string
 */
function getIP(){
    if(isset($_SERVER)){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $realIP=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else if(isset($_SERVER['HTTP_CLIENT_IP'])){
            $realIP=$_SERVER['HTTP_CLIENT_IP'];
        }else{
            $realIP=$_SERVER['REMOTE_ADDR'];
        }
    }else{
        if(getenv('HTTP_X_FORWARDED_FOR')){
            $realIP=getenv('HTTP_X_FORWARDED_FOR');
        }else if(getenv('HTTP_CLIENT_IP')){
            $realIP=getenv('HTTP_CLIENT_IP');
        }else{
            $realIP=getenv('REMOTE_ADDR');
        }
    }
    return $realIP;
}

/**
 * Description: 获取/更新系统配置
 * Author: Uncle-L
 * Date: 2019/10/12
 * Time: 17:52
 * @param string $code [配置代码]
 * @param null $default [默认值]
 * @param bool $update [是否刷新配置缓存]
 * @return array|bool|\Illuminate\Cache\CacheManager|mixed|null
 */
function sconfig($code='',$default=null,$update=false){
    $result = cache('system_config');
    if ($result === false || $update == true) {
        $fieldArr=['cg.code as group_code','ci.code as item_code','ci.value','ct.type'];
        $whereArr=[['cg.status','eq',1], ['ci.status','eq',1], ['ct.status','eq',1]];
        $configs = (new \app\common\model\ConfigItem())->getRowsLinkGroupType($fieldArr,$whereArr);
        $result = [];
        foreach ($configs as $config) {
            switch ($config['type']) {
                case 'checkbox':
                    $result[$config['group_code']][$config['item_code']] = explode(',',$config['value']);
                    break;
                case 'array':
                    $result[$config['group_code']][$config['item_code']] = explode(',',$config['value']);
                    break;
                default:
                    $result[$config['group_code']][$config['item_code']] = $config['value'];
                    break;
            }
        }
        cache('system_config', $result);
    }
    if($code != ''){
        $res=null;
        if(strstr($code,'.')){
            list($group_code,$item_code)=explode('.',$code);
            if(isset($result[$group_code])){
                if(isset($result[$group_code][$item_code])){
                    $res=$result[$group_code][$item_code];
                }
            }
        }elseif(isset($result[$code])){
            $res=$result[$code];
        }
        if(!$res&&$default){
            $res=$default;
        }
    }else{
        $res=$default;
    }
    return $res;
}

/**
 * Description: 直接导出csv
 * Author: Uncle-L
 * Date: 2019/9/18
 * Time: 11:31
 * @param $rows [标题+数据的二维数组]
 * @param string $fileName [文件名]
 */
function exportCsv($rows,$fileName=''){
    if($fileName){
        $fileName=substr($fileName,-4)!='.csv'?$fileName.".csv":$fileName;
    }else{
        $fileName=date("YmdHis").".csv";
    }

    header('Content-Type: application/vnd.ms-excel');//设置内容类型为Excel
    header('Content-Disposition: attachment;filename='.$fileName );//下载文件
    header('Cache-Control: max-age=0');//表示当访问此网页后的0秒内再次访问不会去服务器
    $file = fopen('php://output',"a");//打开文件或者URL,php://output 是一个只写的数据流，允许你以 print 和 echo 一样的方式 写入到输出缓冲区,a:写入方式打开，将文件指针指向文件末尾。如果文件不存在则尝试创建之。

    $limit = 1000;
    $calc = 0;
    foreach ($rows as $v){
        $calc++;
        //核心！！！清空缓存，将缓存上的数据写入到文件
        if($limit == $calc){
            ob_flush();//将本来存在输出缓存中的内容取出来，调用ob_flush()之后缓冲区内容将被丢弃。
            flush();   //待输出的内容立即发送。具体查看：https://www.jb51.net/article/37822.htm
            $calc = 0;
        }
        //核心
        foreach($v as $t){
            $tarr[] = iconv('UTF-8', 'GB2312//IGNORE',$t);//转码
        }
        fputcsv($file,$tarr);//将行格式化为 CSV 并写入一个打开的文件中。（内容）
        unset($tarr);//销毁指定的变量
    }
    unset($rows);//销毁指定的变量
    fclose($file);//关闭打开的文件
    exit();
}

/**
 * Description: 导出csv的zip文件
 * Author: Uncle-L
 * Date: 2019/9/18
 * Time: 11:59
 */
function exportZipByCsv($args){
    if(!isset($args['title'])||
        !isset($args['count'])||
        !isset($args['model'])||
        !isset($args['export_field_name_arr'])||
        !isset($args['export_field_arr'])||
        !isset($args['where_arr'])||
        !isset($args['sort_field_arr'])){
        returnJson(['code'=>'99999','msg'=>'参数格式错误']);
    }
    $time=date('YmdHis');
    $zipFileName="{$args['title']}_{$time}.zip";
    $csvFileNameArr=[];
    //导出文件数据量限制
    $export_file_data_number_limit=sconfig('base.export_file_data_number_limit');
    //循环读取次数
    $forCount=ceil($args['count']/$export_file_data_number_limit);
    for($i=1;$i<=$forCount;$i++){
        $csvFileName="{$args['title']}_{$time}_{$i}.csv";
        $file = fopen($csvFileName, 'w'); //生成临时文件
        $csvFileNameArr[]=$csvFileName;
        $start=($i-1)*$export_file_data_number_limit;
        $rows=$args['model']->getRows($args['export_field_arr'],$args['where_arr'],$args['sort_field_arr'],$start,$export_file_data_number_limit);
        array_unshift($rows, $args['export_field_name_arr']);

        //写入csv
        $limit = $export_file_data_number_limit;
        $calc = 0;
        foreach ($rows as $v){
            $calc++;
            if($limit == $calc){
                ob_flush();
                flush();
                $calc = 0;
            }
            //核心
            $tarr=[];
            foreach($v as $t){
                $tarr[] = iconv('UTF-8', 'GB2312//IGNORE',$t);//转码
            }
            fputcsv($file,$tarr);//将行格式化为 CSV 并写入一个打开的文件中。（内容）
            unset($tarr);//销毁指定的变量
        }
        unset($rows);
        fclose($file);
    }
    //进行多个文件压缩
    $zip = new \ZipArchive();
    $zip->open($zipFileName, $zip::CREATE);   //打开压缩包
    foreach ($csvFileNameArr as $file) {
        $zip->addFile($file, basename($file));   //向压缩包中添加文件
    }
    $zip->close();  //关闭压缩包
    foreach ($csvFileNameArr as $file) {
        unlink($file); //删除csv临时文件
    }
    //输出压缩文件提供下载
    header("Cache-Control: max-age=0");
    header("Content-Description: File Transfer");
    header('Content-disposition: attachment; filename=' . basename($zipFileName)); // 文件名
    header("Content-Type: application/zip"); // zip格式的
    header("Content-Transfer-Encoding: binary");
    header('Content-Length: ' . filesize($zipFileName));
    @readfile($zipFileName);//输出文件;
    unlink($zipFileName); //删除压缩包临时文件
    exit();
}

/**
 * Description: 生成登录签名，防止session劫持
 * Author: Uncle-L
 * Date: 2019/9/27
 * Time: 17:53
 * @param array $data [id、username、login_pwd、login_salt、create_time]
 * @return string
 */
function generateLoginSign($data = []) {
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data);
    $code = http_build_query($data);
    $sign = sha1($code);
    return $sign;
}

/**
 * 解析sql语句
 * @param  string $content sql内容
 * @param  int $limit  如果为1，则只返回一条sql语句，默认返回所有
 * @param  array $prefix 替换表前缀
 * @return array|string 除去注释之后的sql语句数组或一条语句
 */

/**
 * Description: 解析sql语句
 * Author: Uncle-L
 * Date: 2019/10/12
 * Time: 16:24
 * @param string $sql [sql内容]
 * @param int $limit [如果为1，则只返回一条sql语句，默认返回所有]
 * @param array $prefix [替换表前缀]
 * @return array|string [除去注释之后的sql语句数组或一条语句]
 */
function parseSql($sql = '', $limit = 0, $prefix = []) {
    // 被替换的前缀
    $from = '';
    // 要替换的前缀
    $to = '';

    // 替换表前缀
    if (!empty($prefix)) {
        $to   = current($prefix);
        $from = current(array_flip($prefix));
    }

    if ($sql != '') {
        // 纯sql内容
        $pure_sql = [];

        // 多行注释标记
        $comment = false;

        // 按行分割，兼容多个平台
        $sql = str_replace(["\r\n", "\r"], "\n", $sql);
        $sql = explode("\n", trim($sql));

        // 循环处理每一行
        foreach ($sql as $key => $line) {
            // 跳过空行
            if ($line == '') continue;

            // 跳过以#或者--开头的单行注释
            if (preg_match("/^(#|--)/", $line)) continue;

            // 跳过以/**/包裹起来的单行注释
            if (preg_match("/^\/\*(.*?)\*\//", $line)) continue;

            // 多行注释开始
            if (substr($line, 0, 2) == '/*') {
                $comment = true;
                continue;
            }

            // 多行注释结束
            if (substr($line, -2) == '*/') {
                $comment = false;
                continue;
            }

            // 多行注释没有结束，继续跳过
            if ($comment) continue;

            // 替换表前缀
            if ($from != '') {
                $line = str_replace('`'.$from, '`'.$to, $line);
            }
            if ($line == 'BEGIN;' || $line =='COMMIT;') continue;
            // sql语句
            array_push($pure_sql, $line);
        }

        // 只返回一条语句
        if ($limit == 1) {
            return implode($pure_sql, "");
        }

        // 以数组形式返回sql语句
        $pure_sql = implode($pure_sql, "\n");
        $pure_sql = explode(";\n", $pure_sql);
        return $pure_sql;
    } else {
        return $limit == 1 ? '' : [];
    }
}

/**
 * Description: 是否移动端
 * Author: Uncle-L
 * Date: 2019/11/6
 * Time: 14:47
 * @return bool
 */
function isMobile() {
    $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
    $mobile_browser = '0';
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
        $mobile_browser++;
    if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        $mobile_browser++;
    if (isset($_SERVER['HTTP_PROFILE']))
        $mobile_browser++;
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
        'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
        'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
        'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
        'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
        'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
        'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
        'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
        'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
    );
    if (in_array($mobile_ua, $mobile_agents))
        $mobile_browser++;
    if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
        $mobile_browser++;
    // win
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
        $mobile_browser = 0;
    // win7
    if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
        $mobile_browser++;
    if ($mobile_browser > 0) {
        return true;
    }
    return false;
}

/**
 * Description: 是否微信浏览器
 * Author: Uncle-L
 * Date: 2019/11/6
 * Time: 14:47
 * @return bool
 */
function isWechatBrowser(){
    $res=isset($_SERVER['HTTP_USER_AGENT'])&& (strripos($_SERVER['HTTP_USER_AGENT'],'micromessenger')!=false||strripos($_SERVER['HTTP_USER_AGENT'],'mqqbrowser')!=false);
    return $res;
}

/**
 * Description: 是否微信小程序
 * Author: Uncle-L
 * Date: 2019/11/6
 * Time: 14:47
 * @return bool
 */
function isWechatApplet(){
    $res=isset($_SERVER['HTTP_USER_AGENT'])&&strripos($_SERVER['HTTP_USER_AGENT'],'miniprogram')!=false;
    return $res;
}