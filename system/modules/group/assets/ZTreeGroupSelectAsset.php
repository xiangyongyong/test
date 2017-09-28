<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/21
 * Time: 下午6:31
 */

namespace system\modules\group\assets;


use yii\web\AssetBundle;

class ZTreeGroupSelectAsset extends AssetBundle
{
    public $sourcePath = '@system/modules/group/static';
    public $css = [
    ];
    public $js = [
        'js/ztree-group-select.js',
    ];

    public $depends = [
        'system\modules\group\assets\ZTreeAsset',
    ];
}