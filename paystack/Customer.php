<?php
namespace smladeoye\paystack;

use yii\base\Component;

class Customer extends Component
{
    public $id;
    public $code;
    public $email;
    public $first_name;
    public $last_name;
    public $metadata;
    public $perPage;
    public $page;
    public $risk_action;

    private $customer = array(
        'baseUrl'=>'/customer',
        'riskActionUrl'=>'/set_risk_action',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->customer = array_replace($this->customer,$paystack->customer);
        $this->setConfig($this->customer);

        parent::__construct($config);
    }

    public function create($options = null)
    {
        if (!empty($options))
        {
            if (is_array($options))
                $this->setRequestOptions($options);
            else
                $this->setRequestOptions(['email'=>$options]);
        }

        $this->sendRequest(Paystack::OP_CUST_CREATE,Paystack::METHOD_POST);
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

        $this->sendRequest(Paystack::OP_CUST_LIST);
        $this->setResponseOptions();

        return $this;
    }

    public function fetch($id = null)
    {
        $this->accept_array = false;

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_CUST_FETCH);
        $this->setResponseOptions();

        return $this;
    }

    public function update($id,$options = null)
    {
        $options['id'] = $id;
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_CUST_UPDATE,Paystack::METHOD_PUT);
        $this->setResponseOptions();

        return $this;
    }

    public function whitelist($customer_id)
    {
        $options['risk_action'] = Paystack::WHITELIST;
        $options['customer'] = $customer_id;

        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_CUST_WHITELIST,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    public function blacklist($customer_id)
    {
        $options['risk_action'] = Paystack::WHITELIST;
        $options['customer'] = $customer_id;

        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_CUST_BLACKLIST,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }
}