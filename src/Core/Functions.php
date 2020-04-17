<?php
/**
 * Created by PhpStorm.
 * User: Sing <78276478@qq.com>
 * Date: 2017/9/23
 * Time: 12:19
 */

namespace AtSoft\SingPHP\Core;


class Functions
{
    public static function dhtmlspecialchars($string, $flags = null) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = self::dhtmlspecialchars($val, $flags);
            }
        } else {
            if($flags === null) {
                $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
                if(strpos($string, '&amp;#') !== false) {
                    $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
                }
            } else {
                if(PHP_VERSION < '5.4.0') {
                    $string = htmlspecialchars($string, $flags);
                } else {
                    if(strtolower(CHARSET) == 'utf-8') {
                        $charset = 'UTF-8';
                    } else {
                        $charset = 'ISO-8859-1';
                    }
                    $string = htmlspecialchars($string, $flags, $charset);
                }
            }
        }
        return $string;
    }
    public static function checkrobot($useragent = '')
    {
        static $kw_spiders = array('bot', 'crawl', 'spider', 'slurp', 'sohu-search', 'lycos', 'robozilla');
        static $kw_browsers = array('msie', 'netscape', 'opera', 'konqueror', 'mozilla');

        $useragent = strtolower(empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent);
        if (strpos($useragent, 'http://') === false && self::dstrpos($useragent, $kw_browsers)) return false;
        if (self::dstrpos($useragent, $kw_spiders)) return true;
        return false;
    }
    public static function dstrpos($string, $arr, $returnvalue = false) {
        if(empty($string)) return false;
        foreach((array)$arr as $v) {
            if(strpos($string, $v) !== false) {
                $return = $returnvalue ? $v : true;
                return $return;
            }
        }
        return false;
    }

    public static function daddslashes($string, $force = 1) {
        if(is_array($string)) {
            $keys = array_keys($string);
            foreach($keys as $key) {
                $val = $string[$key];
                unset($string[$key]);
                $string[addslashes($key)] = self::daddslashes($val, $force);
            }
        } else {
            $string = addslashes($string);
        }
        return $string;
    }

    public static function dsetcookie($var, $value = '', $life = 0, $prefix = 1, $httponly = false) {

        global $_G;

        $config = $_G['config']['cookie'];

        $_G['cookie'][$var] = $value;
        $var = ($prefix ? $config['cookiepre'] : '').$var;
        $_COOKIE[$var] = $value;

        if($value == '' || $life < 0) {
            $value = '';
            $life = -1;
        }

        if(defined('IN_MOBILE')) {
            $httponly = false;
        }

        $life = $life > 0 ? Config::getglobal('timestamp') + $life : ($life < 0 ? Config::getglobal('timestamp') - 31536000 : 0);
        $path = $httponly && PHP_VERSION < '5.2.0' ? $config['cookiepath'].'; HttpOnly' : $config['cookiepath'];

        $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
        if(PHP_VERSION < '5.2.0') {
            setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure);
        } else {
            setcookie($var, $value, $life, $path, $config['cookiedomain'], $secure, $httponly);
        }
    }

    public static function getcookie($key) {
        global $_G;
        return isset($_G['cookie'][$key]) ? $_G['cookie'][$key] : '';
    }
}