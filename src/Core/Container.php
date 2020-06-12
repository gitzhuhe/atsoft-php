<?php

namespace AtSoft\SingPHP\Core;

use AtSoft\SingPHP\Exception\ServerException;

class Container
{
    private static $instances = [];
    private static $allObject = [];

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (empty(self::$instances)) {
            self::$instances = new self();
        }
        return self::$instances;
    }

    public static function make($objectName, $params = null, $allways = false)
    {
        $keyName = $objectName;
        if (is_array($params) && !empty($params['_prefix'])) {
            $keyName .= $params['_prefix'];
        }
        if (isset(self::$allObject[$keyName]) && !$allways) {
            return self::$allObject[$keyName];
        }

        if (!\class_exists($objectName)) {
            throw new ServerException($objectName . ' not exsist!');
        }
        //TODO 在这里注入AOP配置
        self::$allObject[$keyName] = AopWrapper::init($objectName, $params);
//        if ($params===null) {
//            self::$allObject[$keyName] = new AopWrapper(new $objectName());
//        } else {
//            self::$allObject[$keyName] = new AopWrapper(new $objectName($params));
//        }
        return self::$allObject[$keyName];
    }

    public static function AwMake($objectName, $params = null)
    {
        return self::make($objectName, $params, true);
    }

    public function getall()
    {
        return self::$allObject;
    }
}
