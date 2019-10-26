<?php
namespace app\install\validate;

use think\Validate;

class Common extends Validate {

    /**
     * Description: 批量验证所有参数
     * Author: Uncle-L
     * Date: 2019/8/18
     * Time: 22:52
     * @param $data
     * @param $scene
     * @return mixed
     */
    public function checkAll($data, $scene) {
        if (!$this->scene($scene)->batch()->check($data)) {
            $msg = is_array($this->error) ? implode(';', $this->error) : $this->error;
            returnJson(['code'=>'99999','msg'=>$msg]);
        }
        return $this->handleData($data, $scene);
    }

    /**
     * Description: 对验证参数的处理并返回
     * Author: Uncle-L
     * Date: 2019/8/18
     * Time: 22:53
     * @param $data
     * @param $scene
     * @return mixed
     */
    protected function handleData($data, $scene) {
        return $data;
    }

    /**
     * Description: 正整数验证
     * Author: Uncle-L
     * Date: 2019/8/18
     * Time: 22:53
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    protected function isPositiveInteger($value, $rule = '', $data = '', $field = '') {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Description: 大等于0的正整数验证
     * Author: Uncle-L
     * Date: 2019/8/18
     * Time: 23:27
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    protected function isPositiveIntegerOrZero($value, $rule = '', $data = '', $field = '') {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) >= 0) {
            return true;
        }
        return false;
    }

    /**
     * Description: 必须是用 , 隔开的字符串
     * Author: Uncle-L
     * Date: 2019/8/20
     * Time: 23:52
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool
     */
    protected function isPositiveIntegerStr($value, $rule = '', $data = '', $field = '') {
        $arr = explode(',', $value);
        foreach ($arr as $v) {
            $res = $this->isPositiveInteger($v);
            if (!$res) {
                return false;
            }
        }
        return true;
    }
}