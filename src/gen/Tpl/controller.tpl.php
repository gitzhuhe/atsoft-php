<?php
echo "<?php\n";
?>
namespace <?=$moudel?>\controller;

use AtSoft\SingPHP\Controller\Controller;
use AtSoft\SingPHP\Core\Di;
use AtSoft\SingPHP\response\successResponse;
use <?=$moudel?>\service\<?=$name?>Service;

class <?=$name?>Controller extends Controller
{
    public function save()
    {
        $data = Di::make(<?=$name?>Service::class)->save();
        return successResponse::result($data);
    }

    public function get()
    {
        $data = Di::make(<?=$name?>Service::class)->get();
        return successResponse::result($data);
    }
    public function fetch()
    {
        $data = Di::make(<?=$name?>Service::class)->fetch();
        return successResponse::result($data);
    }

    public function delete()
    {
        $data = Di::make(<?=$name?>Service::class)->delete();
        return successResponse::result($data);
    }
    public function recovery()
    {
        $data = Di::make(<?=$name?>Service::class)->recovery();
        return successResponse::result($data);
    }
}
