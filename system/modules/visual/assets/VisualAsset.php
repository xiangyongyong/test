<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/4/25
 * Time: 下午1:43
 */

namespace system\modules\visual\assets;


use yii\web\AssetBundle;

class VisualAsset extends AssetBundle
{
    public $sourcePath = '@system/modules/visual/static';
    public $css = [
        'css/style.css'
    ];
    public $js = [
        'http://webapi.amap.com/maps?v=1.3&key=84b2f7930d0992706ffd65960e9ecf52',
        'js/jquery-1.12.1.min.js',
        'js/jquery.nicescroll.min.js',
        'js/script.js',
        'js/index.js',
    ];
}