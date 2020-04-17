<?php
echo "<?php\n";
?>

return [
    '/<?=$moudel?>/<?=$name?>/save' => [
        'method' => "POST",
        'module' => <?=$moudel?>\controller\<?=$name?>Controller::class,
        'function' => 'save',
        'desc'=>'添加及修改'
    ],
    '/<?=$moudel?>/<?=$name?>/get' => [
        'method' => "POST",
        'module' => <?=$moudel?>\controller\<?=$name?>Controller::class,
        'function' => 'get',
        'desc'=>'获取一条详情'
    ],
    '/<?=$moudel?>/<?=$name?>/fetch' => [
        'method' => "POST",
        'module' => <?=$moudel?>\controller\<?=$name?>Controller::class,
        'function' => 'fetch',
        'desc'=>'列表'
    ],
    '/<?=$moudel?>/<?=$name?>/delete' => [
        'method' => "POST",
        'module' => <?=$moudel?>\controller\<?=$name?>Controller::class,
        'function' => 'delete',
        'desc'=>'删除'
    ],
    '/<?=$moudel?>/<?=$name?>/recovery' => [
        'method' => "POST",
        'module' => <?=$moudel?>\controller\<?=$name?>Controller::class,
        'function' => 'recovery',
        'desc'=>'恢复删除'
    ]
];