<?php
namespace smladeoye\paystack;

use yii\base\Component;

class Paystack extends Component
{
    /** Operation type to initialize transaction    */
    CONST OP_TRANS_INITIALIZE = 0;

    /** Operation type to verify transaction    */
    CONST OP_TRANS_VERIFY = 1;

    /* Operation type to list all transaction  */
    CONST OP_TRANS_LIST = 2;

    /** Operation type to fetch transaction */
    CONST OP_TRANS_FETCH = 3;

    /** Operation type to charge authorization */
    CONST OP_TRANS_CHARGE = 4;

    /** Operation type to get transaction timeline */
    CONST OP_TRANS_TIMELINE = 5;

    /** Operation type to get transaction total */
    CONST OP_TRANS_TOTAL = 6;

    /** Operation type to expot transaction data
     *  returns a link for the export
     */
    CONST OP_TRANS_EXPORT = 7;

    /**
     * Operation type to create customers
     */
    CONST OP_CUST_CREATE = 8;

    /** Operation type to list all customers  */
    CONST OP_CUST_LIST = 9;

    /**
     * Operation type to fetch a particular customer
     * The cutomer code or email or id is required.
     */
    CONST OP_CUST_FETCH = 10;

    /**
     * Operation type to update a particular customer information
     * The customer code or email or id will be required.
     */
    CONST OP_CUST_UPDATE = 11;

    /** Operation type to whitelist a particular customer
     * The customer code or email or id will be required*/
    CONST OP_CUST_WHITELIST = 12;

    /** Operation type to blacklist a particular customer
     * The customer code or email or id will be required*/
    CONST OP_CUST_BLACKLIST = 13;

    /** Operation code to create a subaccount
     * The business_name, settlement_name, account_number and percentage_charge are required*/
    CONST OP_SUBACCOUNT_CREATE = 14;

    /**
     * Operation code to list all subaccounts
     */
    CONST OP_SUBACCOUNT_LIST = 15;

    /**
     * Operation code to fetch a particular subaccount
     * the account id is required
     */
    CONST OP_SUBACCOUNT_FETCH = 16;

    /**
     * Operation code to update a particular subaccount
     * the account id is required along with the updated info
     */
    CONST OP_SUBACCOUNT_UPDATE = 17;

    /**
     * Operation code to list banks associated with a subaccount
     * the account id is required along with the updated info
     */
    CONST OP_SUBACCOUNT_BANKS = 18;

    /** Operation code to create a plan  */
    CONST OP_PLAN_CREATE = 19;

    /** Operation code to list all plans  */
    CONST OP_PLAN_LIST = 20;

    /** Operation code to detch a plan  */
    CONST OP_PLAN_FETCH = 21;

    /** Operation code to update a plan  */
    CONST OP_PLAN_UPDATE = 22;

    CONST OP_SUBSCRIPTION_CREATE = 23;

    CONST OP_SUBSCRIPTION_LIST = 24;

    CONST OP_SUBSCRIPTION_FETCH = 25;

    CONST OP_SUBSCRIPTION_ENABLE = 26;

    CONST OP_SUBSCRIPTION_DISABLE = 27;

    CONST OP_PAGE_CREATE = 28;

    CONST OP_PAGE_LIST = 29;

    CONST OP_PAGE_FETCH = 30;

    CONST OP_PAGE_UPDATE = 31;

    CONST OP_PAGE_AVAILABILITY = 32;

    /** operation code to list settlements */
    CONST OP_SETTLEMENT_LIST = 33;

    /** Paystack whitelist code */
    CONST WHITELIST = 'allow';

    /** Paystack blacklist code */
    CONST BLACKLIST = 'deny';

    /** GET method for request */
    CONST METHOD_GET = 'GET';

    /** POST method for request */
    CONST METHOD_POST = 'POST';

    /** PUT method for request */
    CONST METHOD_PUT = 'PUT';

    /** DELETE method for request */
    CONST METHOD_DELETE = 'DELETE';

    //plan interval code for hourly
    CONST PLAN_INTERVAL_HOURLY = 'hourly';

    //plan interval code for daily
    CONST PLAN_INTERVAL_DAILY = 'daily';

    //plan interval code for weekly
    CONST PLAN_INTERVAL_WEEKLY = 'weekly';

    //plan interval code for monthly
    CONST PLAN_INTERVAL_MONTHLY = 'monthly';

    //plan interval code for yearly
    CONST PLAN_INTERVAL_YEARLY = 'yearly';

    /**
     * Test environmet
     */
    CONST ENV_TEST = 'TEST';

    /**
     * Live environment
     */
    CONST ENV_LIVE = 'LIVE';

    /** Callback url after a transaction has been performed */
    public $callbackUrl;


    /** @var   string test-key for the test environment */
    /** @var boolean sets the environment for the request to LIVE when test is false */
    public $environment = self::ENV_TEST;

    /** @var bool set to false to allow unsecured connection to the paystack api */
    public $verifyPeer;

    public $testSecretKey;

    public $testPublicKey;

    /** @var   string live-key for the test environment */
    public $livePublicKey;

    /** @var  string live secret key used in live environment */
    public $liveSecretKey;

    /** @var  string Paystack API base url, replaces the defaultApiUrl when set */
    public $apiUrl;

    /** @var  string Paystack API default base url*/
    private $defaultApiUrl = "https://api.paystack.co";

    private static $reference;

    public $header;
    public $authHeader;

    public $beforeSend;
    public $afterSend;

    public $transaction = array(
        'baseUrl'=>'/transaction',
        'initializeUrl'=>'/initialize',
        'verifyUrl'=>'/verify',
        'chargeUrl'=>'/charge_authorization',
        'beforeSend'=>'',
        'afterSend'=>''
    );

    public $customer = array(
        'baseUrl'=>'/customer',
        'beforeSend'=>'',
        'afterSend'=>''
    );

    public $subaccount = array(
        'baseUrl'=>'/subaccount',
        'beforeSend'=>'',
        'afterSend'=>''
    );

    public $plan = array(
        'baseUrl'=>'/plan',
        'beforeSend'=>'',
        'afterSend'=>''
    );

    public $page = array(
        'baseUrl'=>'/page',
        'slugAvailabilityUrl'=>'/check_slug_availability',
        'beforeSend'=>'',
        'afterSend'=>''
    );

    public $subscription = array(
        'baseUrl'=>'/subscription',
        'beforeSend'=>'',
        'afterSend'=>''
    );

    public $settlement = array(
        'baseUrl'=>'/settlement',
        'beforeSend'=>'',
        'afterSend'=>''
    );

    public function transaction()
    {
        return new Transaction($this);
    }

    public function customer()
    {
        return new Customer($this);
    }

    public function subaccount()
    {
        return new SubAccount($this);
    }

    public function plan()
    {
        return new Plan($this);
    }

    public function subscription()
    {
        return new Subscription($this);
    }

    public function page()
    {
        return new Page($this);
    }

    public function settlement()
    {
        return new Settlement($this);
    }

    public function init()
    {
        parent::init();

        $this->setApiUrl();
        $this->setAuthorization();
    }

    protected function getSecretKey()
    {
        return (strtolower($this->environment) == strtolower(self::ENV_LIVE))?$this->liveSecretKey:$this->testSecretKey;
    }

    public function getAuthKeys()
    {
        if (strtolower($this->environment) == strtolower(self::ENV_LIVE))
        {
            return array('secret_key'=>$this->liveSecretKey,'public_key'=>$this->livePublicKey);
        }
        else
        {
            return array('secret_key'=>$this->testSecretKey,'public_key'=>$this->testPublicKey);
        }
    }

    protected function setAuthorization()
    {
        $this->authHeader = array('Authorization: Bearer '.$this->getSecretKey()/*,'Content-type: application/json'*/);
    }

    public function setHeader($headers = array())
    {
        $this->header = array_merge($this->authHeader,$headers);
    }

    public function getHeader()
    {
        return $this->header?$this->header:$this->authHeader;
    }

    protected function setApiUrl($url = null)
    {
        if ($url == null)
        {
            if (!isset($this->apiUrl) || empty($this->apiUrl))
                $this->apiUrl = $this->defaultApiUrl;
        }
        else
        {
            $this->apiUrl = $url;
        }
        return $this;
    }

    public function getApiUrl()
    {
        if (!isset($this->apiUrl) || empty($this->apiUrl))
            $this->apiUrl = $this->defaultApiUrl;
        return $this->apiUrl;
    }

    public static function generateRef($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}