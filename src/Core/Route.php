<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/3/27
 * Time: 15:02
 */

namespace AtSoft\SingPHP\Core;


use AtSoft\SingPHP\Common\Dir;

class Route
{
    public static function load()
    {
        $tmpFiles = Dir::tree(AppPath, "/(.*?)Route.php/i");
        $files = [];
        $files = array_merge($files, $tmpFiles);
        $config = Config::get('router', []);
        if (!empty($files)) {
            foreach ($files as $file) {
                $config += include "{$file}";
            }
        }
        return $config;
    }
}