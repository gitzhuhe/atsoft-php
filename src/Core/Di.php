<?php

namespace AtSoft\SingPHP\Core;

abstract class Di
{


    private static $closureList;

    /**
     * @var $_container Container
     */
    private static $_container;

    public static function init($container)
    {
        self::$_container = $container;
    }

    public static function getContainer()
    {
        return self::$_container;
    }

    /**
     * 注入
     * @param $key
     * @param $type
     * @param $objectName
     */
    public static function set($objectName, $params = null)
    {
        self::$closureList[$objectName] = self::aMake($objectName, $params);
    }

    /**
     * @param $key
     * @param string $type
     * @return mixed
     */
    public static function get($objectName, $params = [])
    {
        return self::make($objectName, $params);
    }


    /**
     * @param $key
     * @param string $type
     * @return mixed
     */
    public static function make($objectName, $params = null)
    {
        if (empty(self::$closureList[$objectName])) {
            self::set($objectName, $params);
        }
        return call_user_func(self::$closureList[$objectName]);
    }

    private static function aMake($objectName, $params = null)
    {
        return function () use ($objectName, $params) {
            return self::$_container->make($objectName, $params, true);
        };
    }

    public static function entity($objectName,$data = [])
    {
        return call_user_func(self::aMake($objectName, $data));
    }


    public static function clear($objectName)
    {

    }
}