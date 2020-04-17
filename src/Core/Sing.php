<?php

namespace AtSoft\SingPHP\Core;

use AtSoft\SingPHP\Context\RequestContext;
use AtSoft\SingPHP\Exception\NotFoundException;
use AtSoft\SingPHP\Exception\ServerException;

class Sing
{

    private static $_imports;
    private static $_var = array();
    private static $superglobal = array(
        'GLOBALS' => 1,
        '_GET' => 1,
        '_POST' => 1,
        '_REQUEST' => 1,
        '_COOKIE' => 1,
        '_SERVER' => 1,
        '_ENV' => 1,
        '_FILES' => 1,
        'argv' => 1,
        'HTTP_RAW_POST_DATA' => 1
    );


    public static function init()
    {
        self::_init_env();
        self::_init_config();
        self::_init_input();
        self::_init_output();
    }

    private static function _init_config()
    {
        Config::load(APP_ROOT . '/Conf');
        $_config = Config::all();
        define('AppPath', APP_ROOT . '/' . $_config['AppPath']);
        if (empty($_config['security']['authkey'])) {
            $_config['security']['authkey'] = md5($_config['cookie']['cookiepre'] . $_SERVER['HTTP_USER_AGENT']);
        }

        session_start();
        RequestContext::setSessionId(session_id());
        session_write_close();
        $isHTTPS = ($_SERVER['HTTPS'] && strtolower($_SERVER['HTTPS']) != 'off') ? true : false;
        RequestContext::setIsHTTPS($isHTTPS);
        $HOST_URL = Functions::dhtmlspecialchars('http' . ($isHTTPS ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/');
        RequestContext::setHOST_URL($HOST_URL);
        $siteport = empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? '' : ':' . $_SERVER['SERVER_PORT'];
        RequestContext::setSiteport($siteport);
        RequestContext::setClientIP(self::_get_client_ip());

        //加载核心函数库
        $CORE_FUNCTION_FILE = Config::get('CORE_FUNCTION');// self::$_var['config']['CORE_FUNCTION'];
        if (is_file(APP_ROOT . '/' . $CORE_FUNCTION_FILE)) {
            @include(APP_ROOT . '/' . $CORE_FUNCTION_FILE);
        }
    }

    private static function _init_env()
    {
        define('IN_SING', true);
        if (PHP_VERSION < '5.3.0') {
            set_magic_quotes_runtime(0);
        }
        define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
        define('ICONV_ENABLE', function_exists('iconv'));
        define('MB_ENABLE', function_exists('mb_convert_encoding'));
        define('EXT_OBGZIP', function_exists('ob_gzhandler'));
        define('TIME', time());
        if (function_exists('ini_get')) {

            $memorylimit = @ini_get('memory_limit');
            $memorylimit = trim($memorylimit);
            $last = strtolower($memorylimit{strlen($memorylimit) - 1});
            switch ($last) {
                case 'g':
                    $memorylimit *= 1024;
                case 'm':
                    $memorylimit *= 1024;
                case 'k':
                    $memorylimit *= 1024;
            }
            if ($memorylimit && $memorylimit < 33554432 && function_exists('ini_set')) {
                ini_set('memory_limit', '128m');
            }
        }
        ini_set("session.name", 'S' . base64_decode("SU5HU0VTU0lP") . 'N');
        define('IS_ROBOT', Functions::checkrobot());
        foreach ($GLOBALS as $key => $value) {
            if (!isset(self::$superglobal[$key])) {
                $GLOBALS[$key] = null;
                unset($GLOBALS[$key]);
            }
        }
    }

    private static function _init_input()
    {
        if (isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
            system_error('request_tainting');
        }
        if (MAGIC_QUOTES_GPC) {
            $_GET = dstripslashes($_GET);
            $_POST = dstripslashes($_POST);
            $_COOKIE = dstripslashes($_COOKIE);
        }
        $prelength = strlen(self::$_var['config']['cookie']['cookiepre']);
        foreach ($_COOKIE as $key => $val) {
            if (substr($key, 0, $prelength) == self::$_var['config']['cookie']['cookiepre']) {
                self::$_var['cookie'][substr($key, $prelength)] = $val;
            }
        }

        $postInput = file_get_contents('php://input');
        $phpInput = json_decode($postInput, true);
        if ($phpInput && is_array($phpInput)) {
            foreach ($phpInput as $k => $v) {
                $_POST[$k] = $v;
            }
        } else if(empty($_POST) && $postInput){
//            $_POST = $postInput;
        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST) && is_array($_POST)) {
            $_GET = array_merge($_GET, $_POST);
        }
        $_GET['pageSize'] = $_GET['pageSize'] ? ($_GET['pageSize'] > 5000 ? 5000 : $_GET['pageSize']) : 10;
        $_GET['page'] = $_GET['page'] ? $_GET['page'] : 1;
        RequestContext::setBody($postInput);
        RequestContext::setPOST($_POST);
        RequestContext::set_GET($_GET);
        RequestContext::set_COOKIE($_COOKIE);
    }

    private static function _init_output()
    {
        // 压缩输出
        if (!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false) {
            self::$_var['config']['output']['gzip'] = false;
        }
        $allowgzip = self::$_var['config']['output']['gzip'] && EXT_OBGZIP;
        Config::setglobal('gzipcompress', $allowgzip);

        if (!ob_start($allowgzip ? 'ob_gzhandler' : null)) {
            ob_start();
        }
        Config::setglobal('charset', self::$_var['config']['output']['charset']);
        define('CHARSET', self::$_var['config']['output']['charset']);

        // @header("Access-Control-Allow-Origin: ".$_SERVER['Origin']);
        if (is_array(self::$_var['config']['HEADERS'])) {
            foreach (self::$_var['config']['HEADERS'] as $header) {
                @header($header);
            }
        }
    }

    //加载文件
    public static function import($name, $type = '', $force = true)
    {
        $name = ucfirst($name);
        $name = AppPath . '/' . str_replace('{type}', $type, $name);
        if (!isset(self::$_imports[$name])) {
            $filename = str_replace('\\', '/', $name) . '.php';
            if (is_file($filename)) {
                include $filename;
                self::$_imports[$name] = true;
                return true;
            } elseif (!$force) {
                return false;
            } else {
                throw new NotFoundException("Method does not exist!\nPath:" . $filename);
            }
        }
        return true;
    }


    public static function autoload($class)
    {
        $class = \str_replace('\\', '/', $class);
        try {
            self::import($class);
            return true;
        } catch (\Exception $exc) {
            $trace = $exc->getTrace();
            foreach ($trace as $log) {
                if (empty($log['class']) && $log['function'] == 'class_exists') {
                    return false;
                }
            }
            Error::exception_error($exc);
        }
    }

    private static function _get_client_ip()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }

}