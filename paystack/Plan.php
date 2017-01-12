<?php
/**
 * Created by PhpStorm.
 * User: smladeoye
 * Date: 10/30/2016
 * Time: 10:53 AM
 */
namespace smladeoye\paystack;

use yii\base\Component;

class Plan extends Component
{
    public $id;
    public $code;
    public $email;
    public $metadata;
    public $perPage;
    public $page;

    private $plan = array(
        'baseUrl'=>'/plan',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->plan = array_replace($this->plan,$paystack->plan);
        $this->setConfig($this->plan);

        parent::__construct($config);
    }

    public function create($options = null)
    {
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_PLAN_CREATE,Paystack::METHOD_POST);
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

        $this->sendRequest(Paystack::OP_PLAN_FETCH);
        $this->setResponseOptions();

        return $this;
    }

    public function update($account_id,$options = null)
    {
        if (is_array($account_id) || empty($account_id))
            throw new InvalidArgumentException('Invalid argument supplied for subaccount id, id cannot be empty and must be an integer or sting');

        $options['id'] = $account_id;
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_PLAN_UPDATE,Paystack::METHOD_PUT);
        $this->setResponseOptions();

        return $this;
    }
}