<?php
/**
 * Created by PhpStorm.
 * User: ligang
 * Date: 2017/3/21
 * Time: 下午3:52
 */

namespace system\modules\group\widgets;


use yii\base\Widget;

/**
 * ztree树形结构，支持单选，支持多选，支持自定义数据url，支持自定义回调，支持多个树形结构
 * Class GroupWidget
 * @package system\modules\group\widgets
 */
class GroupWidget extends Widget
{
    // 容器id
    public $divId = 'group';
    // 容器div的附加
    public $divOption = 'style="max-height: 500px; overflow-y: auto;"';
    // 要选中的group_id
    public $group_id = '';
    // input的name
    public $inputName = 'group_id';
    // 是否多选; 当自定义onSelect回调函数后此选项无效
    public $isMulti = false;
    // 获取ajax数据的url
    public $getUrl = ['/group/default/ajax'];
    // 自定义回调函数
    public $onSelect = null;

    /**
     * @inheritDoc
     */
    public function run()
    {
        parent::run();

        return $this->render('groupwidget', [
            'id' => $this->divId, // 容器id
            'group_id' => $this->group_id, // 当前选中的groupid
            'inputName' => $this->inputName, // input的name
            'isMulti' => $this->isMulti,
            'getUrl' => $this->getUrl,
            'divOption' => $this->divOption,
            'onSelect' => $this->onSelect,
        ]);
    }

}