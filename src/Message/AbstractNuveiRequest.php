<?php
/**
 * Nuvei Nuvei Abstract Request
 */

namespace Omnipay\Nuvei\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Nuvei\Ach;
use Omnipay\Nuvei\Base\Request;
use Omnipay\Nuvei\Base\XmlAchPaymentRequest;
use Omnipay\Nuvei\Base\XmlCardPaymentRequest;

/**
 * Nuvei Nuvei Abstract Request
 */
abstract class AbstractNuveiRequest extends \Omnipay\Common\Message\AbstractRequest
{

    const METHOD_POST = "POST";
    const CONTENT_TYPE = "application/xml";

    /** @var string live endpoint URL base */
    protected $liveEndpoint = 'https://payments.nuvei.com/merchant/xmlpayment';

    /** @var string test endpoint URL base */
    protected $testEndpoint = 'https://testpayments.nuvei.com/merchant/xmlpayment';


    /** @var string This is the 3 digit ISO currency code for the above Terminal ID */
    protected $currency = 'USA';

    /** @var string payment method */
    protected $paymentMethod = 'card';

    protected $sec_code = "WEB";

    //
    // Transaction types
    //
    const TRAN_PURCHASE                 = '00';
    const TRAN_PREAUTH                  = '01';
    const TRAN_PREAUTHCOMPLETE          = '02';
    const TRAN_FORCEDPOST               = '03';
    const TRAN_REFUND                   = '04';
    const TRAN_PREAUTHONLY              = '05';
    const TRAN_PAYPALORDER              = '07';
    const TRAN_VOID                     = '13';
    const TRAN_TAGGEDPREAUTHCOMPLETE    = '32';
    const TRAN_TAGGEDVOID               = '33';
    const TRAN_TAGGEDREFUND             = '34';
    const TRAN_CASHOUT                  = '83';
    const TRAN_ACTIVATION               = '85';
    const TRAN_BALANCEINQUIRY           = '86';
    const TRAN_RELOAD                   = '88';
    const TRAN_DEACTIVATION             = '89';

    /** @var array Names of the credit card types. */
    protected static $cardTypes = array(
        CreditCard::BRAND_VISA        => 'VISA',
        CreditCard::BRAND_MASTERCARD  => 'MASTERCARD',
        CreditCard::BRAND_MAESTRO     => "MAESTRO",
        CreditCard::BRAND_LASER       => "LASER",
        CreditCard::BRAND_AMEX        => "AMEX",
        CreditCard::BRAND_DINERS_CLUB => "DINERS",
        CreditCard::BRAND_JCB         => "JCB",
        CreditCard::BRAND_DISCOVER    => "DISCOVER"
    );

    public function makeTransactionId(){
        $value = substr(md5(microtime() . uniqid()), 0, 20);
        $this->setTransactionId($value);
        return $value;
    }

    /**
     * Get Terminal ID
     *
     * Calls to the API are secured with a terminal ID and
     * secret.
     *
     * @return string
     */
    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    /**
     * Set Terminal ID
     *
     * Calls to the API are secured with a terminal ID and
     * secret.
     *
     * @return Gateway provides a fluent interface.
     */
    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    /**
     * Get Secret
     *
     * Calls to the API are secured with a terminal ID and
     * secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * Set Secret
     *
     * Calls to the API are secured with a terminal ID and
     * secret.
     *
     * @return Gateway provides a fluent interface.
     */
    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    /**
     * Set transaction type
     *
     * @param int $transactionType
     *
     * @return NuveiAbstractRequest provides a fluent interface.
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;
        return $this;
    }

    /**
     * Get transaction type
     *
     * @return int
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * Set multicur
     *
     * If true the terminal is multicurrency
     */
    public function setMulticur($value)
    {
        return $this->setParameter('multicur', $value);
    }

    /**
     * Set sec code
     *
     * @param int $transactionType
     *
     * @return NuveiAbstractRequest provides a fluent interface.
     */
    public function setSecCode($sec_code)
    {
        $this->sec_code = $sec_code;
        return $this;
    }

    /**
     * Get sec code
     *
     * @return int
     */
    public function getSecCode()
    {
        return $this->sec_code;
    }

    /**
     * Get multicur
     *
     * multicur Number the uniquely entifies the transaction
     *
     * @return bool
     */
    public function getMulticur()
    {
        return $this->getParameter('multicur');
    }

    /**
     * Set autoReady
     *
     * Y or N. If this is set to Y Nuvei will automatically set the transaction to READY in the batch.
     * If set to N then the transaction will go to a
     * PENDING status. If not present the terminal default will be used.
     */
    public function setAutoReady($value)
    {
        return $this->setParameter('autoReady', $value);
    }

    /**
     * Get autoReady
     *
     * Y or N. If this is set to Y Nuvei will automatically set the transaction to READY in the batch.
     * If set to N then the transaction will go to a
     * PENDING status. If not present the terminal default will be used.
     *
     * @return bool
     */
    public function getAutoReady()
    {
        return $this->getParameter('autoReady');
    }

    /**
     * Set issueNo
     *
     * The issue no. of the card (Solo).
     *
     */
    public function setIssueNo($value)
    {
        return $this->setParameter('issueNo', $value);
    }

    /**
     * Get issueNo
     *
     * The issue no. of the card (Solo).
     *
     * @return bool
     */
    public function getIssueNo()
    {
        return $this->getParameter('issueNo');
    }

    /**
     * Set Mpiref
     *
     * 3D Secure Nuvei Transaction Reference supplied in Nuvei MPI transactions. Take a look at 3D Secure.
     *
     */
    public function setMpiref($value)
    {
        return $this->setParameter('mpiref', $value);
    }

    /**
     * Get Mpiref
     *
     * 3D Secure Nuvei Transaction Reference supplied in Nuvei MPI transactions. Take a look at 3D Secure.
     *
     * @return bool
     */
    public function getMpiref()
    {
        return $this->getParameter('mpiref');
    }

    /**
     * Set Device
     *
     * The unique identifier string for a connecting device.
     * Mandatory for non-server based devices such as handheld devices/cash register etc.
     *
     */
    public function setDevice($value)
    {
        return $this->setParameter('device', $value);
    }

    /**
     * Get Device
     *
     * The unique identifier string for a connecting device.
     * Mandatory for non-server based devices such as handheld devices/cash register etc.
     *
     * @return bool
     */
    public function getDevice()
    {
        return $this->getParameter('device');
    }
    /**
     * Set transaction type
     *
     * @param int $transactionType
     *
     * @return NuveiAbstractRequest provides a fluent interface.
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * Get transaction type
     *
     * @return int
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Sets the card.
     *
     * @param CreditCard $value
     * @return $this
     */
    public function setCard($value)
    {
        $card = parent::setCard($value);
        $this->setPaymentMethod('card');
        return $card;
    }

    /**
     * Get transaction type
     *
     * @return bool
     */
    public function isCard()
    {
        return $this->paymentMethod == 'card';
    }

    /**
     * Get transaction type
     *
     * @return bool
     */
    public function isAch()
    {
        return $this->paymentMethod == 'ach' || $this->paymentMethod == 'check';
    }

    /**
     * Get the ach.
     *
     * @return Ach
     */
    public function getAch()
    {
        return $this->getParameter('ach');
    }

    /**
     * Sets the ach.
     *
     * @param Ach $value
     * @return $this
     */
    public function setAch($value)
    {
        if ($value && !$value instanceof Ach) {
            $value = new Ach($value);
        }

        $ach = $this->setParameter('ach', $value);
        $this->setPaymentMethod('ach');
        return $ach;
    }

    /**
     * Get the ach.
     *
     * @return Ach
     */
    public function getCheck()
    {
        return $this->getAch();
    }

    /**
     * Sets the ach.
     *
     * @param Ach $value
     * @return $this
     */
    public function setCheck($value)
    {
        return $this->setAch($value);
    }

    /**
     * Get the card or ach depending on the payment method.
     *
     * @return CreditCard
     */
    public function getPaymentObject()
    {
        if($this->isCard()){
            return $this->getCard();
        }else if($this->isAch()){
            return $this->getAch();
        }
        throw new InvalidRequestException('Invalid Payment Method (Must be "card" or "check")');
    }


    /**
     * Get the base transaction data.
     *
     * @return array
     */
    protected function getBaseData()
    {
        return [
            'terminalId' => $this->getTerminalId(),
            'secret' => $this->getSecret(),
            'currency' => $this->getCurrency()
        ];
    }

    /**
     * Get the transaction headers.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'Content-Type' => self::CONTENT_TYPE
        ];
        // return array(
        //     'Content-Type'  => self::CONTENT_TYPE,
        //     'Accept'        => 'application/json'
        // );
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->setTransactionType($this->action);
        $data = $this->getBaseData();
        return $data;
    }

    /**
     * @param mixed $data
     *
     * @return NuveiResponse
     */
    public function sendData($data)
    {
        if($this->isCard()){
            $data = XmlCardPaymentRequest::toXml($data);
        }else if ($this->isAch()){
            $data = XmlAchPaymentRequest::toXml($data);
        }else{
            throw new InvalidRequestException('Invalid Payment Method (Must be "card" or "check")');
        }
        $headers  = $this->getHeaders();
        $endpoint = $this->getEndpoint();


        // var_dump($data);
        $httpResponse = $this->httpClient->request(
            "POST",
            $endpoint,
            $headers,
            $data
        );

        return $this->createResponse($httpResponse->getBody()->getContents());
    }

    /**
     * Get the endpoint URL for the request.
     *
     * @return string
     */
    protected function getEndpoint()
    {
        return ($this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint);
    }

    /**
     * Get the card type name, from the card type code.
     *
     * @param string $type
     *
     * @return string
     */
    public static function getCardType($type)
    {
        if (isset(self::$cardTypes[$type])) {
            return self::$cardTypes[$type];
        }
        return $type;
    }

    /**
     * Create the response object.
     *
     * @param $data
     *
     * @return NuveiResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new NuveiResponse($this, $data);
    }
}
