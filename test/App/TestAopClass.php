<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/6/12
 * Time: 15:26
 */

use AtSoft\SingPHP\Core\AopInterface;

class TestAopClass implements AopInterface
{

    public function docBefore($arguments){
        return $arguments;
    }

    public function docAfter($result){


        return $result;
    }

    function create($params)
    {
        return $params;
    }

    function created($params)
    {
        // TODO: Implement created() method.
    }
}