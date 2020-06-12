<?php
/*
 * 核心的入口文件 
 */

namespace AtSoft\SingPHP;

use AtSoft\SingPHP\Common\Cache;
use AtSoft\SingPHP\Common\Dir;
use AtSoft\SingPHP\Context\RequestContext;
use AtSoft\SingPHP\Controller\Controller;
use AtSoft\SingPHP\Core\Config;
use AtSoft\SingPHP\Core\Container;
use AtSoft\SingPHP\Core\Di;
use AtSoft\SingPHP\Core\Error;
use AtSoft\SingPHP\Core\Route;
use AtSoft\SingPHP\Core\Sing;
use AtSoft\SingPHP\Common\DocParser;
use AtSoft\SingPHP\doc\gen;
use AtSoft\SingPHP\Exception\CliException;
use AtSoft\SingPHP\Exception\globalException;
use AtSoft\SingPHP\Exception\NotFoundException;
use AtSoft\SingPHP\Exception\UnauthorizedException;
use AtSoft\SingPHP\gen\genCode;

class App extends Sing
{
    private static $_module;

    private static $ErrorClass = Error::class;

    public static function setError($ErrorClass)
    {
        self::$ErrorClass = $ErrorClass;
    }

    public static function run()
    {
        defined('DS') || define('DS', DIRECTORY_SEPARATOR);
        if (!defined('APP_ROOT')) define('APP_ROOT', substr(dirname(__FILE__), 0, -4));

        parent::init();

        //设置异常处理的函数
        set_exception_handler(array(self::$ErrorClass, 'exception_error'));
        //欲注册的自动装载函数。
        if (function_exists('spl_autoload_register')) {
            spl_autoload_register(array(__CLASS__, 'autoload'));
        } else {
            function __autoload($class)
            {
                return Sing::autoload($class);
            }
        }

        Di::init(Container::getInstance());
        $cache = Config::get('cache');
        Cache::initObject($cache);
        $isCLI = preg_match("/cli/i", php_sapi_name()) ? true : false;
        if ($isCLI) {
            global $argv;
            if (!empty($argv[2])) {
                switch ($argv[1]) {
                    case 'doc':
                        // self::doc();
                        break;
                    case 'gen':
                        self::gen();
                        break;
                }
            } else if (!empty($argv[1])) {
                define('__INFO__', trim($argv[1], '/'));
                define('__EXT__', strtolower(pathinfo($argv[1], PATHINFO_EXTENSION)));
                self::controller(trim($argv[1], '/'));
            } else {
                echo "=====================================================\n";
                echo "Usage: gen or controller\n";
                echo "=====================================================\n";
                exit;
            }
        } else {
            define('__INFO__', trim($_SERVER['PATH_INFO'], '/'));
            define('__EXT__', strtolower(pathinfo($_SERVER['PATH_INFO'], PATHINFO_EXTENSION)));
            $result = self::controller(trim($_SERVER['PATH_INFO']));
            if ($result) {
                echo $result;
            }
        }
    }

    private static function controller($path)
    {
        // $path = explode('/', __INFO__);
        if(!$path){
            $path = '/';
        }
        $Router = Route::load();
        $Router += [
            '/doc' => [
                'method' => "POST",
                'module' => gen::class,
                'function' => 'tpl'
            ],
            '/doc/rest' => [
                'method' => "POST",
                'module' => gen::class,
                'function' => 'doc'
            ],
        ];
        $controller = $Router[$path];
        if (!$controller) {
            throw new NotFoundException();
        }
        $class = $controller['module'];
        $actvite = $controller['function'];
        RequestContext::setActvite($actvite);

        $ref = new \ReflectionClass($class);
        if ($ref->getParentClass()->name !== 'AtSoft\SingPHP\Controller\Controller') {
            throw new NotFoundException();
        } else {
            $object = DI::make($class);
            if (!$ref->hasMethod($actvite)) {
                throw new NotFoundException();
            } else {
                $result = call_user_func([$object, $actvite]);
            }
        }
        return $result;
    }

    protected static function gen()
    {
        global $argv;
        if (!$argv[3]) {
            throw new CliException("缺少参数");
        }
        $gen = new genCode();
        $gen->gen($argv[2], $argv[3]);
    }
}