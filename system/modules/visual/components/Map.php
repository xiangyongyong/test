<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/4/13
 * Time: 下午5:36
 */

namespace system\modules\visual\components;


use yii\base\Component;

class Map extends Component
{
    // key
    public $key = 'def816a0541d8f52537e92f3bdb84792';

    public $transferUrl = 'http://restapi.amap.com/v3/geocode/regeo';


    /**
     * 将经纬度转换为地址
     * @param $longitude integer 经度
     * @param $latitude integer 纬度
     * @return array|bool|mixed
     */
    public function regeo($longitude, $latitude)
    {
        $params = [
            'key' => $this->key,
            'location' => $longitude . ',' . $latitude,
        ];
        $string = http_build_query($params);
        $res = file_get_contents($this->transferUrl.'?'.$string);
        $data = json_decode($res, true);
        //print_r($data);exit;
        if (isset($data['status']) && $data['status'] == 1) {
            // 社区街道，比如卓刀泉街道，有的可能没有街道，比如武汉大学信息学部 114.363959,30.530673
            $township = is_string($data['regeocode']['addressComponent']['township']) ? $data['regeocode']['addressComponent']['township'] : '';

            // 具体等街道，比如卓刀泉南路208号
            $street = $data['regeocode']['addressComponent']['streetNumber']['street'].$data['regeocode']['addressComponent']['streetNumber']['number'];

            $data = [
                'formatted_address' => $data['regeocode']['formatted_address'], // 格式化后的地址
                'province' => $data['regeocode']['addressComponent']['province'], // 省份
                'city' => $data['regeocode']['addressComponent']['city'], // 城市
                'district' => $data['regeocode']['addressComponent']['district'], // 区县
                'township' => $township, // xx街道或者乡镇
                'street' => $street, // 具体街道
            ];

            return $data;

        }

        return false;
    }
}