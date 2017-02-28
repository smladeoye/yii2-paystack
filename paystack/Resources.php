<?php
namespace smladeoye\paystack;

use yii\base\Behavior;

class Resources extends Behavior
{
    CONST EVENT_AFTER_SEND = 'afterSend';
    CONST EVENT_BEFORE_SEND = 'beforeSend';

    public $hasError = false;

    private $error;
    private $errors;
    private $operationUrl;

    private $accept_array = true;

    private $response;

    private $status = true;
    private $message;
    private $data;
    private $meta;

    protected $requestOptions = array();

    private $_paystack;
    private $_config;
    private $_beforeSend;
    private $_afterSend;

    public function setPaystack(Paystack $paystack)
    {
        $this->_paystack = $paystack;
    }

    public function paystack()
    {
        return $this->_paystack;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    private function verifyPeer()
    {
        return !($this->paystack()->verifyPeer === false);
    }

    public function onBeforeSend()
    {
        if (!empty($this->_beforeSend))
        {
            $this->owner->on('beforeSend',$this->_beforeSend);
        }
    }

    public function afterSend()
    {
        $this->owner->trigger(self::EVENT_AFTER_SEND);
    }

    public function beforeSend()
    {
        $this->owner->trigger(self::EVENT_BEFORE_SEND);
    }

    public function onAfterSend()
    {
        if (!empty($this->_afterSend))
        {
            $this->owner->on('afterSend', $this->_afterSend);
        }
    }

    public function setConfig($config)
    {
        $this->_config = $config;

        $this->_beforeSend = $config['beforeSend']?:$this->paystack()->beforeSend;
        $this->_afterSend = $config['afterSend']?:$this->paystack()->afterSend;

        $this->onBeforeSend();
        $this->onAfterSend();
    }

    public function setResponse($response)
    {
        $this->response = $response;

        foreach ($response as $key => $value)
        {
            if (property_exists($this,$key))
            {
                $this->$key = $value;
            }
        }

        if (!$this->status || isset($this->error) || isset($this->errors))
        {
            $this->hasError = true;
        }
    }

    public function acceptArray($value)
    {
        if (!is_bool($value))
            throw new \InvalidArgumentException('Value must be boolean');
        $this->accept_array = $value;
    }

    public function canAcceptArray()
    {
        return $this->accept_array;
    }

    public function getError()
    {
        return isset($this->error)?$this->error:$this->errors;
    }

    public function getOperationUrl()
    {
        return $this->operationUrl;
    }

    public function setRequestOptions($options = null)
    {
        if (!empty($options))
        {
            if (!$this->accept_array && is_array($options))
            {
                throw new InvalidArgumentException('Array provided, expecting string or integer');
            }

            if (is_array($options))
                $this->requestOptions = $options + $this->requestOptions;
            else
                $this->requestOptions = $options;
        }

        return $this->owner;
    }

    public function getRequestOptions()
    {
        if (!$this->accept_array && is_array($this->requestOptions))
        {
            throw new InvalidArgumentException('Array provided, expecting string or integer');
        }
        return $this->requestOptions;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function getData()
    {
        return $this->data;
    }

    public function sendRequest($operation,$method = Paystack::METHOD_GET)
    {
        $ch = curl_init();

        curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => $this->verifyPeer(),
                CURLOPT_URL => $this->setOperationUrl($operation),
                CURLOPT_HTTPHEADER=>array('Authorization: ')//$this->paystack()->getHeader(),
            )
        );

        $this->beforeSend();

        if ($method== Paystack::METHOD_GET)
        {
            curl_setopt($ch,CURLOPT_POST, false );
        }
        elseif ($method == Paystack::METHOD_POST)
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getRequestOptions() );
        }
        elseif ($method == Paystack::METHOD_PUT)
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, Paystack::METHOD_PUT);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getRequestOptions());
        }
        elseif ($method == Paystack::METHOD_DELETE)
        {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, Paystack::METHOD_DELETE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getRequestOptions());
        }

        $response = json_decode(curl_exec($ch),true);

        if (curl_error($ch))
        {
            $this->hasError = true;
            $this->error = curl_error($ch);
        }
        else
        {
            $this->setResponse($response);
        }
        $this->afterSend();

        curl_close($ch);

        return $this->response;
    }

    private function setOperationUrl($operation)
    {
        switch ($operation)
        {
            case Paystack::OP_TRANS_INITIALIZE:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['initializeUrl'];
                break;

            case Paystack::OP_TRANS_VERIFY:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['verifyUrl'].'/'.$this->getRequestOptions();
                break;

            case Paystack::OP_TRANS_FETCH:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions();
                break;

            case Paystack::OP_TRANS_CHARGE:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['chargeUrl'];
                break;

            case Paystack::OP_TRANS_TIMELINE:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['timelineUrl'].'/'.$this->getRequestOptions();
                break;

            case Paystack::OP_TRANS_TOTAL:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['totalUrl'];
                break;

            case Paystack::OP_TRANS_EXPORT:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['exportUrl'];
                break;

            case Paystack::OP_CUST_FETCH:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions();
                break;

            case Paystack::OP_CUST_UPDATE:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions()['id'];
                break;

            case Paystack::OP_CUST_WHITELIST:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['riskActionUrl'];
                break;

            case Paystack::OP_CUST_BLACKLIST:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['riskActionUrl'];
                break;

            case Paystack::OP_SUBACCOUNT_FETCH:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions();
                break;

            case Paystack::OP_SUBACCOUNT_UPDATE:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions()['id'];
                break;

            case Paystack::OP_SUBACCOUNT_BANKS:
                $opUrl = $this->getConfig()['listBank'];
                break;

            case Paystack::OP_PLAN_FETCH:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions();
                break;

            case Paystack::OP_PLAN_UPDATE:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions()['id'];
                break;

            case Paystack::OP_SUBSCRIPTION_FETCH:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions();
                break;

            case Paystack::OP_SUBSCRIPTION_ENABLE:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['enableUrl'];
                break;

            case Paystack::OP_SUBSCRIPTION_DISABLE:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['disableUrl'];
                break;

            case Paystack::OP_PAGE_FETCH:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions();
                break;

            case Paystack::OP_PAGE_UPDATE:
                $opUrl = $this->getConfig()['baseUrl'].'/'.$this->getRequestOptions()['id'];
                break;

            case Paystack::OP_PAGE_AVAILABILITY:
                $opUrl = $this->getConfig()['baseUrl'].$this->getConfig()['slugAvailabilityUrl'].'/'.$this->getRequestOptions();
                break;

            default:
                $opUrl = $this->getConfig()['baseUrl'];
                break;
        }

        $this->operationUrl = $this->paystack()->apiUrl.$opUrl;
        return $this->operationUrl;
    }

    public function setResponseOptions()
    {
        if (!empty($this->data))
        {
            foreach ($this->data as $key => $data)
            {
                if (property_exists($this->owner,$key))
                {
                    $this->owner->$key = $data;
                }
            }
        }
    }
}