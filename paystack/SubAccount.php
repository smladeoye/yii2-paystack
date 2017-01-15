<?php
namespace smladeoye\paystack;

use yii\base\Component;

class SubAccount extends Component
{
    /** @var array holds the default page operation configuration */
    private $subaccount = array(
        'baseUrl'=>'/subaccount',
        'listBank'=>'/bank',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    /*Constructor method to setup paystack component, subaccount operation configurations
    * @param $paystack, Paystack instance
     *@param config, Yii2 default object configuration array
    */
    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->subaccount = array_replace($this->subaccount,$paystack->subaccount);
        $this->setConfig($this->subaccount);

        parent::__construct($config);
    }

    /** create a subaccount
     * @param $options array
     * @return $this
     */
    public function create($options = null)
    {
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_SUBACCOUNT_CREATE,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    /** fetch all subaccount
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

        $this->sendRequest(Paystack::OP_SUBACCOUNT_LIST);
        $this->setResponseOptions();

        return $this;
    }

    /** fetch a particular subaccount
     * @param $id string|integer subaccount id
     * @return $this
     */
    public function fetch($id = null)
    {
        $this->acceptArray(false);

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_SUBACCOUNT_FETCH);
        $this->setResponseOptions();

        return $this;
    }

    /** update a particular subaccount info
     * @param $account_id string|integer page id or slug
     * @param $options array, other parameters
     * @throws InvalidArgumentException when account_id is not provided
     * @return $this
     */
    public function update($account_id,$options = null)
    {
        if (is_array($account_id) || empty($account_id))
            throw new InvalidArgumentException('Invalid argument supplied for subaccount id, id must be an integer or sting');

        $options['id'] = $account_id;
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_SUBACCOUNT_UPDATE,Paystack::METHOD_PUT);
        $this->setResponseOptions();

        return $this;
    }

    /** list all banks available on the platoform for creating subaccount
     * @param $page string|integer|array page no
     * @param $per_page integer
     * @return $this
     */
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