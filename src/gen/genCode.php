<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/3/20
 * Time: 17:22
 */

namespace AtSoft\SingPHP\gen;


use AtSoft\SingPHP\Core\Config;
use AtSoft\SingPHP\Db\DB;

class genCode
{
    public function gen($table, $moudel)
    {
        $row = DB::query("SHOW FULL COLUMNS FROM ${table}")->fetchAll();
        if ($row) {
            // 找到对应表的信息

            $Fields = array_column($row, 'Field');
            if (!in_array('uid', $Fields)) {
                // 没有删除状态
                DB::query("ALTER TABLE ${table} ADD COLUMN `uid` int(11) NULL DEFAULT 0");
            }
            if (!in_array('display', $Fields)) {
                // 没有删除状态
                DB::query("ALTER TABLE ${table} ADD COLUMN `display` tinyint(2) NULL DEFAULT 1");
            }
            if (!in_array('inputtime', $Fields)) {
                // 没有插入时间
                DB::query("ALTER TABLE ${table} ADD COLUMN `inputtime` int(10) NULL DEFAULT 0");
            }
            if (!in_array('updatetime', $Fields)) {
                // 没有更新时间
                DB::query("ALTER TABLE ${table} ADD COLUMN `updatetime` int(10) NULL DEFAULT 0");
            }
            if (!in_array('inputtime', $Fields)) {
                // 没有插入时间
                DB::query("ALTER TABLE ${table} ADD COLUMN `create_time` datetime(0) NULL");
            }
            if (!in_array('updatetime', $Fields)) {
                // 没有更新时间
                DB::query("ALTER TABLE ${table} ADD COLUMN `update_time` datetime(0) NULL");
            }
            if (!in_array('inputtime', $Fields)) {
                // 没有插入时间
                DB::query("ALTER TABLE ${table} ADD COLUMN `create_user` bigint(20) NULL");
            }
            if (!in_array('updatetime', $Fields)) {
                // 没有更新时间
                DB::query("ALTER TABLE ${table} ADD COLUMN `update_user` bigint(20) NULL");
            }
            $row = DB::query("SHOW FULL COLUMNS FROM ${table}")->fetchAll();

            $dbConfig = Config::getField('mysql', 'default', '');
            $prefix = $dbConfig['prefix'];
            $table = str_replace($prefix, '', $table);
            $DbTable = $table;
            $table = explode("_", $table);
            foreach ($table as $key => $value) {
                $table[$key] = ucfirst($value);
            }
            $table = implode("", $table);
            $this->genController($table, $moudel);
            $this->genService($table, $moudel);
            $this->genMapper($DbTable, $table, $moudel);
            $this->genEntity($row, $table, $moudel);
            $this->genRoute($table, $moudel);
        }
    }

    protected function genController($name, $moudel)
    {
        ob_start();
        include 'Tpl/controller.tpl.php';
        $outPut = ob_get_clean();
        // echo $outPut;
        $fileName = AppPath . '/' . $moudel . '/controller/' . $name . 'Controller.php';
        $filePath = dirname($fileName);
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
        file_put_contents($fileName, $outPut);
        if (ob_get_contents()) ob_end_clean();
    }

    protected function genService($name, $moudel)
    {
        ob_start();
        include 'Tpl/service.tpl.php';
        $outPut = ob_get_clean();
        // echo $outPut;
        $fileName = AppPath . '/' . $moudel . '/service/' . $name . 'Service.php';
        $filePath = dirname($fileName);
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
        file_put_contents($fileName, $outPut);
        if (ob_get_contents()) ob_end_clean();
    }

    protected function genMapper($DbTable, $name, $moudel)
    {
        ob_start();
        include 'Tpl/mapper.tpl.php';
        $outPut = ob_get_clean();
        // echo $outPut;
        $fileName = AppPath . '/' . $moudel . '/mapper/' . $name . 'Mapper.php';
        $filePath = dirname($fileName);
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
        file_put_contents($fileName, $outPut);
        if (ob_get_contents()) ob_end_clean();
    }

    protected function genEntity($column, $name, $moudel)
    {
        ob_start();
        include 'Tpl/entity.tpl.php';
        $outPut = ob_get_clean();
        // echo $outPut;
        $fileName = AppPath . '/' . $moudel . '/entity/' . $name . 'Entity.php';
        $filePath = dirname($fileName);
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
        file_put_contents($fileName, $outPut);
        if (ob_get_contents()) ob_end_clean();
    }

    protected function genRoute($name, $moudel)
    {
        ob_start();
        include 'Tpl/route.tpl.php';
        $outPut = ob_get_clean();
        $fileName = AppPath . '/' . $moudel . '/route/' . $name . 'Route.php';
        $filePath = dirname($fileName);
        if (!is_dir($filePath)) {
            mkdir($filePath, 0755, true);
        }
        file_put_contents($fileName, $outPut);
        if (ob_get_contents()) ob_end_clean();
    }
}