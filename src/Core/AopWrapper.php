<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/6/12
 * Time: 14:10
 */

namespace AtSoft\SingPHP\Core;


use AtSoft\SingPHP\Exception\globalException;

class AopWrapper
{
    private $object;
    private $aopObject;

    private static $Aop = null;

    public function __construct($objectName, $params = null)
    {

        $aopObjectName = self::$Aop[$objectName];
        if(!$aopObjectName){
            // 根据父类匹配
            $objRef = new \ReflectionClass($objectName);
            $aopObjectName = self::$Aop[$objRef->getParentClass()->name];
        }
        if (!$this->aopObject && $aopObjectName) {
            $ref = new \ReflectionClass($aopObjectName);
            if (in_array('AtSoft\SingPHP\Core\AopInterface', $ref->getInterfaceNames())) {
                $this->aopObject = new $aopObjectName();
            } else{
                throw new globalException('Aop Type error');
            }
        }else {
            $this->aopObject = new \stdClass();
        }
        // 创建对象前执行
        if (method_exists($this->aopObject, 'create')) {
            $params = $this->aopObject->create($params);
        }
        if ($params === null) {
            $this->object = new $objectName();
        } else {
            $this->object = new $objectName($params);
        }
        // 创建对象后执行
        if (method_exists($this->aopObject, 'created')) {
            $this->aopObject->created($params);
        }
    }

    public static function init($objectName, $params = null)
    {
        // $ref = new \ReflectionClass($objectName);
        if (!self::$Aop) {
            self::$Aop = Aop::load();
        }
        return new self($objectName, $params);
    }

    public function __call($name, $arguments)
    {
        // 运行前
        $beforFunc = $name . 'Before';
        if (method_exists($this->aopObject, $beforFunc)) {
            $arguments = $this->aopObject->$beforFunc($arguments);
        }
        $result = call_user_func_array([$this->object, $name], $arguments);
        // 运行后
        $after = $name . 'After';
        if (method_exists($this->aopObject, $after)) {
            $result = $this->aopObject->$after($result);
        }
        return $result;
    }

    public function getObject(){
        return $this->object;
    }
}