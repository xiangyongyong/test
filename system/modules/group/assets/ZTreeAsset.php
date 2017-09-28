<?php
/**
 * zTree 基础asset
 * User: ligang
 * Date: 2017/3/14
 * Time: 下午7:29
 */
namespace system\modules\group\assets;


use yii\web\AssetBundle;

class ZTreeAsset extends AssetBundle
{
    public $sourcePath = '@system/modules/group/static';
    public $css = [
        'zTree/css/zTreeStyle/zTreeStyle.css',
        'css/ztree-custom.css',
    ];
    public $js = [
        'zTree/js/jquery-1.4.4.min.js',
        'zTree/js/jquery.ztree.all.min.js',
    ];

}