<?php
namespace smladeoye\paystack;

use yii\base\Component;

class Subscription extends Component
{
    /** @var array holds the default subscription operation configuration */
    private $subscription = array(
        'baseUrl'=>'/subscription',
        'disableUrl'=>'/disable',
        'enableUrl'=>'/enable',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    /*Constructor method to setup paystack component, subscription operation configurations
    * @param $paystack, Paystack instance
     *@param config, Yii2 default object configuration array
    */
    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->subscription = array_replace($this->subscription,$paystack->subscription);
        $this->setConfig($this->subscription);

        parent::__construct($config);
    }

    /** create a subscription
     * @param $options array
     * @return $this
     */
    public function create($options = null)
    {
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_SUBSCRIPTION_CREATE,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    /** fetch all subscription
     * @param $page integer|array
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

        $this->sendRequest(Paystack::OP_SUBSCRIPTION_LIST);
        $this->setResponseOptions();

        return $this;
    }

    /** fetch a particular subscription
     * @param $id string subscription code or id
     * @return $this
     */
    public function fetch($id = null)
    {
        $this->accept_array = false;

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_SUBSCRIPTION_FETCH);
        $this->setResponseOptions();

        return $this;
    }

    /** disable a customer subscription to a plan
     * @param $code string|array subscription plan code
     * @param $token string, the customer token
     * @return $this
     */
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

    /** enable a customer subscription to a plan
     * @param $code string|array subscription plan code
     * @param $token string, the customer token
     * @return $this
     */
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