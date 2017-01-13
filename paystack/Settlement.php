<?php
namespace smladeoye\paystack;

use yii\base\Component;

class Settlement extends Component
{
    /** @var array holds the default paystack component settlement operation configuration */
    private $settlement = array(
        'baseUrl'=>'/settlement',
        'beforeSend'=>array(),
        'afterSend'=>array()
    );

    /*Constructor method to setup paystack component, settlement operation configurations
    * @param $paystack, Paystack instance
     *@param config, Yii2 default object configuration array
    */
    public function __construct(Paystack $paystack, $config = [])
    {
        $this->attachBehavior('Resources',array('class'=> Resources::className()));

        $this->setPaystack($paystack);

        $this->settlement = array_replace($this->settlement,$paystack->settlement);
        $this->setConfig($this->settlement);

        parent::__construct($config);
    }

    /** fetch all settlements
     * @param $from string
     * @param $to string
     * @return $this
     */
    public function fetchAll($from = null,$to = null, $subaccount = null)
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
            if ($subaccount)
                $options['subaccount'] = $subaccount;

            $this->setRequestOptions($options);
        }

        $this->sendRequest(Paystack::OP_SETTLEMENT_LIST);
        $this->setResponseOptions();

        return $this;
    }
}