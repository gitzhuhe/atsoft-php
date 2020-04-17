<?php

namespace AtSoft\SingPHP\Core;

use AtSoft\SingPHP\Exception\AuthException;
use AtSoft\SingPHP\Exception\CliException;
use AtSoft\SingPHP\Exception\FormException;
use AtSoft\SingPHP\Exception\NoAuthException;
use AtSoft\SingPHP\Exception\NotFoundException;
use AtSoft\SingPHP\Exception\ServerException;
use AtSoft\SingPHP\Exception\UnauthorizedException;
use AtSoft\SingPHP\response\errorResponse;
use AtSoft\SingPHP\response\response;

class Error
{
    public static function exception_error($exception)
    {
        $type = $exception->getMessage();
        $trace = $exception->getTrace();
        krsort($trace);
        $trace[] = ['file' => $exception->getFile(), 'line' => $exception->getLine(), 'function' => 'break'];

        if ($exception instanceof UnauthorizedException) {
            $code = '403';
            $type = 'UnauthorizedException';
        } else if ($exception instanceof NotFoundException) {
            $code = '404';
            $type = 'NotFoundException';
        } else if ($exception instanceof ServerException) {
            $code = '500';
            $type = 'ServerException';
        } else if ($exception instanceof AuthException) {
            $code = '401';
        } else if ($exception instanceof FormException) {
            $code = '601';
        } else if ($exception instanceof NoAuthException) {
            $code = '302';
            $type = 'NoAuthException';
        } else if ($exception instanceof CliException) {
            echo $type . "\n";
            foreach ($trace as $value){
                print_r($value)."\n";
            }
            exit;
        } else {
            $code = '501';
        }

        if (Config::getField('app', 'debug', false) && !defined('OL')) {
            $debug = ['debug' => $trace];
        } else {
            $debug = null;
        }
        echo errorResponse::result($code, $type, $debug);
        exit();

    }

}