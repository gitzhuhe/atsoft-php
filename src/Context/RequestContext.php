<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/4/8
 * Time: 17:21
 */

namespace AtSoft\SingPHP\Context;

//TODO 自己定义的，如果有好的组件可以继承一下
use AtSoft\SingPHP\Exception\globalException;

class RequestContext
{
    private static $object = null;

    private $_GET = [];

    private $_POST = [];

    private $_COOKIE = [];

    private $method;

    private $actvite;

    private $validate = [];

    private $clientIP;

    private $session_id;

    private $body;

    private static function initObj()
    {
        self::$object = new RequestContext();
    }

    public static function getObj()
    {
        if (!self::$object) {
            self::initObj();
        }
        return self::$object;
    }

    public static function setSessionId($value)
    {
        if (!self::$object) {
            self::initObj();
        }
        self::$object->session_id = $value;
    }

    public static function getSessionId()
    {
        if (!self::$object) {
            self::initObj();
        }
        return self::$object->session_id;
    }

    public static function getGET(string $name = "")
    {
        if (!self::$object) {
            self::initObj();
        }
        if ($name) {
            return self::$object->_GET[$name];
        } else {
            return self::$object->_GET;
        }
    }

    public static function setPOST($post)
    {
        self::getObj()->_POST = $post;
    }

    public static function setBody($value)
    {
        self::getObj()->body = $value;
    }

    public static function getPOST(string $name = "")
    {
        if ($name) {
            return self::getObj()->_POST[$name];
        } else {
            return self::getObj()->_POST;
        }
    }

    public static function getBody()
    {
        return self::getObj()->body;
    }

    public static function __callStatic($name, $arguments)
    {
        if (!self::$object) {
            self::initObj();
        }
        $pre = substr($name, 0, 3);
        $name = lcfirst(substr($name, 3));
        if ($pre === 'get') {
            return self::$object->$name;
        } else if ($pre === 'set') {
            self::$object->$name = $arguments[0];
        }

    }
}