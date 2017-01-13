<?php
namespace smladeoye\paystack;

use yii\base\Component;
use yii\base\Exception;
use Yii;

class Transaction extends Component
{
    public $reference;
    public $authorization_url;
    public $access_code;
    public $authorization_code;
    private $callbackUrl;

    public $path;

    /** @var array holds the default transaction operation configuration */
    private $transaction = array(
        'baseUrl'=>'/transaction',
        'initializeUrl'=>'/initialize',
        'verifyUrl'=>'/verify',
        'chargeUrl'=>'/charge_authorization',
        'timelineUrl'=>'/timeline',
        'totalUrl'=>'/totals',
        'exportUrl'=>'/export',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    /*Constructor method to setup paystack component transaction operation configurations
    * @param $paystack, Paystack instance
     *@param config, Yii2 default object configuration array
    */
    public function __construct(Paystack $paystack,$config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->transaction = array_replace($this->transaction,$paystack->transaction);
        $this->setConfig($this->transaction);

        parent::__construct($config);
    }

    /**set the callback url after any transaction is made
     * @param $callback*/
    public function setCallbackUrl($callback)
    {
        $this->callbackUrl = $callback;
    }

    /** @param $options array; initialize a transaction
     * @return $this
     */
    public function initialize($options = null)
    {
        if ($options)
        {
            $this->setRequestOptions($options);
        }

        if (!isset($this->requestOptions['callback_url']) && isset($this->callbackUrl))
        {
            $this->requestOptions = array_replace($this->requestOptions,array('callback_url'=>$this->callbackUrl));
        }

        $this->sendRequest(Paystack::OP_TRANS_INITIALIZE,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    /**verify a particular transaction
     * @param $trx_ref transaction id or reference no
     * @return $this
    */
    public function verify($trx_ref = null)
    {
        $this->accept_array = false;

        if ($trx_ref != null)
            $this->setRequestOptions($trx_ref);
        else
            $this->setRequestOptions($this->reference);

        $this->sendRequest(Paystack::OP_TRANS_VERIFY);
        $this->setResponseOptions();

        return $this;
    }

    /** fetch all transactions
     *@param $options array
     * @return $this
     */
    public function fetchAll($options = null)
    {
        if ($options)
        {
            $this->setRequestOptions($options);
        }

        $this->sendRequest(Paystack::OP_TRANS_LIST);
        $this->setResponseOptions();

        return $this;
    }

    /** fetch a particular transaction
     * @param $id the transaction id or reference
     * @return $this
     */
    public function fetch($id = null)
    {
        $this->accept_array = false;

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_TRANS_FETCH);
        $this->setResponseOptions();

        return $this;
    }


    /**
     * @param $options
     * @return $this
     */
    public function charge($options = null)
    {
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_TRANS_CHARGE,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    /**
     * @param $id
     * $id could be any transaction id or reference
     * @return $this
     */
    public function timeline($id = null)
    {
        $this->accept_array = false;

        if ($id != null)
            $this->setRequestOptions($id);
        else
            $this->setRequestOptions($this->reference);

        $this->sendRequest(Paystack::OP_TRANS_TIMELINE);
        $this->setResponseOptions();

        return $this;
    }

    /**
     * @param sting/array $from --> lower bound of date range for the transaction query
     * @param string (date) $to --> upper bound of date range
     * if an array is provided instead, both limit should be specified in the array
     * @return $this
     */
    public function total($from = null, $to = null)
    {
        $options = array();
        if (is_array($from))
        {
            $this->setRequestOptions($from);
        }
        else
        {
            if ($from)
                $options['from'] = $from;
            if ($to)
                $options['to'] = $to;
            $this->setRequestOptions($options);
        }

        $this->sendRequest(Paystack::OP_TRANS_TOTAL);
        $this->setResponseOptions();

        return $this;
    }

    /** @param $options, an array of options in key => value format
     * @return $this
     */
    public function export($options = null)
    {
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_TRANS_EXPORT,Paystack::METHOD_GET);
        $this->setResponseOptions();

        return $this;
    }

    /** get the authorization url generated from initializing a transaction
     * @return $this
     */
    public function getAuthorizationUrl()
    {
        return $this->authorization_url;
    }

    /**
     * @param $refno string generated reference number
     * @return $this instance of the class
     */
    public function setReference($refno)
    {
        $this->reference = $refno;
        return $this;
    }

    /**
     * @return string reference number which must have been set or generated
     */
    public function getReference()
    {
        return $this->reference;
    }

    /** get the export url generated from performing export on transaction */
    public function getExportPath()
    {
        return $this->path;
    }

    /** helper method to generate random reference which could be used in initiating a transaction*/
    public function generateRef($length = 10)
    {
        $this->reference = $this->paystack()->generateRef($length);

        return $this->reference;
    }

    /** redirect to the authorization url after successfully gettting authorization */
    public function redirect()
    {
        $url = $this->getAuthorizationUrl();

        if (empty($url))
        {
            throw new Exception('Authorization URL is empty');
        }

        Yii::$app->response->redirect($this->getAuthorizationUrl());
    }

    /** download the exported data from the export url gotten from export transaction method */
    public function download()
    {
        if (empty($this->path))
        {
            throw new Exception('Export link is empty');
        }

        yii::$app->response->redirect($this->getExportPath());
    }
}