<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/7/19
 * Time: 17:26
 */

namespace SongXuan;

/**
 * Class PhpFun
 * @package SongXuan
 */
class PhpFun{
    /**
     * uuid  生成32位以内随机字符串
     * @param  int $len  长度
     * @return string
     */
    static function uuid32($len = 10){
        $str = md5(uniqid(mt_rand(), true));
        return substr ( $str, 0, $len );
    }

    /**
     * 手机号正则
     * @param int $phone  11 位手机号
     * @return bool
     */
    static function isPhone($phone){
        $myreg='/^1[3-9]\d{9}$/';

        if (!preg_match($myreg, $phone)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 邮箱正则
     * @param  string $email  邮箱号
     * @return bool
     */
    static function isEmail($email){
        $myreg = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/';
        if (!preg_match($myreg, $email)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 二维数组根据某个字段排序
     * @param array  $array 要排序的数组
     * @param string $key 要排序的键
     * @param string $sort  排序类型 SORT_ASC SORT_DESC
     * @return array 排序后的数组
     */
    static function arraySort($array, $key, $sort = SORT_ASC){
        $keysValue = array();
        foreach ($array as $k => $v) {
            $keysValue[$k] = $v[$key];
        }
        array_multisort($keysValue, $sort, $array);
        return $array;
    }

    /**
     * 截取指定两个字符之间字符串
     * @param  string $input      被截取的字符串
     * @param  string $start      开始
     * @param  string $end        结束
     * @return string
     */
    static function strCut($input, $start, $end) {
        $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));
        return $substr;
    }


}