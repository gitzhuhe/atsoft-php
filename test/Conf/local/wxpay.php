<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/4/21
 * Time: 10:21
 */

return [
    'wxpay'=>[
        // 必要配置
        'app_id'             => 'wxa4ecd96bbfb3cb92',
        'mch_id'             => '1534863691',
        'key'                => '1e11a15bdeb3fa705d9a4e232e454e95',   // API 密钥

        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
        'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！

        'notify_url'         => 'http://api.test.sapp.shoujiazhihui.com/bank/BankPay/wxNotify',     // 你也可以在下单时单独设置来想覆盖它
    ]
];