<?php


namespace AtSoft\SingPHP\response;

use AtSoft\SingPHP\Common\camelize;
use AtSoft\SingPHP\Common\Entity;
use AtSoft\SingPHP\Context\RequestContext;
use AtSoft\SingPHP\Core\AopInterface;
use AtSoft\SingPHP\Core\AopWrapper;

class  response
{
    public static function result($code = "", $message = 'ok', $data = [], $count = false, $pageSize = false)
    {
        $data = self::JsonReturn(
            [
                'errCode' => $code,
                'message' => $message,
                'data' => $data,
            ]
        );
        if ($count !== false) {
            $data['count'] = $count;
        }
        if ($count !== false) {
            $data['pageSize'] = $pageSize;
        }
        if (!is_array($data)) {
            return $data;
        } else {
            $result = json_encode($data);
            if ($_GET['callback']) {
                return $_GET['callback'] . '(' . $result . ')';
            } else {
                return $result;
            }
        }
    }

    private static function JsonReturn($data)
    {
        $type = gettype($data);
        switch ($type) {
            case 'array':
                foreach ($data as $key => $value) {
                    $data[$key] = self::JsonReturn($value);
                }
                break;
            case 'string':
            case 'integer':
                break;
            case 'object':
                $dataList = [];
                $class = self::getEntityType($data);
                $parentClass = get_parent_class($class);
                if ($class && 'AtSoft\SingPHP\Common\Entity' === $parentClass) {
                    $ref = new \ReflectionClass($class);
                    $resultField = [];
                    if (method_exists($data, 'getResultField')) {
                        $resultField = $data->getResultField();
                    }
                    $actvite = RequestContext::getActvite();
                    foreach ($ref->getProperties() as $var) {

                        if ($var->class == $class && !in_array($var->name, $data->getBlackKey()) && ($resultField[$actvite] ? in_array($var->name, $resultField[$actvite]) : true)) {
                            $dataList[camelize::enCamelize($var->name)] = self::JsonReturn(call_user_func([$data, 'get' . ucfirst($var->name)]));
                        }
                    }
                    return $dataList;
                }
                break;
            default:

                break;
        }
        return $data;
    }

    protected static function getEntityType($aopWrapper)
    {
        if ($aopWrapper instanceof AopWrapper) {
            return get_class($aopWrapper->getObject());
        } else if ($aopWrapper instanceof Entity) {
            return get_class($aopWrapper);
        } else {
            return \stdClass::class;
        }
    }
}