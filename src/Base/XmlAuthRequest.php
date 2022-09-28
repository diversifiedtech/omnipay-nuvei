<?php

namespace Omnipay\Nuvei\Base;

use Omnipay\Nuvei\Base\Request;
use DOMDocument;
/**
 *  Used for processing XML Authorisations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
abstract class XmlAuthRequest extends Request
{
	protected $terminalId;
	protected $orderId;
	protected $currency;
	protected $amount = 0.00;
	public function Amount()
	{
		return $this->amount;
	}

    protected $terminalType = "2";

    protected $dateTime;
    protected $hash;

    protected $description;
    protected $email;
    protected $address1;
    protected $address2;
    protected $postCode;
    protected $phone;
    protected $country;
    protected $ipAddress;

    protected $city;
    protected $region;


    public function __construct(
        $terminalId,
        $orderId,
        $currency
    )
    {
        $this->dateTime = $this->GetFormattedDate();
        $this->terminalId = $terminalId;
        $this->orderId = $orderId;
        $this->currency = $currency;
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
     *  Setter for hash value
     *
     *  @param sharedSecret
     *  Shared secret either supplied by WorldNet TPS or configured under
     *  Terminal Settings in the Merchant Selfcare System.
     */
    public function SetHash($CARDNUMBER, $CARDEXPIRY, $CARDTYPE, $CARDHOLDERNAME, $sharedSecret)
    {
    	$this->hash = $this->GetRequestHash($this->terminalId . $this->orderId  . $this->dateTime . $CARDNUMBER.  $CARDEXPIRY . $CARDTYPE . $CARDHOLDERNAME . $sharedSecret);
    }

    public function toArray()
    {
        return [
            "MERCHANTREF" => $this->orderId,
            "TERMINALID" => $this->terminalId,
            // "AMOUNT" => $this->amount,
            // "CURRENCY" => $this->currency,
            // "DATETIME" => $this->dateTime,
            // "TERMINALTYPE" => $this->terminalType,
        ];
    }

    public abstract function GenerateXml();

    public static function toXml(array $data)
    {
        return [];
    }
}
