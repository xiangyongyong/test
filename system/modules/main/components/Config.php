<?php
/**
 * 系统配置参数组件
 * 访问形式：Yii::$app->systemConfig->get('name');
 * User: ligang
 * Date: 2017/3/12
 * Time: 下午3:29
 */

namespace system\modules\main\components;


use system\core\utils\StringUtil;
use yii\base\Component;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

class Config extends Component
{

    // 将所有数据缓存起来
    private $_data = null;

    // 获取value
    public function getValue($name, $default = '')
    {
        return $this->get($name, 'value', $default);
    }

    /**
     * 然后根据name获取值
     * @param $name string 名称
     * @param string $field 字段
     * @param string $default 默认值
     * @return mixed
     */
    public function get($name, $field = '', $default = '')
    {
        if ($this->_data == null) {
            $this->_getAll();
        }

        // 如果键不存在
        if (!isset($this->_data[$name])) {
            //throw new InvalidParamException('参数'.$name.'不存在，请配置');
            return $default;
        }

        $item = $this->_data[$name];
        // 枚举类型
        if ($item['type'] == 4) {
            $item['extra'] = StringUtil::paramsToArray($item['extra']);
        }
        // 数组类型
        else if ($item['type'] == 3) {
            $item['value'] = StringUtil::paramsToArray($item['value']);
        }

        //echo '<pre>';print_r($item);exit;

        if ($field != '') {
            if (isset($item[$field])) {
                return $item[$field];
            } else {
                // 字段不存在，返回默认值
                //throw new InvalidParamException('参数'.$field.'不存在');
                return $default;
            }
        }

        return $item;
    }

    /**
     * 获取所有的配置
     * @return array|null
     */
    private function _getAll()
    {
        $data = \system\modules\main\models\Config::find()->asArray()->all();
        // 数组以name为索引
        $data = ArrayHelper::index($data, 'name');
        $this->_data = $data;

        return $this->_data;
    }

    // 更新缓存
    public function refresh()
    {
        $this->_data = null;
    }
}