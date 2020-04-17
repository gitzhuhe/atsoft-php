<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/3/20
 * Time: 15:13
 */

namespace AtSoft\SingPHP\validate\build;

use AtSoft\SingPHP\Context\RequestContext;
use AtSoft\SingPHP\Exception\FormException;
use AtSoft\SingPHP\validate\Validate;
use Closure;

/**
 *
 * 表单验证

 */
class Base extends VaAction
{
    //扩展验证规则
    protected $validate = [];
    //错误信息
    protected $error = [];

    public function make($validate, $data)
    {
        $this->error = "";
        if(!$validate[3]){
            $validate[3] = [];
        }
        $activte = RequestContext::getActvite();
        if(!in_array($activte,$validate[3])){
            // 不在验证规则内
            return false;
        }
        //验证条件
        $validate[2] = isset($validate[2]) ? $validate[2] : Validate::MUST_VALIDATE;

        if ($validate[2] == Validate::EXISTS_VALIDATE && !isset($data)) {
            return false;
        } else if ($validate[2] == Validate::VALUE_VALIDATE && empty($data)) {
            //不为空时处理
            return false;
        } else if ($validate[2] == Validate::VALUE_NULL && !empty($data)) {
            //值为空时处理
            return false;
        } else if ($validate[2] == Validate::NO_EXISTS_VALIDATE && isset($data)) {
            //值为空时处理
            return false;
        } else if ($validate[2] == Validate::MUST_VALIDATE) {
            //必须处理
        }
        //表单值
        $value = isset($data) ? $data : '';
        //验证规则
        if ($validate[0] instanceof Closure) {
            $method = $validate[0];
            //闭包函数
            if ($method($value) !== true) {
                $this->error = $validate[1];
            }
        } else {
            $actions = explode('|', $validate[0]);
            foreach ($actions as $action) {
                $info = explode(':', $action);
                $method = $info[0];
                $params = isset($info[1]) ? $info[1] : '';
                if (method_exists($this, $method)) {
                    //类方法验证
                    if ($this->$method($value,$params)
                        !== true
                    ) {
                        $this->error = $validate[1];
                    }
                }
//                else if (isset($this->validate[$method])) {
//                    $callback = $this->validate[$method];
//                    if ($callback instanceof Closure) {
//                        //闭包函数
//                        if ($callback($validate[0], $value, $params, $data)
//                            !== true
//                        ) {
//                            $this->error = $validate[1];
//                        }
//                    }
//                }
            }
        }

        //验证返回信息处理
        return $this->respond($this->error);
    }

    public function respond($errors)
    {
        //验证返回信息处理
        if ($errors) {
           throw new FormException($errors);
        }
        return [];
    }

    /**
     * 添加验证闭包
     *
     * @param $name
     * @param $callback
     */
//    public function extend($name, $callback)
//    {
//        if ($callback instanceof Closure) {
//            $this->validate[$name] = $callback;
//        }
//    }

    /**
     * 验证判断是否失败
     *
     * @return bool
     */
//    public function fail()
//    {
//        return !empty($this->error);
//    }

    /**
     * 获取错误信息
     *
     * @return array
     */
//    public function getError()
//    {
//        return $this->error;
//    }
}