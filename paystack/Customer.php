<?php
namespace smladeoye\paystack;

use yii\base\Component;

class Customer extends Component
{
    /** @var array holds the default customer operation configuration */
    private $customer = array(
        'baseUrl'=>'/customer',
        'riskActionUrl'=>'/set_risk_action',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    /*Constructor method to setup paystack component consumer operation configurations
    * @param $paystack, Paystack instance
     *@param config, Yii2 default object configuration array
    */
    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->customer = array_replace($this->customer,$paystack->customer);
        $this->setConfig($this->customer);

        parent::__construct($config);
    }

    /** create a customer
     * @param $options string|array
     * @return $this
     */
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

    /** fetch all customers
     * @param $page string|integer
     * @param $per_page string|integer
     * @return $this
     */
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

    /** fetch a particular customer
     * @param $id string|integer customer id
     * @return $this
     */
    public function fetch($id = null)
    {
        $this->acceptArray(false);

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_CUST_FETCH);
        $this->setResponseOptions();

        return $this;
    }

    /** update a particular customer record
     * @param $id string|integer customer id or reference
     * @param $options array, other parameters
     * @return $this
     */
    public function update($customer_id,$options = null)
    {
        if (is_array($customer_id) || empty($customer_id))
            throw new InvalidArgumentException('Invalid argument supplied for customer id, id must be string');

        $options['id'] = $customer_id;
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_CUST_UPDATE,Paystack::METHOD_PUT);
        $this->setResponseOptions();

        return $this;
    }

    /** whitelist a particular customer
     * @param $customer_id string, customer id
     * @return $this
     */
    public function whitelist($customer_id)
    {
        $options['risk_action'] = Paystack::WHITELIST;

        $options['customer'] = $customer_id;

        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_CUST_WHITELIST,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    /** blacklist a particular customer
     * @param $customer_id string, customer id
     * @return $this
     */
    public function blacklist($customer_id)
    {
        $options['risk_action'] = Paystack::BLACKLIST;

        $options['customer'] = $customer_id;

        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_CUST_BLACKLIST,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }
}