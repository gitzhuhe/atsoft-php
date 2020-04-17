<?php
/**
 * Created by PhpStorm.
 * User: Sing <78276478@qq.com>
 * Date: 2017/9/25 0025
 * Time: 上午 10:27
 */

namespace AtSoft\SingPHP\Db;


use AtSoft\SingPHP\Exception\globalException;
use Medoo\Medoo;
use AtSoft\SingPHP\Core\Config;

class DB
{
    private static $Client;
    public $sqlObj;

    private static function getObj($dbNum = '')
    {
        $dbNum = empty($dbNum) ? 'default' : $dbNum;

        if (isset(self::$Client[$dbNum])) {
            return self::$Client[$dbNum];
        } else {
            $config = Config::getField('mysql', $dbNum);
            if (!$config) {
                throw new globalException('DB Config is null!');
            }
            self::$Client[$dbNum] = new Medoo($config);
        }
        return self::$Client[$dbNum];
    }

    public static function PDO($dbNum = '')
    {
        $Obj = self::getObj($dbNum);
        return $Obj->pdo;
    }

    public static function Obj($dbNum = '')
    {
        $Obj = self::getObj($dbNum);
        return $Obj;
    }

    public static function table($table)
    {
        $Obj = new DB();
        $Obj->sqlObj = new \stdClass();
        $Obj->sqlObj->table = $table;
        return $Obj;
    }

    public function where($where)
    {
        $this->sqlObj->where = $where;
        return $this;
    }

    public function field($field = '*')
    {
        $this->sqlObj->field = $field;
        return $this;
    }

    public function __call($name, $arguments)
    {
        list($dbNum, $table) = explode('_', $this->sqlObj->table);
        if (empty($table)) {
            $table = $dbNum;
            $dbNum = 'default';
        }
        $obj = DB::getObj($dbNum);
        switch ($name) {
            case 'select':
            case 'get':
                return call_user_func_array([$obj, $name], [
                    $table,
                    $this->sqlObj->field,
                    $this->sqlObj->where
                ]);
                break;
            case 'update':
                return call_user_func_array([$obj, $name], [
                    $table,
                    $arguments[0],
                    $this->sqlObj->where
                ]);
                break;
            case 'insert':
                return call_user_func_array([$obj, $name], [
                    $table,
                    $arguments[0]
                ]);
                break;
        }
    }

    public static function __callStatic($name, $arguments)
    {
        list($dbNum, $name) = explode('_', $name);
        if (empty($name)) {
            $name = $dbNum;
            $dbNum = 'default';
        }
        $obj = self::getObj($dbNum);
        return call_user_func_array([$obj, $name], $arguments);
    }
}