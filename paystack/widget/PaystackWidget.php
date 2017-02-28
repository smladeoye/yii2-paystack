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

    private $idOptions = ['email','amount','currency','quantity'];
    private $jsScript = 'https://js.paystack.co/v1/inline.js';

    public function init()
    {
        parent::init();
        $this->view->registerJs($this->setHandler(),View::POS_END,'paystack');
        $this->view->registerJsFile($this->jsScript,['depends' => [JqueryAsset::className()]]);
    }

    private function filterIdOptions()
    {
        $idOptionsList = array();

        foreach ($this->idOptions as $value)
        {
            if  (array_key_exists($value,$this->options))
            {
                $hasSubstr = substr($this->options[$value],0,1) == '#';

                if ($value == 'amount' && !$hasSubstr)
                    $this->options[$value] *= 100;
                elseif ($hasSubstr)
                    $idOptionsList[] = $value;
            }
        }
        $this->idOptions = $idOptionsList;
        return $this->idOptions;
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
        $this->filterIdOptions();
        return strip_tags($this->view->renderFile($file,['options'=>$this->options,'idOptions'=>$this->idOptions]));
    }
}