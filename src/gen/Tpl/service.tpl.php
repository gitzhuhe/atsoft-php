<?php
echo "<?php\n";
?>
namespace <?=$moudel?>\service;

use AtSoft\SingPHP\Core\Di;
use <?=$moudel?>\mapper\<?=$name?>Mapper;
use <?=$moudel?>\entity\<?=$name?>Entity;

class <?=$name?>Service
{
    private $mapper;
    private $entity;

    public function __construct()
    {
        $this->mapper = Di::make(<?=$name?>Mapper::class);
        $this->entity = Di::make(<?=$name?>Entity::class);
    }

    public function save()
    {
        $PrimaryKeyValue = $this->entity->getPrimaryKeyValue();
        if(!$PrimaryKeyValue){
            return $this->mapper->insert($this->entity);
        }else{
            return $this->mapper->updateById($this->entity);
        }
    }

    public function get()
    {
        return $this->mapper->getById($this->entity);
    }

    public function fetch()
    {
        $pageSize = $_GET['pageSize'];
        $current = $_GET['page'];
        $start = ($current - 1) * $pageSize;
        $count = $this->mapper->count($this->entity);
        $list = $this->mapper->fetch($this->entity, $start, $pageSize);
        return [
            'page'=>[
                'count'=>$count,
                'pageSize'=>$pageSize,
                'current'=>$current,
            ],
            'list'=>$list,
        ];
    }

    public function delete()
    {
        return $this->mapper->delete($this->entity);
    }

    public function recovery()
    {
        return $this->mapper->recovery($this->entity);
    }
}
