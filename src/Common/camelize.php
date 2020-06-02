<?php
/**
 * Created by PhpStorm.
 * User: Sing
 * Mail: 78276478@qq.com
 * Date: 2020/6/2
 * Time: 17:23
 */

namespace AtSoft\SingPHP\Common;


class camelize
{
    /**
     * 　　 下划线转驼峰
     * 　　 思路:
     * 　　 step1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * 　　 step2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     **/
    public static function enCamelize($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    /**
     * 　　* 驼峰命名转下划线命名
     * 　　* 思路:
     * 　　* 小写和大写紧挨一起的地方,加上分隔符,然后全部转小写
     * 　　*/
    public static function DeCamelize($camelCaps, $separator = '_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}