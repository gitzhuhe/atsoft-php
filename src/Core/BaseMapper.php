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

    public function insert($data)
    {
        $data->setInputtime(time());
        DB::insert($this->table, $data->toArray());
        return DB::id();
    }

    public function getById($data)
    {
        $class = $this->getEntityType($data);
        $data = DB::get($this->table, '*', [
            $data->getPrimaryKey() => $data->getPrimaryKeyValue()
        ]);
        $data = $data ? $this->Wrapper($class, $data) : false;
        return $data;
    }

    public function getByMap($data)
    {
        $class = $this->getEntityType($data);
        $data = DB::get($this->table, '*', $data->toArray(true));
        $data = $data ? $this->Wrapper($class, $data) : false;
        return $data;
    }

    public function updateById($data)
    {
        $data->setUpdatetime(time());
        $data = DB::update($this->table, $data->toArray(), [
            $data->getPrimaryKey() => $data->getPrimaryKeyValue()
        ]);
        return $data;
    }

    public function updateByMap($data, $map)
    {
        $data->setUpdatetime(time());
        return DB::update($this->table, $data->toArray(), $map->toArray(true));
    }

    public function count($data, $other = [])
    {
        $where = array_merge(['display' => 1], $data->toArray(true), $other);
        return DB::count($this->table, $where);
    }

    public function fetch($data, $start = 0, $limit = 10, $other = [])
    {
        if ($limit > 1000) {
            $limit = 10;
        }
        $where = [
            'LIMIT' => [$start, $limit]
        ];
        $class = $this->getEntityType($data);
        $where = array_merge(['display' => 1], $data->toArray(true), $where, $other);
        $data = DB::select($this->table, '*', $where);
        if ($data) {
            $data = $this->WrapperList($class, $data);
        } else {
            $data = [];
        }
        return $data;
    }

    public function fetchByMap($data, $other = [])
    {
        $class = $this->getEntityType($data);
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


    public function delete($data)
    {
        $entity = $this->setkeyValue($data);
        $entity->setDisplay(0);
        return $this->updateById($entity);
    }

    public function recovery($data)
    {
        $entity = $this->setkeyValue($data);
        $entity->setDisplay(1);
        return $this->updateById($entity);
    }

    protected function setkeyValue($data)
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

    protected function getEntityType($aopWrapper){
        if($aopWrapper instanceof AopWrapper){
            return get_class($aopWrapper->getObject());
        }else if ($aopWrapper instanceof Entity){
            return get_class($aopWrapper);
        }else{
            return \stdClass::class;
        }
    }
}