<?php

/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2018/5/8/008
 * Time: 12:13
 */

namespace AtSoft\SingPHP\Controller;



class Controller
{
    public function __construct()
    {
        if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
            exit;
        }
    }


}