<?php
/* @var $this yii\web\View */
use smladeoye\paystack\widget\PaystackWidget;
?>
<div>
    <?php
    echo PaystackWidget::widget(
        [
            'buttonText'=>'PAY',
            'buttonOptions'=>array(
                'class'=>'btn btn-danger',
                'style'=>'width: 80px;',
            ),
            'options'=>[
                'key'=>Yii::$app->Paystack->testPublicKey,
                'email'=>'smladeoye@ymail.com',
                'ref'=>'123456789',
                'amount'=>'200000',
                'currency' =>'',
                'plan' =>'',
                'quantity' =>'',
                'callback'=>'function(response){alert(response.trxref);};',
                //'callbackUrl' =>'',
            ],
        ]
    );
    ?>
</div>