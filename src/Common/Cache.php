<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/4/13
 * Time: 9:06
 */

namespace AtSoft\SingPHP\Common;


class Cache
{
    private $cache;
    private static $object;

    public function __construct($cacheDrvie)
    {
        $this->cache = $cacheDrvie;
    }

    public static function initObject($cacheDrvie)
    {
        self::$object = new Cache($cacheDrvie);
    }

    public static function getObject()
    {
        return self::$object;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        return call_user_func_array([$this->cache, $name], $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        // TODO: Implement __callStatic() method.
        return call_user_func_array([self::getObject(), $name], $arguments);
    }
}