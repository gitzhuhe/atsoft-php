<?php


namespace AtSoft\SingPHP\response;


class successResponse extends response
{
    public static function result($code = 0, $message = '', $data = [])
    {
        if (!$message && !$data) {
            $data = $code;
            $message = 'ok';
            $code = 0;
        } else if (!$data) {
            $data = $message;
            $message = $code;
            $code = 0;
        }
        return parent::result($code, $message, $data);
    }
}