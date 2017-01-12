<?php
namespace smladeoye\paystack;

use yii\base\Component;

class SubAccount extends Component
{
    public $id;
    public $code;
    public $email;
    public $first_name;
    public $last_name;
    public $metadata;
    public $perPage;
    public $page;

    private $subaccount = array(
        'baseUrl'=>'/subaccount',
        'listBank'=>'/bank',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->subaccount = array_replace($this->subaccount,$paystack->subaccount);
        $this->setConfig($this->subaccount);

        parent::__construct($config);
    }

    public function create($options = null)
    {
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_SUBACCOUNT_CREATE,Paystack::METHOD_POST);
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

        $this->sendRequest(Paystack::OP_SUBACCOUNT_LIST);
        $this->setResponseOptions();

        return $this;
    }

    public function fetch($id = null)
    {
        $this->accept_array = false;

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_SUBACCOUNT_FETCH);
        $this->setResponseOptions();

        return $this;
    }

    public function update($account_id,$options = null)
    {
        if (is_array($account_id) || empty($account_id))
            throw new InvalidArgumentException('Invalid argument supplied for subaccount id, id cannot be empty and must be an integer or sting');

        $options['id'] = $account_id;
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_SUBACCOUNT_UPDATE,Paystack::METHOD_PUT);
        $this->setResponseOptions();

        return $this;
    }

    public function listBank($page = null,$per_page = null)
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

        $this->sendRequest(Paystack::OP_SUBACCOUNT_BANKS);
        $this->setResponseOptions();

        return $this;
    }
}