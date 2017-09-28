<?php
namespace system\widgets;

use yii\base\Widget;

/**
 * Alert 弹出消息类
 * 使用方法：Yii::$app->session->addFlash('success', '操作成功');
 * 使用方法：Yii::$app->session->addFlash('error', '用户名密码错误');
 * @package work\modules\wechat\widgets
 */
class FlashMsg extends Widget
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $session = \Yii::$app->getSession();
        $flashes = $session->getAllFlashes();

        $html = json_encode($flashes);
        $session->removeAllFlashes();
        echo $html;

        //$flashes = ['ok' => 'hello'];
        /*$html = '';
        foreach ($flashes as $type => $message) {
            if ($type == 'error') {
                //$html .= 'layer.msg("'.$message.'");';

            }
            else if ($type == 'ok') {
                $html .= 'layer.msg("'.$message.'");';
            }
            $session->removeFlash($type);
        }

        echo $html;*/
    }
}
