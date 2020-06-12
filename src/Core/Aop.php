<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/6/12
 * Time: 15:15
 */

namespace AtSoft\SingPHP\Core;


use AtSoft\SingPHP\Common\Dir;

class Aop
{
    public static function load()
    {
        $tmpFiles = Dir::tree(AppPath, "/(.*?)Aop.php/i");
        $files = [];
        $files = array_merge($files, $tmpFiles);
        $config = [];
        if (!empty($files)) {
            foreach ($files as $file) {
                $config += include "{$file}";
            }
        }
        return $config;
    }
}