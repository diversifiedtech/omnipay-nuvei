<?php

namespace Omnipay\Nuvei\Base;

use Omnipay\Nuvei\Base\Request;
use DOMDocument;
/**
 *  Used for processing XML Authorisations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlPaymentRequest extends Request
{
	private $terminalId;
	private $orderId;
	private $currency;
	private $amount;
	public function Amount()
	{
		return $this->amount;
	}
	private $dateTime;
	private $hash;
	private $autoReady;
	private $description;
	private $email;
	private $cardNumber;
	private $trackData;
	private $cardType;
	private $cardExpiry;
	private $cardHolderName;
	private $cvv;
	private $issueNo;
	private $address1;
	private $address2;
	private $postCode;
	private $cardCurrency;
	private $cardAmount;
	private $conversionRate;
	private $terminalType = "2";
	private $transactionType = "7";
	private $avsOnly;
	private $mpiRef;
	private $mobileNumber;
	private $deviceId;
	private $phone;
	private $country;
	private $ipAddress;

    private $city;
    private $region;

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
    	$this->dateTime = $this->GetFormattedDate();

    	$this->terminalId = $terminalId;
    	$this->orderId = $orderId;
    	$this->currency = $currency;
    	$this->amount = $amount;
    	$this->cardNumber = $cardNumber;
    	$this->cardType = $cardType;
    }

    /**
     *  Setter for Auto Ready Value
     *
     *  @param autoReady
     *  Auto Ready is an optional parameter and defines if the transaction should be settled automatically.
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
     *  Setter for Email Address Value
     *
     *  @param email Alpha-numeric field.
     */
    public function SetEmail($email)
    {
    	$this->email = $email;
    }

    /**
     *  Setter for Email Address Value
     *
     *  @param email Alpha-numeric field.
     */
    public function SetDescription($description)
    {
    	$this->description = $description;
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
     *  Setter for Address Verification Values
     *
     *  @param address1 First Line of address - Max size 20
     *  @param address2 Second Line of address - Max size 20
     *  @param postCode Postcode - Max size 9
     */
    public function SetAvs($address1, $address2, $postCode)
    {
    	$this->address1 = $address1;
    	$this->address2 = $address2;
    	$this->postCode = $postCode;
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
     *  Setter for Phone number
     *
     *  @param phone Phone number of cardholder
     */
    public function SetPhone($phone)
    {
    	$this->phone = $phone;
    }

    /**
     *  Setter for the cardholders IP address
     *
     *  @param ipAddress IP Address of the cardholder
     */
    public function SetIPAddress($ipAddress)
    {
    	$this->ipAddress = $ipAddress;
    }

    /**
     *  Setter for Country
     *
     *  @param country Cardholders Country
     */
    public function SetCountry($country)
    {
    	$this->country = $country;
    }

    /**
     *  Setter for City
     *
     *  @param city Cardholders City
     */
    public function SetCity($city)
    {
        $this->city = $city;
    }

    /**
     *  Setter for Region
     *
     *  @param Region Cardholders Region
     */
    public function SetRegion($region)
    {
        $this->region = $region;
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

    /**
     *  Setter for hash value
     *
     *  @param sharedSecret
     *  Shared secret either supplied by WorldNet TPS or configured under
     *  Terminal Settings in the Merchant Selfcare System.
     */
    public function SetHash($sharedSecret)
    {
    	if (isset($this->multicur) && $this->multicur == true) {
    		$this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->currency . $this->amount . $this->dateTime . $sharedSecret);
    	} else {
    		$this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->amount . $this->dateTime . $sharedSecret);
    	}
    }

    /**
     *  (Old) Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
     *
     *  @param sharedSecret
     *  Shared secret either supplied by WorldNet TPS or configured under
     *  Terminal Settings in the Merchant Selfcare System.
     *
     *  @param testAccount
     *  Boolean value defining Mode
     *  true - This is a test account
     *  false - Production mode, all transactions will be processed by Issuer.
     *
     *  @return XmlPaymentResponse containing an error or the parsed payment response.
     */
    public function ProcessRequest($sharedSecret, $testAccount)
    {
    	return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
    }

    public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
    {
    	$this->SetHash($sharedSecret);
    	$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
    	$response = new XmlPaymentResponse($responseString);
    	return $response;
    }

    public function getOptionals(){
        return [
            "CVV" => $this->cvv,
            "DESCRIPTION" => $this->description,
        ];
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
        $node = [];

        $node["ORDERID"] = $this->orderId;

        $node["TERMINALID"] = $this->terminalId;

        $node["AMOUNT"] = $this->amount;

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

    //     $node = $requestXML->createElement("ORDERID");
    //     $node->appendChild($requestXML->createTextNode($this->orderId));
    //     $requestString->appendChild($node);

    //     $node = $requestXML->createElement("TERMINALID");
    //     $requestString->appendChild($node);
    //     $node->appendChild($requestXML->createTextNode($this->terminalId));

    //     $node = $requestXML->createElement("AMOUNT");
    //     $requestString->appendChild($node);
    //     $node->appendChild($requestXML->createTextNode($this->amount));

    //     $node = $requestXML->createElement("DATETIME");
    //     $requestString->appendChild($node);
    //     $node->appendChild($requestXML->createTextNode($this->dateTime));

    //     if ($this->trackData !== null) {
    //       $node = $requestXML->createElement("TRACKDATA");
    //       $requestString->appendChild($node);
    //       $node->appendChild($requestXML->createTextNode($this->trackData));
    //   } else {
    //       $node = $requestXML->createElement("CARDNUMBER");
    //       $requestString->appendChild($node);
    //       $node->appendChild($requestXML->createTextNode($this->cardNumber));
    //   }

    //   $node = $requestXML->createElement("CARDTYPE");
    //   $requestString->appendChild($node);
    //   $node->appendChild($requestXML->createTextNode($this->cardType));

    //   if ($this->cardExpiry !== null && $this->cardHolderName !== null && $this->trackData == null) {
    //       $node = $requestXML->createElement("CARDEXPIRY");
    //       $requestString->appendChild($node);
    //       $node->appendChild($requestXML->createTextNode($this->cardExpiry));

    //       $node = $requestXML->createElement("CARDHOLDERNAME");
    //       $requestString->appendChild($node);
    //       $node->appendChild($requestXML->createTextNode($this->cardHolderName));
    //   }

    //   $node = $requestXML->createElement("HASH");
    //   $requestString->appendChild($node);
    //   $node->appendChild($requestXML->createTextNode($this->hash));

    //   $node = $requestXML->createElement("CURRENCY");
    //   $requestString->appendChild($node);
    //   $node->appendChild($requestXML->createTextNode($this->currency));

    //   if ($this->foreignCurInfoSet) {
    //       $dcNode = $requestXML->createElement("FOREIGNCURRENCYINFORMATION");
    //       $requestString->appendChild($dcNode);

    //       $dcSubNode = $requestXML->createElement("CARDCURRENCY");
    //       $dcSubNode ->appendChild($requestXML->createTextNode($this->cardCurrency));
    //       $dcNode->appendChild($dcSubNode);

    //       $dcSubNode = $requestXML->createElement("CARDAMOUNT");
    //       $dcSubNode ->appendChild($requestXML->createTextNode($this->cardAmount));
    //       $dcNode->appendChild($dcSubNode);

    //       $dcSubNode = $requestXML->createElement("CONVERSIONRATE");
    //       $dcSubNode ->appendChild($requestXML->createTextNode($this->conversionRate));
    //       $dcNode->appendChild($dcSubNode);
    //   }

    //   $node = $requestXML->createElement("TERMINALTYPE");
    //   $requestString->appendChild($node);
    //   $nodeText = $requestXML->createTextNode($this->terminalType);
    //   $node->appendChild($nodeText);

    //   $node = $requestXML->createElement("TRANSACTIONTYPE");
    //   $requestString->appendChild($node);
    //   $nodeText = $requestXML->createTextNode($this->transactionType);
    //   $node->appendChild($nodeText);

    //   if ($this->cvv !== null) {
    //     $node = $requestXML->createElement("CVV");
    //     $requestString->appendChild($node);
    //     $node->appendChild($requestXML->createTextNode($this->cvv));
    // }

    // if ($this->postCode !== null) {
    //     if ($this->address1 !== null) {
    //         $node = $requestXML->createElement("ADDRESS1");
    //         $requestString->appendChild($node);
    //         $node->appendChild($requestXML->createTextNode($this->address1));
    //     }
    //     if ($this->address2 !== null) {
    //         $node = $requestXML->createElement("ADDRESS2");
    //         $requestString->appendChild($node);
    //         $node->appendChild($requestXML->createTextNode($this->address2));
    //     }

    //     $node = $requestXML->createElement("POSTCODE");
    //     $requestString->appendChild($node);
    //     $node->appendChild($requestXML->createTextNode($this->postCode));
    // }

    // if ($this->description !== null) {
    //     $node = $requestXML->createElement("DESCRIPTION");
    //     $requestString->appendChild($node);
    //     $node->appendChild($requestXML->createTextNode($this->description));
    // }

        return $requestXML->saveXML();
    }
}
