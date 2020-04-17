<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/3/20
 * Time: 15:10
 */

namespace AtSoft\SingPHP\validate;


use AtSoft\SingPHP\validate\build\Base;

/**
 * 表单验证
 * Class Validate
 */
class Validate {
    //有字段时验证
    const EXISTS_VALIDATE = 1;
    //值不为空时验证
    const VALUE_VALIDATE = 2;
    //必须验证
    const MUST_VALIDATE = 3;
    //值是空时处理
    const VALUE_NULL = 4;
    //不存在字段时处理
    const NO_EXISTS_VALIDATE = 5;

    const required = 'required';
    const email = 'email';
    const http = 'http';
    const tel = 'tel';
    const phone = 'phone';
    const zipCode = 'zipCode';
    const china = 'china';
    const identity = 'identity';
    const exists = 'exists';

    //驱动连接
    protected $link;

    //获取实例
    protected function driver() {
        $this->link = new Base( $this );

        return $this;
    }

    public function __call( $method, $params ) {
        if ( is_null( $this->link ) ) {
            $this->driver();
        }

        return call_user_func_array( [ $this->link, $method ], $params );
    }

    public static function single() {
        static $link;
        if ( is_null( $link ) ) {
            $link = new static();
        }

        return $link;
    }

    public static function __callStatic( $name, $arguments ) {
        return call_user_func_array( [ static::single(), $name ], $arguments );
    }
}