# yii2-paystack
YII 2 component for paystack payment integration

## Configuration

In your configuration file (web.php) register the component with the necessary configurations, for example:

```
'paystack' => [
	'class' => 'smladeoye\paystack\Paystack',
	'environment' => 'test',
	'testPublicKey'=>'pk_test_311e89cc6b0e95f1fb53fa0eeaef6b1819f1b0f2',
	'testSecretKey'=>'sk_test_1a4aa18eaec6f4f3b23771edb2c60fe8d8b95cbe',
	'livePublicKey'=>'pk_test_311e89cc6b0e95f1fb53fa0eeaef6b1819f1b0f2',
	'liveSecretKey'=>'pk_test_311e89cc6b0e95f1fb53fa0eeaef6b1819f1b0f2',
],
```

## Usage Example
```php

//Initializing a payment transaction

$paystack = Yii::$app->paystack;

$transaction = $paystack->transaction();
$transaction->initialize(['email'=>'email@smthg.com','amount'=>'100000','currency'=>'NGN']);

//check if an error occured during the operation, you can check response property for response gotten for any operation
if (!$transaction->hasError)
{
    // redirect the user to the payment page gotten from the initialization

    $transaction->redirect();
}
else
{
    // display message
    echo $transaction->message;

    // save/get all the errors information regarding the operation from paystack
    $error = $transaction->getError();
}

```
