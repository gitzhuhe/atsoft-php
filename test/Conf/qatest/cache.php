<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/4/13
 * Time: 10:00
 */

use Doctrine\Common\Cache\PhpFileCache;

return [
    'cache'=>new PhpFileCache(sys_get_temp_dir())
];