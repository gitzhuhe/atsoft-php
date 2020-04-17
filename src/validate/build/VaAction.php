<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/3/20
 * Time: 15:15
 */

namespace AtSoft\SingPHP\validate\build;


class VaAction
{
    //字段为空时验证失败
    public function isnull($value, $params)
    {
        if (!isset($value) || $value == '') {
            return false;
        }

        return true;
    }

    //验证字段是否存在
    public function required($value, $params)
    {
        if (!isset($value) || empty($value)) { //TODO 必填值为 0 也会抛出异常
            return false;
        }

        return true;
    }


    //存在字段时验证失败
    public function exists($value, $params)
    {
        return isset($value) ? false : true;
    }

    //邮箱验证
    public function email($value, $params)
    {
        $preg = "/^([a-zA-Z0-9_\-\.])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/i";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //邮编验证
    public function zipCode($value, $params)
    {
        $preg = "/^\d{6}$/i";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //最大长度验证
    public function maxlen($value, $params)
    {
        if (mb_strlen($value, 'utf-8') <= $params) {
            return true;
        }
    }

    //最小长度验证
    public function minlen($value, $params)
    {
        if (mb_strlen($value, 'utf-8') >= $params) {
            return true;
        }
    }

    //网址验证
    public function http($value, $params)
    {
        $preg
            = "/^(http[s]?:)?(\/{2})?([a-z0-9]+\.)?[a-z0-9]+(\.(com|cn|cc|build|net|com.cn))$/i";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //固定电话
    public function tel($value, $params)
    {
        $preg = "/(?:\(\d{3,4}\)|\d{3,4}-?)\d{8}/";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //手机号验证
    public function phone($value, $params)
    {
        $preg = "/^\d{11}$/";
        if (preg_match($preg, $value)) {
            return true;
        }
    }

    //身份证验证
    public function identity($value, $params)
    {
        $preg = "/^(\d{15}|\d{18})$/";
        if (preg_match($preg, $value)) {
            return true;
        }
    }


    //数字范围
    public function num($value, $params)
    {
        $params = explode(',', $params);
        if (intval($value) >= $params[0] && intval($value) <= $params[1]) {
            return true;
        }
    }

    //正则验证
    public function regexp($value, $preg)
    {
        if (preg_match($preg, $value)) {
            return true;
        }
    }



    //中文验证
    public function china($value, $params)
    {
        if (preg_match('/^[\x{4e00}-\x{9fa5}a-z0-9]+$/ui', $value)) {
            return true;
        }
    }
}