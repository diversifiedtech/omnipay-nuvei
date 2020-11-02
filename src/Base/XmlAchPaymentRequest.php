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
class XmlAchPaymentRequest extends XmlPaymentRequest
{
    const ROOT_NODE = "PAYMENTACH";
    private $sec_code = "WEB";
    private $account_type;
    private $account_number;
    private $routing_number;
    private $account_name;
    private $check_number;
    private $dl_state;
    private $dl_number;

    /**
     *  Creates the standard request less optional parameters for processing an XML Transaction
     *  through the WorldNetTPS XML Gateway
     *
     *  @param terminalId Terminal ID provided by WorldNet TPS
     *  @param orderId A unique merchant identifier. Alpha numeric and max size 12 chars.
     *  @param currency ISO 4217 3 Digit Currency Code, e.g. EUR / USD / GBP
     *  @param amount Transaction Amount, Double formatted to 2 decimal places.
     *  @param account_number
     *  @param routing_number
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
    	$account_number,
    	$routing_number,
        $account_name
    )
    {
        parent::__construct($terminalId,
            $orderId,
            $currency,
            $amount
        );
        $this->account_number = $account_number;
        $this->routing_number = $routing_number;
        $this->account_name = $account_name;
    }

    /**
     *  Setter for SEC_CODE
     *
     *  @param sec_code
     */
    public function SetSecCode($sec_code)
    {
        $this->sec_code = $sec_code;
    }

    /**
     *  Setter for account type
     *
     * CHECKING or SAVINGS. Required if ACH_SECURE=N.
     *
     *  @param account_type
     */
    public function SetAccountType($account_type)
    {
        $this->account_type = $account_type;
    }

    /**
     *  Setter for account number
     *
     *  The ACH account number
     *
     *  @param account_type
     */
    public function SetAccountNumber($account_number)
    {
        $this->account_number = $account_number;
    }

    /**
     *  Setter for routing number
     *
     *  The ACH routing number.
     *
     *  @param account_type
     */
    public function SetRoutingNumber($routing_number)
    {
        $this->routing_number = $routing_number;
    }

    /**
     *  Setter for acount name
     *
     *  The customer’s first and last name.
     *
     *  @param account_type
     */
    public function SetAccountName($account_name)
    {
        $this->account_name = $account_name;
    }

    /**
     *  Setter for check number
     *
     *  Check number
     *
     *  @param account_type
     */
    public function SetCheckNumber($check_number)
    {
        $this->check_number = $check_number;
    }

    /**
     *  Setter for drivers license state
     *
     *  Check number
     *
     *  @param account_type
     */
    public function SetLicenseState($dl_state)
    {
        $this->dl_state = $dl_state;
    }

    /**
     *  Setter for drivers license number
     *
     *  Check number
     *
     *  @param account_type
     */
    public function SetLicenseNumber($dl_number)
    {
        $this->dl_number = $dl_number;
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
        $node["CURRENCY"] = $this->currency;
        $node["DATETIME"] = $this->dateTime;
        $node["TERMINALTYPE"] = $this->terminalType;

        $node["SEC_CODE"] = $this->sec_code;
        $node["ACCOUNT_TYPE"] = $this->account_type;
        $node["ACCOUNT_NUMBER"] = $this->account_number;
        $node["ROUTING_NUMBER"] = $this->routing_number;
        $node["ACCOUNT_NAME"] = $this->account_name;

        if ($this->check_number !== null) {
            $node["CHECK_NUMBER"] = $this->check_number;
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

        //NO CITY OR REGION

        if ($this->country !== null) {
            $node["COUNTRY"] = $this->country;
        }
        if ($this->phone !== null) {
            $node["PHONE"] = $this->phone;
        }
        if ($this->ipAddress !== null) {
            $node["IPADDRESS"] = $this->ipAddress;
        }
        if ($this->email !== null) {
            $node["EMAIL"] = $this->email;
        }



        if ($this->description !== null) {
            $node["DESCRIPTION"] = $this->description;
        }


        if ($this->dl_number !== null) {
            if ($this->dl_state !== null) {
                $node["DL_STATE"] = $this->dl_state;
            }
            if ($this->dl_number !== null) {
                $node["DL_NUMBER"] = $this->dl_number;
            }
        }

        $node["HASH"] = $this->hash;
        return $node;
    }

    public function GenerateXml(){
        return static::toXml($this->toArray());
    }

    public static function toXml(array $data)
    {
        $requestXML = new DOMDocument("1.0");
        $requestXML->formatOutput = true;

        $requestString = $requestXML->createElement(static::ROOT_NODE);
        $requestXML->appendChild($requestString);

        foreach($data as $KEY => $value){
            $node = $requestXML->createElement($KEY);
            $node->appendChild($requestXML->createTextNode($value));
            $requestString->appendChild($node);
        }
        return $requestXML->saveXML();
    }

}
