<?php
/**
 * Created by PhpStorm.
 * User: smladeoye
 * Date: 10/30/2016
 * Time: 10:53 AM
 */
namespace smladeoye\payment\paystack;

use yii\base\Component;

class Subscription extends Component
{
    public $id;
    public $code;
    public $email;

    public $invoices;
    public $customer;
    public $subscription_code;
    public $plan;
    public $integration;
    public $authorization;
    public $next_payment_date;
    public $email_token;
    public $created_at;
    public $updated_at;
    public $metadata;
    public $perPage;
    public $page;

    private $subscription = array(
        'baseUrl'=>'/subscription',
        'disableUrl'=>'/disable',
        'enableUrl'=>'/enable',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->subscription = array_replace($this->subscription,$paystack->subscription);
        $this->setConfig($this->subscription);

        parent::__construct($config);
    }

    public function create($options = null)
    {
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_SUBSCRIPTION_CREATE,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    public function fetchAll($page = null,$per_page = null)
    {
        $options = array();
        if (is_array($page))
        {
            $this->setRequestOptions($page);
        }
        else
        {
            if ($page)
                $options['page'] = $page;
            if ($per_page)
                $options['perPage'] = $per_page;

            $this->setRequestOptions($options);
        }

        $this->sendRequest(Paystack::OP_PLAN_LIST);
        $this->setResponseOptions();

        return $this;
    }

    public function fetch($id = null)
    {
        $this->accept_array = false;

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_SUBSCRIPTION_FETCH);
        $this->setResponseOptions();

        return $this;
    }

    public function disable($code = null, $token = null)
    {
        $options = array();
        if (is_array($code))
        {
            $this->setRequestOptions($code);
        }
        else
        {
            if ($code)
                $options['code'] = $code;
            if ($token)
                $options['token'] = $token;

            $this->setRequestOptions($options);
        }

        $this->sendRequest(Paystack::OP_SUBSCRIPTION_DISABLE, Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    public function enable($code = null, $token = null)
    {
        $options = array();
        if (is_array($code))
        {
            $this->setRequestOptions($code);
        }
        else
        {
            if ($code)
                $options['code'] = $code;
            if ($token)
                $options['token'] = $token;

            $this->setRequestOptions($options);
        }

        $this->sendRequest(Paystack::OP_SUBSCRIPTION_ENABLE, Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

}