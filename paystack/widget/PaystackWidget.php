<?php
namespace smladeoye\paystack\widget;

use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;
use yii\base\InvalidConfigException;
use yii\web\JqueryAsset;

class PaystackWidget extends Widget
{
    public $options;
    public $buttonOptions = [];

    public $buttonText;

    private $jsScript = 'https://js.paystack.co/v1/inline.js';

    public function init()
    {
        parent::init();
        $this->view->registerJs($this->setHandler(),View::POS_END,'paystack');
        $this->view->registerJsFile($this->jsScript,['depends' => [JqueryAsset::className()]]);
    }

    public function run()
    {
        return Html::tag('button',$this->buttonText,array_merge(['class'=>'btn btn-info'],$this->buttonOptions,['onClick'=>'paystack_inline();']));
    }

    public function setHandler()
    {
        $file = __DIR__.DIRECTORY_SEPARATOR.'_paystack.php';

        if (empty($this->options))
        {
            throw new InvalidConfigException('Paystack options must be set');
        }

        return strip_tags($this->view->renderFile($file,['options'=>$this->options]));
    }
}