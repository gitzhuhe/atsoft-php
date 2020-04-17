<?php
echo "<?php\n";
?>
namespace <?=$moudel?>\mapper;


use AtSoft\SingPHP\Core\BaseMapper;
use AtSoft\SingPHP\Core\Di;
use <?=$moudel?>\entity\<?=$name?>Entity;

class <?=$name?>Mapper extends BaseMapper
{
    public function __construct()
    {
        $this->table = "<?=$DbTable?>";
    }

}
