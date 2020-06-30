<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/3/27
 * Time: 14:50
 */

namespace AtSoft\SingPHP\doc;


use AtSoft\SingPHP\Common\camelize;
use AtSoft\SingPHP\Common\Dir;
use AtSoft\SingPHP\Controller\Controller;
use AtSoft\SingPHP\Core\Di;
use AtSoft\SingPHP\Core\Route;
use AtSoft\SingPHP\response\successResponse;

class gen extends Controller
{
    public function tpl()
    {
        header("Content-Type: text/html;charset=UTF-8");
        echo file_get_contents(dirname(__FILE__) . '/index.tpl.php');
    }

    public function doc()
    {
        $routeConfig = Route::load();
        $tmpFiles = Dir::tree(AppPath, "/(.*?)Entity.php/i");
        $files = [];
        $files = array_merge($files, $tmpFiles);
        if (!empty($files)) {
            $files = str_replace(AppPath . '/', "", $files);
            $files = str_replace(".php", "", $files);
            $properties = [];
            foreach ($files as $value) {
                $class = str_replace("/", "\\", $value);
                $obj = Di::make($class, []);
                $moudel = str_replace("Entity", "Controller", $class);
                $moudel = str_replace("entity", "controller", $moudel);
                $field = $obj->getFieldDescription();
                foreach ($field as $k => $v) {
                    $field[$k] = array_merge($v, ['key' => camelize::enCamelize($v['key'])]);
                }
                $properties[strtolower($moudel)] = $field;
            }
        }
        $entity = [];
        foreach ($routeConfig as $key => $value) {
            $class = $value['module'];
            $entity[$key] = $properties[strtolower($class)];
        }

        $list = [];
        foreach ($routeConfig as $key => $value) {
            $list[$key] = [
                'route' => $key,
                'desc' => $value['desc'] ? $value['desc'] : "",
            ];
        }
        return successResponse::result([
            'route' => $list,
            'doc' => $entity,
        ]);
    }
}