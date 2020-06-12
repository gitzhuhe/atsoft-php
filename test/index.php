<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/6/12
 * Time: 11:38
 */

use Doctrine\Common\Cache\PhpFileCache;

include '../vendor/autoload.php';
define("APP_ROOT",dirname(__FILE__));
\AtSoft\SingPHP\App::run();