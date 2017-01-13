<?php
namespace smladeoye\paystack;

use yii\base\Component;

class Page extends Component
{
    /** @var array holds the default page operation configuration */
    private $page = array(
        'baseUrl'=>'/page',
        'slugAvailabilityUrl'=>'/check_slug_availability',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    /*Constructor method to setup paystack component, page operation configurations
    * @param $paystack, Paystack instance
     *@param config, Yii2 default object configuration array
    */
    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->page = array_replace($this->page,$paystack->page);
        $this->setConfig($this->page);

        parent::__construct($config);
    }

    /** create a page
     * @param $name string|array array
     * @return $this
     */
    public function create($name = null)
    {
        $options = array();
        if (is_array($name))
        {
            $this->setRequestOptions($name);
        }
        else
        {
            if ($name)
                $options['name'] = $name;
            $this->setRequestOptions($options);
        }

        $this->sendRequest(Paystack::OP_PAGE_CREATE,Paystack::METHOD_POST);
        $this->setResponseOptions();

        return $this;
    }

    /** fetch all pages
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

        $this->sendRequest(Paystack::OP_PLAN_LIST);
        $this->setResponseOptions();

        return $this;
    }

    /** fetch a particular page
     * @param $id string|integer page id or slug
     * @return $this
     */
    public function fetch($id = null)
    {
        $this->accept_array = false;

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_PAGE_FETCH);
        $this->setResponseOptions();

        return $this;
    }

    /** update a particular page info
     * @param $id string|integer page id or slug
     * @param $options array, other parameters
     * @throws InvalidArgumentException when page_id is not provided
     * @return $this
     */
    public function update($page_id,$options = null)
    {
        if (is_array($page_id) || empty($page_id))
            throw new InvalidArgumentException('Invalid argument supplied for page id/slug, id cannot be empty and must be an integer or sting');

        $options['id'] = $page_id;
        $this->setRequestOptions($options);

        $this->sendRequest(Paystack::OP_PLAN_UPDATE,Paystack::METHOD_PUT);
        $this->setResponseOptions();

        return $this;
    }

    /** check the availability of a particular page before deciding to create
     * @param $id string|integer transaction id or reference
     * @return $this
     */
    public function checkAvailability($id = null)
    {
        $this->accept_array = false;

        $this->setRequestOptions($id);

        $this->sendRequest(Paystack::OP_PAGE_AVAILABILITY);
        $this->setResponseOptions();

        return $this;
    }

}