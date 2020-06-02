<?php
/**
 * Created by PhpStorm.
 * User: Sing <78276478@qq.com>
 * Date: 2017/12/5 0005
 * Time: 下午 13:03
 */

namespace AtSoft\SingPHP\Common;


use AtSoft\SingPHP\Context\RequestContext;
use AtSoft\SingPHP\Exception\globalException;
use AtSoft\SingPHP\validate\Validate;

class Entity
{
    public function __construct($data = null)
    {
        if ($data === null) {
            $data = RequestContext::get_POST();
            $this->initialization($data);
        } else {
            if ($data[1] == true) {
                $this->initialization($data[0], true, false);
            } else {
                $this->initialization($data, false, false);
            }
        }
    }

    protected function initialization($data, $validate = true, $delay = true)
    {
        $ref = new \ReflectionClass($this);
        $class = get_class($this);
        // 防止频繁重复提交
        if ($delay) {
            $sid = RequestContext::getSessionId() . '-' . $class . '-' . RequestContext::getActvite();
            $exist = Cache::contains($sid);
            if ($exist) {
                $cacheData = Cache::fetch($sid);
                if (time() - $cacheData < 2) {
                    throw new globalException("操作太快了");
                }
            }
            Cache::save($sid, TIME, 10);
        }
        $rules = $this->getRules();
        foreach ($ref->getProperties() as $var) {
            $value = null;
            if(isset($data[$var->name])){
                $value = $data[$var->name];
            }else if(isset($data[$this->uncamelize($var->name)])){
                $value = $data[$this->uncamelize($var->name)];
            }
            if ($var->class == $class) {
                $validate &&
                $rules[$var->name] &&
                Validate::make($rules[$var->name],
                    $value);
                if ($value !== null && !in_array($var->name, $this->getBlackKey())) {
                    call_user_func_array([$this, 'set' . ucfirst($var->name)], [$value]);
                }
            }
        }
    }

    public function toArray($isWhere = false)
    {
        $data = [];
        $ref = new \ReflectionClass($this);
        foreach ($ref->getProperties() as $var) {
            if ($var->class == get_class($this)) {
                $value = !in_array($var->name, $this->getBlackKey()) && ($this->field ? in_array($var->name, $this->field) : true) ? call_user_func([$this, 'get' . ucfirst($var->name)]) : "";
                if ($value !== "" && $value !== null && !in_array($var->name, ['blackKey', 'rules', 'fieldDescription'])) {
                    if(is_array($value) && $isWhere){
                        $data[$var->name.'['.$value[0].']'] = $value[1];
                    }else{
                        $data[$var->name] = $value;
                    }
                }
            }
        }
        return $data;
    }

    public function getFieldDescription()
    {
        return $this->fieldDescription;
    }

    public function getBlackKey()
    {
        try {
            if ($this->blackKey != null) {
                return $this->blackKey;
            } else {
                return [];
            }

        } catch (\Exception $exception) {
            return [];
        }

    }
    /**
     * 　　* 驼峰命名转下划线命名
     * 　　* 思路:
     * 　　* 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     * 　　*/
    private function uncamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}