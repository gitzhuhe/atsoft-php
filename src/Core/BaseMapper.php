<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/3/20
 * Time: 9:27
 */

namespace AtSoft\SingPHP\Core;


use AtSoft\SingPHP\Common\Entity;
use AtSoft\SingPHP\Db\DB;

class BaseMapper
{
    protected $table;

    public function insert(Entity $data)
    {
        $data->setInputtime(time());
        DB::insert($this->table, $data->toArray());
        return DB::id();
    }

    public function getById(Entity $data)
    {
        $class = get_class($data);
        $data = DB::get($this->table, '*', [
            $data->getPrimaryKey() => $data->getPrimaryKeyValue()
        ]);
        $data = $data ? $this->Wrapper($class, $data) : false;
        return $data;
    }

    public function getByMap(Entity $data)
    {
        $class = get_class($data);
        $data = DB::get($this->table, '*', $data->toArray(true));
        $data = $data ? $this->Wrapper($class, $data) : false;
        return $data;
    }

    public function updateById(Entity $data)
    {
        $data->setUpdatetime(time());
        $data = DB::update($this->table, $data->toArray(), [
            $data->getPrimaryKey() => $data->getPrimaryKeyValue()
        ]);
        return $data;
    }

    public function updateByMap(Entity $data, Entity $map)
    {
        $data->setUpdatetime(time());
        return DB::update($this->table, $data->toArray(), $map->toArray(true));
    }

    public function count(Entity $data, $other)
    {
        $where = array_merge(['display' => 1], $data->toArray(true), $other);
        return DB::count($this->table, $where);
    }

    public function fetch(Entity $data, $start = 0, $limit = 10, $other = [])
    {
        if ($limit > 1000) {
            $limit = 10;
        }
        $where = [
            'LIMIT' => [$start, $limit]
        ];
        $class = get_class($data);
        $where = array_merge(['display' => 1], $data->toArray(true), $where, $other);
        $data = DB::select($this->table, '*', $where);
        if ($data) {
            $data = $this->WrapperList($class, $data);
        } else {
            $data = [];
        }
        return $data;
    }

    public function fetchByMap(Entity $data, $other = [])
    {
        $class = get_class($data);
        $data = $data->toArray(true);
        $data = array_merge($data, $other);
        $data = DB::select($this->table, '*', $data);
        if ($data) {
            $data = $this->WrapperList($class, $data);
        } else {
            $data = [];
        }
        return $data;
    }


    public function delete(Entity $data)
    {
        $entity = $this->setkeyValue($data);
        $entity->setDisplay(0);
        return $this->updateById($entity);
    }

    public function recovery(Entity $data)
    {
        $entity = $this->setkeyValue($data);
        $entity->setDisplay(1);
        return $this->updateById($entity);
    }

    protected function setkeyValue(Entity $data)
    {
        $keyValue = $data->getPrimaryKeyValue();
        $PrimaryKey = $data->getPrimaryKey();
        $func = 'set' . ucfirst($PrimaryKey);
        $class = get_class($data);
        $entity = Di::entity($class);
        $entity->$func($keyValue);
        return $entity;
    }

    public function quote($value)
    {
        return DB::quote($value);
    }

    public function query($sql)
    {
        return DB::query($sql);
    }

    protected function Wrapper($class, $data)
    {
        return Di::entity($class, $data);
    }

    protected function WrapperList($class, $data)
    {
        $list = [];
        foreach ($data as $key => $value) {
            $list[$key] = Di::entity($class, $value);
        }
        return $list;
    }
}