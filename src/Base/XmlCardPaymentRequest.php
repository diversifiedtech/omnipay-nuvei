<?php

namespace Omnipay\Nuvei\Base;

use DOMDocument;
use Omnipay\Nuvei\Base\Request;
use Omnipay\Nuvei\Base\XmlPaymentRequest;
/**
 *  Used for processing XML Authorisations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlCardPaymentRequest extends XmlPaymentRequest
{
	private $autoReady;
	private $cardNumber;
	private $trackData;
	private $cardType;
	private $cardExpiry;
	private $cardHolderName;
	private $cvv;
	private $issueNo;
	private $cardCurrency;
	private $cardAmount;
	private $conversionRate;
	// private $terminalType = "2";
	private $transactionType = "7";
	private $avsOnly;
	private $mpiRef;
	private $mobileNumber;
	private $deviceId;

    private $multicur = false;
    private $foreignCurInfoSet = false;

    /**
     *  Creates the standard request less optional parameters for processing an XML Transaction
     *  through the WorldNetTPS XML Gateway
     *
     *  @param terminalId Terminal ID provided by WorldNet TPS
     *  @param orderId A unique merchant identifier. Alpha numeric and max size 12 chars.
     *  @param currency ISO 4217 3 Digit Currency Code, e.g. EUR / USD / GBP
     *  @param amount Transaction Amount, Double formatted to 2 decimal places.
     *  @param description Transaction Description
     *  @param email Cardholder e-mail
     *  @param cardNumber A valid Card Number that passes the Luhn Check.
     *  @param cardType
     *  Card Type (Accepted Card Types must be configured in the Merchant Selfcare System.)
     *
     *  Accepted Values :
     *
     *  VISA
     *  MASTERCARD
     *  LASER
     *  SWITCH
     *  SOLO
     *  AMEX
     *  DINERS
     *  MAESTRO
     *  DELTA
     *  ELECTRON
     *
     */
    public function __construct(
    	$terminalId,
    	$orderId,
    	$currency,
    	$amount,
    	$cardNumber,
    	$cardType
    )
    {
        parent::__construct(
            $terminalId,
            $orderId,
            $currency,
            $amount
        );
        $this->cardNumber = $cardNumber;
        $this->cardType = $cardType;
    }

    /**
     *  Setter for Auto Ready Value
     *
     *  @param autoReady
     *  Auto Ready is an optional parameter and defines
     *  if the transaction should be settled automatically.
     *
     *  Accepted Values :
     *
     *  Y   -   Transaction will be settled in next batch
     *  N   -   Transaction will not be settled until user changes state in Merchant Selfcare Section
     */
    public function SetAutoReady($autoReady)
    {
    	$this->autoReady = $autoReady;
    }

    /**
     *  Setter for Card Expiry and Card Holder Name values
     *  These are mandatory for non-SecureCard transactions
     *
     *  @param cardExpiry Card Expiry formatted MMYY
     *  @param cardHolderName Card Holder Name
     */
    public function SetNonSecureCardCardInfo($cardExpiry, $cardHolderName)
    {
    	$this->cardExpiry = $cardExpiry;
    	$this->cardHolderName = $cardHolderName;
    }

    /**
     *  Setter for Card Verification Value
     *
     *  @param cvv Numeric field with a max of 4 characters.
     */
    public function SetCvv($cvv)
    {
    	$this->cvv = $cvv;
    }

    /**
     *  Setter for Issue No
     *
     *  @param issueNo Numeric field with a max of 3 characters.
     */
    public function SetIssueNo($issueNo)
    {
    	$this->issueNo = $issueNo;
    }

    /**
     *  Setter for Foreign Currency Information
     *
     *  @param cardCurrency ISO 4217 3 Digit Currency Code, e.g. EUR / USD / GBP
     *  @param cardAmount (Amount X Conversion rate) Formatted to two decimal places
     *  @param conversionRate Converstion rate supplied in rate response
     */
    public function SetForeignCurrencyInformation($cardCurrency, $cardAmount, $conversionRate)
    {
    	$this->cardCurrency = $cardCurrency;
    	$this->cardAmount = $cardAmount;
    	$this->conversionRate = $conversionRate;

    	$this->foreignCurInfoSet = true;
    }

    /**
	 *  Setter for AVS only flag
	 *
	 *  @param avsOnly Only perform an AVS check, do not store as a transaction. Possible values: "Y", "N"
	 */
    public function SetAvsOnly($avsOnly)
    {
    	$this->avsOnly = $avsOnly;
    }

    /**
     *  Setter for MPI Reference code
     *
     *  @param mpiRef MPI Reference code supplied by WorldNet TPS MPI redirect
     */
    public function SetMpiRef($mpiRef)
    {
    	$this->mpiRef = $mpiRef;
    }

    /**
     *  Setter for Mobile Number
     *
     *  @param mobileNumber Mobile Number of cardholder. If sent an SMS receipt will be sent to them
     */
    public function SetMobileNumber($mobileNumber)
    {
    	$this->mobileNumber = $mobileNumber;
    }

    /**
     *  Setter for Device ID
     *
     *  @param deviceId Device ID to identify this source to the XML gateway
     */
    public function SetDeviceId($deviceId)
    {
    	$this->deviceId = $deviceId;
    }

    /**
     *  Setter for multi-currency value
     *  This is required to be set for multi-currency terminals because the Hash is calculated differently.
     */
    public function SetMultiCur()
    {
    	$this->multicur = true;
    }

    /**
     *  Setter to flag transaction as a Mail Order. If not set the transaction defaults to eCommerce
     */
    public function SetMotoTrans()
    {
    	$this->terminalType = "1";
    	$this->transactionType = "4";
    }

    /**
     *  Setter to flag transaction as a Mail Order. If not set the transaction defaults to eCommerce
     */
    public function SetTrackData($trackData)
    {
    	$this->terminalType = "3";
    	$this->transactionType = "0";
    	$this->cardNumber = "";
    	$this->trackData = $trackData;
    }

    public function getExtras(){
        return [
            "ISSUENO" =>$this->issueNo,
            "AUTOREADY" => $this->autoReady,
            "AVSONLY" => $this->avsOnly,
            "MPIREF" => $this->mpiRef,
            "MOBILENUMBER" => $this->mobileNumber,
            "DEVICEID" => $this->deviceId,
            "PHONE" => $this->phone,
            "REGION" => $this->region,
            "CITY" => $this->city,
            "COUNTRY" => $this->country,
            "EMAIL" => $this->email,
            "IPADDRESS" => $this->ipAddress,
        ];
    }

    public function toArray()
    {

        $node = parent::toArray();
        $node["DATETIME"] = $this->dateTime;

        if ($this->trackData !== null) {
            $node["TRACKDATA"] = $this->trackData;
        } else {
            $node["CARDNUMBER"] = $this->cardNumber;
        }

        $node["CARDTYPE"] = $this->cardType;

        if ($this->cardExpiry !== null && $this->cardHolderName !== null && $this->trackData == null) {
            $node["CARDEXPIRY"] = $this->cardExpiry;

            $node["CARDHOLDERNAME"] = $this->cardHolderName;
        }

        $node["HASH"] = $this->hash;

        $node["CURRENCY"] = $this->currency;

        if ($this->foreignCurInfoSet) {
            $node["FOREIGNCURRENCYINFORMATION"] = [
                "CARDCURRENCY" => $this->cardCurrency,
                "CARDAMOUNT" => $this->cardAmount,
                "CONVERSIONRATE" => $this->conversionRate
            ];
        }

        $node["TERMINALTYPE"] = $this->terminalType;

        $node["TRANSACTIONTYPE"] = $this->transactionType;

        if ($this->cvv !== null) {
            $node["CVV"] = $this->cvv;
        }

        if ($this->postCode !== null) {
            if ($this->address1 !== null) {
                $node["ADDRESS1"] = $this->address1;
            }
            if ($this->address2 !== null) {
                $node["ADDRESS2"] = $this->address2;
            }

            $node["POSTCODE"] = $this->postCode;
        }

        if ($this->description !== null) {
            $node["DESCRIPTION"] = $this->description;
        }

        return $node;
    }

    public function GenerateXml(){
        return static::toXml($this->toArray());
    }

    public static function toXml(array $data)
    {
        $requestXML = new DOMDocument("1.0");
        $requestXML->formatOutput = true;

        $requestString = $requestXML->createElement("PAYMENT");
        $requestXML->appendChild($requestString);

        foreach($data as $KEY => $value){
            if($KEY === "FOREIGNCURRENCYINFORMATION"){
                $dcNode = $requestXML->createElement("FOREIGNCURRENCYINFORMATION");
                $requestString->appendChild($dcNode);

                $dcSubNode = $requestXML->createElement("CARDCURRENCY");
                $dcSubNode->appendChild($requestXML->createTextNode($value["CARDCURRENCY"]));
                $dcNode->appendChild($dcSubNode);

                $dcSubNode = $requestXML->createElement("CARDAMOUNT");
                $dcSubNode->appendChild($requestXML->createTextNode($value["CARDAMOUNT"]));
                $dcNode->appendChild($dcSubNode);

                $dcSubNode = $requestXML->createElement("CONVERSIONRATE");
                $dcSubNode->appendChild($requestXML->createTextNode($value["CONVERSIONRATE"]));
                $dcNode->appendChild($dcSubNode);
            }else{
                $node = $requestXML->createElement($KEY);
                $node->appendChild($requestXML->createTextNode($value));
                $requestString->appendChild($node);
            }
        }

        return $requestXML->saveXML();
    }
}
