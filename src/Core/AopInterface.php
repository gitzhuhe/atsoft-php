<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/6/12
 * Time: 15:42
 */

namespace AtSoft\SingPHP\Core;


interface AopInterface
{
    function create($params);

    function created($params);
}