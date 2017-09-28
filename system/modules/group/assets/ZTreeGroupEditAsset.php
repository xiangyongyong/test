<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/14
 * Time: 下午9:58
 */

namespace system\modules\group\assets;


use yii\web\AssetBundle;

class ZTreeGroupEditAsset extends AssetBundle
{
    public $sourcePath = '@system/modules/group/static';
    public $css = [
    ];
    public $js = [
        'js/ztree-group-edit.js',
    ];

    public $depends = [
        'system\modules\group\assets\ZTreeAsset',
    ];
}