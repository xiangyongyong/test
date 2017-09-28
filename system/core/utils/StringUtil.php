<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/12
 * Time: 下午10:41
 */

namespace system\core\utils;


use yii\helpers\Json;

class StringUtil
{
    const TRAFFIC_CARRY = 1024; //流量进位

    /**
     * 字符串参数转化为数组;
     * 主要为了处理以下格式的数据：
     * id=123456
     * secret=888888
     * 也可以处理以下格式的数据：
     * 192.168.0.1
     * 192.168.2.10
     * @param $string
     * @return array
     */
    public static function paramsToArray($string)
    {
        $newParams = [];
        $lines = preg_split('/\r?\n/',$string);
        //$lines = explode('\r\n', $string);
        //echo Json::encode($lines);exit;
        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            $one = explode('=', $line, 2);
            if(count($one) == 1) {
                $newParams[] = $one[0];
            }
            elseif(count($one) == 2){
                $newParams[$one[0]] = $one[1];
            }
        }

        return $newParams;
    }

    /**
     * 格式化流量
     * @param $bytes int 字节
     * @return string
     */
    public static function bytes_format($bytes) {

        if ($bytes / (self::TRAFFIC_CARRY * self::TRAFFIC_CARRY * self::TRAFFIC_CARRY) >= 1)
            return number_format ( $bytes / (self::TRAFFIC_CARRY * self::TRAFFIC_CARRY * self::TRAFFIC_CARRY), 2 ) . "G";
        else if ($bytes / (self::TRAFFIC_CARRY * self::TRAFFIC_CARRY) >= 1)
            return number_format ( $bytes / (self::TRAFFIC_CARRY * self::TRAFFIC_CARRY), 2 ) . "M";
        else if (($bytes / 1000) >= 1)
            return number_format ( $bytes / self::TRAFFIC_CARRY, 2 ) . "KB";
        else
            return $bytes . "B";
    }

    /**
     * 格式化时间
     * @param $second int 秒
     * @return string
     */
    public static function seconds_format($second) {
        $h = floor ( $second / 3600 );
        $m = floor ( ($second % 3600) / 60 );
        $s = floor ( ($second % 3600) % 60 );
        $out = "";
        if ($h > 0)
            $out = number_format($h,0) . '小时' . $m . '分' . $s . '秒';
        else if ($m > 0)
            $out = $m . '分' . $s . '秒';
        else
            $out = $s . '秒';
        return $out;
    }
}