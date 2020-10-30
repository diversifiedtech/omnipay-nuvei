<?php
/*
    Copyright 2006-2014 WorldNet TPS Ltd.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(basename(__FILE__) == basename($_SERVER["REQUEST_URI"])) die("<b>ERROR: You cannot display this file directly.</b>");

/**
 * Base Request Class holding common functionality for Request Types.
 */
class Request
{
		protected static function GetRequestHash($plainString)
		{
				return md5($plainString);
		}

		protected static function GetFormattedDate()
		{
				return date('d-m-Y:H:i:s:000');
		}

		protected static function SendRequestToGateway($requestString, $testAccount, $gateway)
		{
				$serverUrl = 'https://';
				if($testAccount) $serverUrl .= 'test';
				switch (strtolower($gateway)) {
					default :
					case 'worldnet'  : $serverUrl .= 'payments.worldnettps.com'; break;
					case 'cashflows' : $serverUrl .= 'cashflows.worldnettps.com'; break;
					case 'payius' : $serverUrl .= 'payments.payius.com'; break;
					case 'pago' : $serverUrl .= 'payments.pagotechnology.com'; break;
					case 'nuvei' : $serverUrl .= 'payments.nuvei.com'; break;
				}
				$XMLSchemaFile = $serverUrl . '/merchant/gateway.xsd';
				$serverUrl .= '/merchant/xmlpayment';

				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;
				$requestXML->loadXML($requestString);
				if(!$requestXML->schemaValidate($XMLSchemaFile)) die('<b>XML VALIDATION FAILED AGAINST SCHEMA:</b>' . $XMLSchemaFile . libxml_display_errors());
				unset($requestXML);

				// Initialisation
				$ch=curl_init();
				// Set parameters
				curl_setopt($ch, CURLOPT_URL, $serverUrl);
				// Return a variable instead of posting it directly
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			//	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
				// Activate the POST method
				curl_setopt($ch, CURLOPT_POST, 1);
				// Request
				curl_setopt($ch, CURLOPT_POSTFIELDS, $requestString);
				// execute the connection
				$result = curl_exec($ch);
				// Close it
				curl_close($ch);
				return $result;
		}
}

/**
 *  Used for processing XML Authorisations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlAuthRequest extends Request
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
		public function XmlAuthRequest($terminalId,
				$orderId,
				$currency,
				$amount,
				$cardNumber,
				$cardType)
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
				if(isset($this->multicur) && $this->multicur == true) $this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->currency . $this->amount . $this->dateTime . $sharedSecret);
				else $this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->amount . $this->dateTime . $sharedSecret);
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
		 *  @return XmlAuthResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlAuthResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("PAYMENT");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("ORDERID");
				$node->appendChild($requestXML->createTextNode($this->orderId));
				$requestString->appendChild($node);

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("AMOUNT");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->amount));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				if($this->trackData !== NULL)
				{
					$node = $requestXML->createElement("TRACKDATA");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->trackData));
				} else {
					$node = $requestXML->createElement("CARDNUMBER");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cardNumber));
				}

				$node = $requestXML->createElement("CARDTYPE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardType));

				if($this->cardExpiry !== NULL && $this->cardHolderName !== NULL && $this->trackData == NULL)
				{
					$node = $requestXML->createElement("CARDEXPIRY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cardExpiry));

					$node = $requestXML->createElement("CARDHOLDERNAME");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cardHolderName));
				}

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				$node = $requestXML->createElement("CURRENCY");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->currency));

				if($this->foreignCurInfoSet)
				{
					$dcNode = $requestXML->createElement("FOREIGNCURRENCYINFORMATION");
					$requestString->appendChild($dcNode );

					$dcSubNode = $requestXML->createElement("CARDCURRENCY");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->cardCurrency));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CARDAMOUNT");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->cardAmount));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CONVERSIONRATE");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->conversionRate));
					$dcNode->appendChild($dcSubNode);
				}

				$node = $requestXML->createElement("TERMINALTYPE");
				$requestString->appendChild($node);
				$nodeText = $requestXML->createTextNode($this->terminalType);
				$node->appendChild($nodeText);

				$node = $requestXML->createElement("TRANSACTIONTYPE");
				$requestString->appendChild($node);
				$nodeText = $requestXML->createTextNode($this->transactionType);
				$node->appendChild($nodeText);

				if($this->autoReady !== NULL)
				{
					$node = $requestXML->createElement("AUTOREADY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->autoReady));
				}

				if($this->email !== NULL)
				{
					$node = $requestXML->createElement("EMAIL");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->email));
				}

				if($this->cvv !== NULL)
				{
					$node = $requestXML->createElement("CVV");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cvv));
				}

				if($this->issueNo !== NULL)
				{
					$node = $requestXML->createElement("ISSUENO");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->issueNo));
				}

				if($this->postCode !== NULL)
				{
					if($this->address1 !== NULL)
					{
						$node = $requestXML->createElement("ADDRESS1");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->address1));
					}
					if($this->address2 !== NULL)
					{
						$node = $requestXML->createElement("ADDRESS2");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->address2));
					}

					$node = $requestXML->createElement("POSTCODE");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->postCode));
				}

				if($this->avsOnly !== NULL)
				{
					$node = $requestXML->createElement("AVSONLY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->avsOnly));
				}

				if($this->description !== NULL)
				{
					$node = $requestXML->createElement("DESCRIPTION");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->description));
				}

				if($this->mpiRef !== NULL)
				{
					$node = $requestXML->createElement("MPIREF");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->mpiRef));
				}

				if($this->mobileNumber !== NULL)
				{
					$node = $requestXML->createElement("MOBILENUMBER");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->mobileNumber));
				}

				if($this->deviceId !== NULL)
				{
					$node = $requestXML->createElement("DEVICEID");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->deviceId));
				}

				if($this->phone !== NULL)
				{
					$node = $requestXML->createElement("PHONE");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->phone));
				}

				if($this->country !== NULL)
				{
					$node = $requestXML->createElement("COUNTRY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->country));
				}

				if($this->ipAddress !== NULL)
				{
					$node = $requestXML->createElement("IPADDRESS");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->ipAddress));
				}

                return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML Refund Authorisations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation. There are no coptional fields.
 */
class XmlRefundRequest extends Request
{
		private $terminalId;
		private $orderId;
		private $uniqueRef;
		private $amount;
		public function Amount()
		{
				return $this->amount;
		}
		private $dateTime;
		private $hash;
		private $operator;
		private $reason;
		private $autoReady;

		/**
		 *  Creates the refund request for processing an XML Transaction
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 *  @param orderId A unique merchant identifier. Alpha numeric and max size 12 chars.
		 *  @param currency ISO 4217 3 Digit Currency Code, e.g. EUR / USD / GBP
		 *  @param amount Transaction Amount, Double formatted to 2 decimal places.
		 *  @param operator An identifier for who executed this transaction
		 *  @param reason The reason for the refund
		 */
		public function XmlRefundRequest($terminalId,
				$orderId,
				$amount,
				$operator,
				$reason)
		{
				$this->dateTime = $this->GetFormattedDate();
             			$this->amount = $amount;
				$this->terminalId = $terminalId;
				$this->orderId = $orderId;
				$this->operator = $operator;
				$this->reason = $reason;
		}
	   /**
		 *  Setter for UniqueRef

		 *
		 *  @param uniqueRef
		 *  Unique Reference of transaction returned from gateway in authorisation response
		 */
		public function SetUniqueRef($uniqueRef)
		{
				$this->uniqueRef = $uniqueRef;
				$this->orderId = "";
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
		 *  Setter for hash value
		 *
		 *  @param sharedSecret
		 *  Shared secret either supplied by WorldNet TPS or configured under
		 *  Terminal Settings in the Merchant Selfcare System.
		 */
		public function SetHash($sharedSecret)
		{
				if($this->uniqueRef !== NULL)
				{
						$this->hash = $this->GetRequestHash($this->terminalId . $this->uniqueRef . $this->amount . $this->dateTime . $sharedSecret);
				} else {
						$this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->amount . $this->dateTime . $sharedSecret);
				}
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlRefundResponse containing an error or the parsed refund response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}
		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlRefundResponse($responseString);
				return $response;
		}
		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("REFUND");
				$requestXML->appendChild($requestString);

				if($this->uniqueRef !== NULL)
				{
						$node = $requestXML->createElement("UNIQUEREF");
						$node->appendChild($requestXML->createTextNode($this->uniqueRef));
						$requestString->appendChild($node);
				} else {
						$node = $requestXML->createElement("ORDERID");
						$node->appendChild($requestXML->createTextNode($this->orderId));
						$requestString->appendChild($node);
				}

				$node = $requestXML->createElement("TERMINALID");
				$node->appendChild($requestXML->createTextNode($this->terminalId));
				$requestString->appendChild($node);

				$node = $requestXML->createElement("AMOUNT");
				$node->appendChild($requestXML->createTextNode($this->amount));
				$requestString->appendChild($node);

				$node = $requestXML->createElement("DATETIME");
				$node->appendChild($requestXML->createTextNode($this->dateTime));
				$requestString->appendChild($node);

				$node = $requestXML->createElement("HASH");
				$node->appendChild($requestXML->createTextNode($this->hash));
				$requestString->appendChild($node);

				$node = $requestXML->createElement("OPERATOR");
				$node->appendChild($requestXML->createTextNode($this->operator));
				$requestString->appendChild($node);

				$node = $requestXML->createElement("REASON");
				$node->appendChild($requestXML->createTextNode($this->reason));
				$requestString->appendChild($node);

				if($this->autoReady !== NULL)
				{
					$node = $requestXML->createElement("AUTOREADY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->autoReady));
				}

				return $requestXML->saveXML();

		}
}

/**
 *  Used for processing XML Pre-Authorisations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlPreAuthRequest extends Request
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
		private $description;
		private $email;
		private $cardNumber;
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
		private $multicur = false;
		private $foreignCurInfoSet = false;
		private $ipAddress;

		/**
		 *  Creates the pre-auth request less optional parameters for processing an XML Transaction
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
		 *  @param cardExpiry Card Expiry formatted MMYY
		 *  @param cardHolderName Card Holder Name
		 */
		public function XmlPreAuthRequest($terminalId,
				$orderId,
				$currency,
				$amount,
				$cardNumber,
				$cardType)
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
		 *  Setter for Card Verification Value
		 *
		 *  @param cvv Numeric field with a max of 4 characters.
		 */
		public function SetCvv($cvv)
		{
				$this->cvv = $cvv;
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
		 *  @param email Alpha-numeric field.
		 */
		public function SetNonSecureCardCardInfo($cardExpiry, $cardHolderName)
		{
				$this->cardExpiry = $cardExpiry;
				$this->cardHolderName = $cardHolderName;
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
		 *  Setter for the cardholders IP address
		 *
		 *  @param ipAddress IP Address of the cardholder
		 */
		public function SetIPAddress($ipAddress)
		{
				$this->ipAddress = $ipAddress;
		}
	   /**
		 *  Setter for Multicurrency value
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
		 *  Setter for hash value
		 *
		 *  @param sharedSecret
		 *  Shared secret either supplied by WorldNet TPS or configured under
		 *  Terminal Settings in the Merchant Selfcare System.
		 */
		public function SetHash($sharedSecret)
		{
				if(isset($this->multicur) && $this->multicur == true) $this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->currency . $this->amount . $this->dateTime . $sharedSecret);
				else $this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->amount . $this->dateTime . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlPreAuthResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlPreAuthResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("PREAUTH");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("ORDERID");
				$node->appendChild($requestXML->createTextNode($this->orderId));
				$requestString->appendChild($node);

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("AMOUNT");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->amount));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("CARDNUMBER");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardNumber));

				$node = $requestXML->createElement("CARDTYPE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardType));

				if($this->cardExpiry !== NULL && $this->cardHolderName !== NULL) {
					$node = $requestXML->createElement("CARDEXPIRY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cardExpiry));

					$node = $requestXML->createElement("CARDHOLDERNAME");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cardHolderName));
				}

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				$node = $requestXML->createElement("CURRENCY");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->currency));

				if($this->foreignCurInfoSet)
				{
					$dcNode = $requestXML->createElement("FOREIGNCURRENCYINFORMATION");
					$requestString->appendChild($dcNode );

					$dcSubNode = $requestXML->createElement("CARDCURRENCY");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->cardCurrency));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CARDAMOUNT");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->cardAmount));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CONVERSIONRATE");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->conversionRate));
					$dcNode->appendChild($dcSubNode);
				}

				$node = $requestXML->createElement("TERMINALTYPE");
				$requestString->appendChild($node);
				$nodeText = $requestXML->createTextNode($this->terminalType);
				$node->appendChild($nodeText);

				$node = $requestXML->createElement("TRANSACTIONTYPE");
				$requestString->appendChild($node);
				$nodeText = $requestXML->createTextNode($this->transactionType);
				$node->appendChild($nodeText);

				if($this->email !== NULL)
				{
					$node = $requestXML->createElement("EMAIL");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->email));
				}

				if($this->cvv !== NULL)
				{
					$node = $requestXML->createElement("CVV");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cvv));
				}

				if($this->issueNo !== NULL)
				{
					$node = $requestXML->createElement("ISSUENO");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->issueNo));
				}

				if($this->postCode !== NULL)
				{
					if($this->address1 !== NULL)
					{
						$node = $requestXML->createElement("ADDRESS1");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->address1));
					}
					if($this->address2 !== NULL)
					{
						$node = $requestXML->createElement("ADDRESS2");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->address2));
					}

					$node = $requestXML->createElement("POSTCODE");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->postCode));
				}

				if($this->description !== NULL)
				{
					$node = $requestXML->createElement("DESCRIPTION");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->description));
				}

				if($this->ipAddress !== NULL)
				{
					$node = $requestXML->createElement("IPADDRESS");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->ipAddress));
				}

				return $requestXML->saveXML();

		}
}

/**
 *  Used for processing XML PreAuthorisation Completions through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlPreAuthCompletionRequest extends Request
{
		private $terminalId;
		private $orderId;
		private $uniqueRef;
		private $amount;
		public function Amount()
		{
				return $this->amount;
		}
		private $dateTime;
		private $hash;
		private $description;
		private $cvv;
		private $cardCurrency;
		private $cardAmount;
		private $conversionRate;
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
		 *  @param cardExpiry Card Expiry formatted MMYY
		 *  @param cardHolderName Card Holder Name
		 */
		public function XmlPreAuthCompletionRequest($terminalId,
				$orderId,
				$amount)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->terminalId = $terminalId;
				$this->orderId = $orderId;
				$this->amount = $amount;
		}
	   /**
		 *  Setter for UniqueRef

		 *
		 *  @param uniqueRef
		 *  Unique Reference of transaction returned from gateway in authorisation response
		 */
		public function SetUniqueRef($uniqueRef)
		{
				$this->uniqueRef = $uniqueRef;
				$this->orderId = "";
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
		 *  Setter for transaction description
		 *
		 *  @param cvv Discretionary text value
		 */
		public function SetDescription($description)
		{
				$this->description = $description;
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


		 *  Setter for hash value
		 *
		 *  @param sharedSecret
		 *  Shared secret either supplied by WorldNet TPS or configured under
		 *  Terminal Settings in the Merchant Selfcare System.
		 */
		public function SetHash($sharedSecret)
		{
				if($this->uniqueRef !== NULL)
				{
						$this->hash = $this->GetRequestHash($this->terminalId . $this->uniqueRef. $this->amount . $this->dateTime . $sharedSecret);
				} else {
						$this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->amount . $this->dateTime . $sharedSecret);
				}
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlPreAuthCompletionResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlPreAuthCompletionResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("PREAUTHCOMPLETION");
				$requestXML->appendChild($requestString);

				if($this->uniqueRef !== NULL)
				{
						$node = $requestXML->createElement("UNIQUEREF");
						$node->appendChild($requestXML->createTextNode($this->uniqueRef));
						$requestString->appendChild($node);
				} else {
						$node = $requestXML->createElement("ORDERID");
						$node->appendChild($requestXML->createTextNode($this->orderId));
						$requestString->appendChild($node);
				}

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("AMOUNT");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->amount));

				if($this->foreignCurInfoSet)
				{
					$dcNode = $requestXML->createElement("FOREIGNCURRENCYINFORMATION");
					$requestString->appendChild($dcNode );

					$dcSubNode = $requestXML->createElement("CARDCURRENCY");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->cardCurrency));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CARDAMOUNT");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->cardAmount));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CONVERSIONRATE");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->conversionRate));
					$dcNode->appendChild($dcSubNode);
				}

				if($this->description !== NULL)
				{
    				$node = $requestXML->createElement("DESCRIPTION");
    				$requestString->appendChild($node);
    				$nodeText = $requestXML->createTextNode($this->description);
    				$node->appendChild($nodeText);
                }

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				if($this->cvv !== NULL)
				{
					$node = $requestXML->createElement("CVV");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cvv));
				}

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();

		}
}

/**
 *  Used for processing XML PreAuthorisation Completions through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlRateRequest extends Request
{
		private $terminalId;
		private $cardBin;
		private $baseAmount;

		/**
		 *  Creates the rate request for processing an XML Transaction
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 *  @param cardBin First 6 digits of the card number
		 */
		public function XmlRateRequest($terminalId,
				$cardBin)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->terminalId = $terminalId;
				$this->cardBin = $cardBin;
		}
		/**
		 *  Setter for Card Verification Value
		 *
		 *  @param cvv Numeric field with a max of 4 characters.
		 */
		public function SetBaseAmount($baseAmount)
		{
				$this->baseAmount = $baseAmount;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->cardBin . $this->dateTime . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlRateResponse containing an error or the parsed response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlRateResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("GETCARDCURRENCYRATE");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("CARDBIN");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardBin));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				if($this->baseAmount != NULL )
				{
					$node = $requestXML->createElement("BASEAMOUNT");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->baseAmount));
				}

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML SecureCard Registrations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlSecureCardRegRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $cardNumber;
		private $cardExpiry;
		private $cardHolderName;
		private $dateTime;
		private $hash;
		private $dontCheckSecurity;
		private $cvv;
		private $issueNo;

		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique card identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS
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
		 *  @param cardExpiry Card Expiry formatted MMYY
		 *  @param cardHolderName Card Holder Name
		 */
		public function XmlSecureCardRegRequest($merchantRef,
				$terminalId,
				$cardNumber,
				$cardExpiry,
				$cardType,
				$cardHolderName)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;
				$this->cardNumber = $cardNumber;
				$this->cardExpiry = $cardExpiry;
				$this->cardType = $cardType;
				$this->cardHolderName = $cardHolderName;
		}
	   /**
		 *  Setter for dontCheckSecurity setting
		 *
		 *  @param dontCheckSecurity can be either "Y" or "N".
		 */
		public function SetDontCheckSecurity($dontCheckSecurity)
		{
				$this->dontCheckSecurity = $dontCheckSecurity;
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
		 *  Setter for hash value
		 *
		 *  @param sharedSecret
		 *  Shared secret either supplied by WorldNet TPS or configured under
		 *  Terminal Settings in the Merchant Selfcare System.
		 */
		public function SetHash($sharedSecret)
		{
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->dateTime . $this->cardNumber . $this->cardExpiry . $this->cardType . $this->cardHolderName . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlSecureCardRegResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("SECURECARDREGISTRATION");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("CARDNUMBER");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardNumber));

				$node = $requestXML->createElement("CARDEXPIRY");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardExpiry));

				$node = $requestXML->createElement("CARDTYPE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardType));

				$node = $requestXML->createElement("CARDHOLDERNAME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardHolderName));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				if($this->dontCheckSecurity !== NULL)
				{
					$node = $requestXML->createElement("DONTCHECKSECURITY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->dontCheckSecurity));
				}

				if($this->cvv !== NULL)
				{
					$node = $requestXML->createElement("CVV");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cvv));
				}

				if($this->issueNo !== NULL)
				{
					$node = $requestXML->createElement("ISSUENO");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->issueNo));
				}

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML SecureCard Registrations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlSecureCardUpdRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $cardNumber;
		private $cardExpiry;
		private $cardHolderName;
		private $dateTime;
		private $hash;
		private $dontCheckSecurity;
		private $cvv;
		private $issueNo;

		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique card identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS
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
		 *  @param cardExpiry Card Expiry formatted MMYY
		 *  @param cardHolderName Card Holder Name
		 */
		public function XmlSecureCardUpdRequest($merchantRef,
				$terminalId,
				$cardNumber,
				$cardExpiry,
				$cardType,
				$cardHolderName)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;
				$this->cardNumber = $cardNumber;
				$this->cardExpiry = $cardExpiry;
				$this->cardType = $cardType;
				$this->cardHolderName = $cardHolderName;
		}
	   /**
		 *  Setter for dontCheckSecurity setting
		 *
		 *  @param dontCheckSecurity can be either "Y" or "N".
		 */
		public function SetDontCheckSecurity($dontCheckSecurity)
		{
				$this->dontCheckSecurity = $dontCheckSecurity;
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
		 *  Setter for hash value
		 *
		 *  @param sharedSecret
		 *  Shared secret either supplied by WorldNet TPS or configured under
		 *  Terminal Settings in the Merchant Selfcare System.
		 */
		public function SetHash($sharedSecret)
		{
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->dateTime . $this->cardNumber . $this->cardExpiry . $this->cardType . $this->cardHolderName . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlSecureCardUpdResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("SECURECARDUPDATE");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("CARDNUMBER");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardNumber));

				$node = $requestXML->createElement("CARDEXPIRY");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardExpiry));

				$node = $requestXML->createElement("CARDTYPE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardType));

				$node = $requestXML->createElement("CARDHOLDERNAME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->cardHolderName));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				if($this->dontCheckSecurity !== NULL)
				{
					$node = $requestXML->createElement("DONTCHECKSECURITY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->dontCheckSecurity));
				}

				if($this->cvv !== NULL)
				{
					$node = $requestXML->createElement("CVV");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->cvv));
				}

				if($this->issueNo !== NULL)
				{
					$node = $requestXML->createElement("ISSUENO");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->issueNo));
				}

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML SecureCard deletion through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlSecureCardDelRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $secureCardCardRef;
		private $dateTime;
		private $hash;

		/**
		 *  Creates the SecureCard searche request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique card identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 */
		public function XmlSecureCardDelRequest($merchantRef,
				$terminalId,
				$secureCardCardRef)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;
				$this->secureCardCardRef = $secureCardCardRef;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->dateTime . $this->secureCardCardRef . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlSecureCardDelResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("SECURECARDREMOVAL");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("CARDREFERENCE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->secureCardCardRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML SecureCard searching through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlSecureCardSearchRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $dateTime;
		private $hash;

		/**
		 *  Creates the SecureCard searche request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique card identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 */
		public function XmlSecureCardSearchRequest($merchantRef,
				$terminalId)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->dateTime . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlSecureCardSearchResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("SECURECARDSEARCH");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML Stored Subscription Registrations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlStoredSubscriptionRegRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $name;
		private $description;
		private $periodType;
		private $length;
		private $recurringAmount;
		private $initialAmount;
		private $type;
		private $onUpdate;
		private $onDelete;
		private $dateTime;
		private $hash;

		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique subscription identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 *  @param secureCardMerchantRef A valid, registered SecureCard Merchant Reference.
		 *  @param name Name of the subscription
		 *  @param description Card Holder Name
		 */
		public function XmlStoredSubscriptionRegRequest($merchantRef,
				$terminalId,
				$name,
				$description,

				$periodType,
				$length,
				$currency,
				$recurringAmount,
				$initialAmount,
				$type,
				$onUpdate,
				$onDelete)
		{
				$this->dateTime = $this->GetFormattedDate();


				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;

				$this->name = $name;
				$this->description = $description;
				$this->periodType = $periodType;
				$this->length = $length;
				$this->currency = $currency;
				$this->recurringAmount = $recurringAmount;
				$this->initialAmount = $initialAmount;
				$this->type = $type;
				$this->onUpdate = $onUpdate;
				$this->onDelete = $onDelete;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->dateTime . $this->type . $this->name . $this->periodType . $this->currency . $this->recurringAmount . $this->initialAmount . $this->length . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlStoredSubscriptionRegResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("ADDSTOREDSUBSCRIPTION");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("NAME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->name));

				$node = $requestXML->createElement("DESCRIPTION");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->description));

				$node = $requestXML->createElement("PERIODTYPE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->periodType));

				$node = $requestXML->createElement("LENGTH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->length));

				$node = $requestXML->createElement("CURRENCY");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->currency));

				if($this->type != "AUTOMATIC (WITHOUT AMOUNTS)")
				{
					$node = $requestXML->createElement("RECURRINGAMOUNT");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->recurringAmount));

					$node = $requestXML->createElement("INITIALAMOUNT");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->initialAmount));
				}

				$node = $requestXML->createElement("TYPE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->type));

				$node = $requestXML->createElement("ONUPDATE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->onUpdate));

				$node = $requestXML->createElement("ONDELETE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->onDelete));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML Stored Subscription Registrations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlStoredSubscriptionUpdRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $name;
		private $description;
		private $periodType;
		private $length;
		private $currency;
		private $recurringAmount;
		private $initialAmount;
		private $type;
		private $onUpdate;
		private $onDelete;
		private $dateTime;
		private $hash;

		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique subscription identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 *  @param secureCardMerchantRef A valid, registered SecureCard Merchant Reference.
		 *  @param name Name of the subscription
		 *  @param description Card Holder Name
		 */
		public function XmlStoredSubscriptionUpdRequest($merchantRef,
				$terminalId,
				$name,
				$description,
				$periodType,
				$length,
				$currency,
				$recurringAmount,
				$initialAmount,
				$type,
				$onUpdate,
				$onDelete)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;

				$this->name = $name;
				$this->description = $description;
				$this->periodType = $periodType;
				$this->length = $length;
				$this->currency = $currency;
				$this->recurringAmount = $recurringAmount;
				$this->initialAmount = $initialAmount;
				$this->type = $type;
				$this->onUpdate = $onUpdate;
				$this->onDelete = $onDelete;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->dateTime . $this->type . $this->name . $this->periodType . $this->currency . $this->recurringAmount . $this->initialAmount . $this->length . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlStoredSubscriptionUpdResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("UPDATESTOREDSUBSCRIPTION");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("NAME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->name));

				$node = $requestXML->createElement("DESCRIPTION");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->description));

				$node = $requestXML->createElement("PERIODTYPE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->periodType));

				$node = $requestXML->createElement("LENGTH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->length));

				$node = $requestXML->createElement("CURRENCY");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->currency));

				if($this->type != "AUTOMATIC (WITHOUT AMOUNTS)")
				{
					$node = $requestXML->createElement("RECURRINGAMOUNT");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->recurringAmount));

					$node = $requestXML->createElement("INITIALAMOUNT");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->initialAmount));
				}

				$node = $requestXML->createElement("TYPE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->type));

				$node = $requestXML->createElement("ONUPDATE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->onUpdate));

				$node = $requestXML->createElement("ONDELETE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->onDelete));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML SecureCard Registrations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlStoredSubscriptionDelRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $dateTime;
		private $hash;

		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique subscription identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 */
		public function XmlStoredSubscriptionDelRequest($merchantRef,
				$terminalId)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->dateTime . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlStoredSubscriptionDelResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("DELETESTOREDSUBSCRIPTION");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

class XmlSubscriptionRegRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $storedSubscriptionRef;
		private $secureCardMerchantRef;
		private $name;
		private $description;
		private $periodType;
		private $length;
		private $currency;
		private $recurringAmount;
		private $initialAmount;
		private $type;
		private $startDate;
		private $endDate;
		private $onUpdate;
		private $onDelete;
		private $dateTime;
		private $hash;
		private $eDCCDecision;

		private $newStoredSubscription = false;

		public function SetNewStoredSubscriptionValues($name,
				$description,
				$periodType,
				$length,
				$currency,
				$recurringAmount,
				$initialAmount,
				$type,
				$onUpdate,
				$onDelete)
		{
				$this->name = $name;
				$this->description = $description;
				$this->periodType = $periodType;
				$this->length = $length;
				$this->currency = $currency;
				$this->recurringAmount = $recurringAmount;
				$this->initialAmount = $initialAmount;
				$this->type = $type;
				$this->onUpdate = $onUpdate;
				$this->onDelete = $onDelete;

				$this->newStoredSubscription = true;
		}
		public function SetSubscriptionAmounts($recurringAmount,
				$initialAmount)
		{
				$this->recurringAmount = $recurringAmount;
				$this->initialAmount = $initialAmount;
		}
	   /**
		 *  Setter for end date
		 *
		 *  @param endDate End Date of subscription
		 */
		public function SetEndDate($endDate)
		{
				$this->endDate = $endDate;
		}
	   /**
		 *  Setter for when the cardholder has accepted the eDCC offering
		 *
		 *  @param eDCCDecision eDCC decision ("Y" or "N")
		 */
		public function EDCCDecision($eDCCDecision)
		{
				$this->eDCCDecision = $eDCCDecision;
		}
		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique subscription identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS

		 *  @param storedSubscriptionRef Name of the Stored subscription under which this subscription should run
		 *  @param secureCardMerchantRef A valid, registered SecureCard Merchant Reference.
		 *  @param startDate Card Holder Name
		 */
		public function XmlSubscriptionRegRequest($merchantRef,
				$terminalId,
				$storedSubscriptionRef,
				$secureCardMerchantRef,
				$startDate)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->storedSubscriptionRef = $storedSubscriptionRef;
				$this->secureCardMerchantRef = $secureCardMerchantRef;
				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;
				$this->startDate = $startDate;
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
				if($this->newStoredSubscription) $this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->secureCardMerchantRef . $this->dateTime . $this->startDate . $sharedSecret);
				else $this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->storedSubscriptionRef . $this->secureCardMerchantRef . $this->dateTime . $this->startDate . $sharedSecret);

		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlSubscriptionRegResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("ADDSUBSCRIPTION");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				if(!$this->newStoredSubscription)
				{
					$node = $requestXML->createElement("STOREDSUBSCRIPTIONREF");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->storedSubscriptionRef));
				}

				$node = $requestXML->createElement("SECURECARDMERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->secureCardMerchantRef));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				if($this->recurringAmount != null && $this->recurringAmount != null && !$this->newStoredSubscription)
				{
					$node = $requestXML->createElement("RECURRINGAMOUNT");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->recurringAmount));

					$node = $requestXML->createElement("INITIALAMOUNT");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->initialAmount));
				}

				$node = $requestXML->createElement("STARTDATE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->startDate));


				if($this->endDate != null)
				{
					$node = $requestXML->createElement("ENDDATE");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->endDate));
				}

				if($this->eDCCDecision !== NULL)
				{
					$node = $requestXML->createElement("EDCCDECISION");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->eDCCDecision));
				}

				if($this->newStoredSubscription)
				{
					$ssNode = $requestXML->createElement("NEWSTOREDSUBSCRIPTIONINFO");
					$requestString->appendChild($ssNode );

					$ssSubNode = $requestXML->createElement("MERCHANTREF");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->storedSubscriptionRef));

					$ssSubNode = $requestXML->createElement("NAME");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->name));

					$ssSubNode = $requestXML->createElement("DESCRIPTION");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->description));

					$ssSubNode = $requestXML->createElement("PERIODTYPE");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->periodType));

					$ssSubNode = $requestXML->createElement("LENGTH");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->length));

					$ssSubNode = $requestXML->createElement("CURRENCY");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->currency));

					if($this->type != "AUTOMATIC (WITHOUT AMOUNTS)")
					{
						$ssSubNode = $requestXML->createElement("RECURRINGAMOUNT");
						$ssNode->appendChild($ssSubNode);
						$ssSubNode->appendChild($requestXML->createTextNode($this->recurringAmount));

						$ssSubNode = $requestXML->createElement("INITIALAMOUNT");
						$ssNode->appendChild($ssSubNode);
						$ssSubNode->appendChild($requestXML->createTextNode($this->initialAmount));
					}

					$ssSubNode = $requestXML->createElement("TYPE");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->type));

					$ssSubNode = $requestXML->createElement("ONUPDATE");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->onUpdate));

					$ssSubNode = $requestXML->createElement("ONDELETE");
					$ssNode->appendChild($ssSubNode);
					$ssSubNode->appendChild($requestXML->createTextNode($this->onDelete));
				}

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML SecureCard Registrations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlSubscriptionUpdRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $secureCardMerchantRef;
		private $name;
		private $description;
		private $periodType;
		private $length;
		private $recurringAmount;
		private $type;
		private $startDate;
		private $endDate;
		private $dateTime;
		private $hash;
		private $eDCCDecision;

	   /**
		 *  Setter for subscription name
		 *
		 *  @param name Subscription name
		 */
		public function SetSubName($name)
		{
				$this->name = $name;
		}
	   /**
		 *  Setter for subscription description
		 *
		 *  @param description Subscription description
		 */
		public function SetDescription($description)
		{
				$this->description = $description;
		}
	   /**
		 *  Setter for subscription period type
		 *
		 *  @param periodType Subscription period type
		 */
		public function SetPeriodType($periodType)
		{
				$this->periodType = $periodType;
		}
	   /**
		 *  Setter for subscription length
		 *
		 *  @param length Subscription length
		 */
		public function SetLength($length)
		{
				$this->length = $length;
		}
	   /**
		 *  Setter for subscription recurring amount
		 *
		 *  @param recurringAmount Subscription recurring amount
		 */
		public function SetRecurringAmount($recurringAmount)
		{
				$this->recurringAmount = $recurringAmount;
		}
	   /**
		 *  Setter for stored subscription type
		 *
		 *  @param endDate Stored subscription type
		 */
		public function SetSubType($type)
		{
				$this->type = $type;
		}
	   /**
		 *  Setter for stored subscription start date
		 *
		 *  @param startDate Stored subscription start date
		 */
		public function SetStartDate($startDate)
		{
				$this->startDate = $startDate;
		}
	   /**
		 *  Setter for stored subscription end date
		 *
		 *  @param endDate Stored subscription end date
		 */
		public function SetEndDate($endDate)
		{
				$this->endDate = $endDate;
		}
	   /**
		 *  Setter for when the cardholder has accepted the eDCC offering
		 *
		 *  @param eDCCDecision eDCC decision ("Y" or "N")
		 */
		public function EDCCDecision($eDCCDecision)
		{
				$this->eDCCDecision = $eDCCDecision;
		}
		/**
		 *  Creates the SecureCard Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique subscription identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS.
		 *  @param secureCardMerchantRef Reference to the existing or new SecureCard for the subscription.
		 */
		public function XmlSubscriptionUpdRequest($merchantRef,
				$terminalId,
				$secureCardMerchantRef)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;
				$this->secureCardMerchantRef = $secureCardMerchantRef;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->secureCardMerchantRef . $this->dateTime . $this->startDate . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlSubscriptionUpdResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("UPDATESUBSCRIPTION");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("SECURECARDMERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->secureCardMerchantRef));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				if($this->name !== NULL)
				{
						$node = $requestXML->createElement("NAME");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->name));
				}

				if($this->description !== NULL)
				{
						$node = $requestXML->createElement("DESCRIPTION");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->description));
				}

				if($this->periodType !== NULL)
				{
						$node = $requestXML->createElement("PERIODTYPE");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->periodType));
				}

				if($this->length != null)
				{
						$node = $requestXML->createElement("LENGTH");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->length));
				}

				if($this->recurringAmount !== NULL)
				{
						$node = $requestXML->createElement("RECURRINGAMOUNT");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->recurringAmount));
				}

				if($this->type !== NULL)
				{
						$node = $requestXML->createElement("TYPE");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->type));
				}

				if($this->startDate !== NULL)
				{
						$node = $requestXML->createElement("STARTDATE");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->startDate));
				}

				if($this->endDate != null)
				{
						$node = $requestXML->createElement("ENDDATE");
						$requestString->appendChild($node);
						$node->appendChild($requestXML->createTextNode($this->endDate));
				}

				if($this->eDCCDecision !== NULL)
				{
					$node = $requestXML->createElement("EDCCDECISION");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->eDCCDecision));
				}

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML SecureCard Registrations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlSubscriptionDelRequest extends Request
{
		private $merchantRef;
		private $terminalId;
		private $dateTime;
		private $hash;

		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique subscription identifier. Alpha numeric and max size 48 chars.
		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 */
		public function XmlSubscriptionDelRequest($merchantRef,
				$terminalId)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->merchantRef = $merchantRef;
				$this->terminalId = $terminalId;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->merchantRef . $this->dateTime . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlSubscriptionDelResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("DELETESUBSCRIPTION");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("MERCHANTREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->merchantRef));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**
 *  Used for processing XML SecureCard Registrations through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlSubscriptionPaymentRequest extends Request
{
		private $terminalId;
		private $orderId;
		private $amount;
		private $subscriptionRef;
		private $cardCurrency;
		private $cardAmount;
		private $conversionRate;
		private $email;
		private $dateTime;
		private $hash;

		private $foreignCurInfoSet = false;

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

		public function XmlSubscriptionPaymentRequest($terminalId,
				$orderId,
				$amount,
				$subscriptionRef)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->terminalId = $terminalId;
				$this->orderId = $orderId;
				$this->amount = $amount;
				$this->subscriptionRef = $subscriptionRef;
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
				$this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->subscriptionRef . $this->amount . $this->dateTime . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlSubscriptionPaymentResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("SUBSCRIPTIONPAYMENT");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("ORDERID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->orderId));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("AMOUNT");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->amount));

				$node = $requestXML->createElement("SUBSCRIPTIONREF");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->subscriptionRef));

				if($this->foreignCurInfoSet)
				{
					$dcNode = $requestXML->createElement("FOREIGNCURRENCYINFORMATION");
					$requestString->appendChild($dcNode );

					$dcSubNode = $requestXML->createElement("CARDCURRENCY");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->cardCurrency));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CARDAMOUNT");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->cardAmount));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CONVERSIONRATE");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->conversionRate));
					$dcNode->appendChild($dcSubNode);
				}

				if($this->email !== NULL)
				{
					$node = $requestXML->createElement("EMAIL");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->email));
				}

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				return $requestXML->saveXML();
		}
}

/**

 *  Used for processing XML Unreferenced Refund through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlUnreferencedRefundRequest extends Request
{
		private $orderId;
		private $terminalId;
		private $secureCardMerchantRef;
		private $amount;
		private $email;
		private $autoReady;
		private $dateTime;
		private $hash;
		private $operator;
		private $description;

		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique subscription identifier. Alpha numeric and max size 48 chars.

		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 */
		public function XmlUnreferencedRefundRequest($orderId,
				$terminalId,
				$secureCardMerchantRef,
				$amount,
				$operator)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->orderId = $orderId;
				$this->terminalId = $terminalId;
				$this->secureCardMerchantRef = $secureCardMerchantRef;
				$this->amount = $amount;
				$this->operator = $operator;
		}
	   /**

		 *  Setter for Transaction Description
		 *
		 *  @param email Alpha-numeric field.
		 */
		public function SetDescription($description)
		{
				$this->description = $description;
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
		 *  Setter for hash value
		 *
		 *  @param sharedSecret
		 *  Shared secret either supplied by WorldNet TPS or configured under
		 *  Terminal Settings in the Merchant Selfcare System.

		 */
		public function SetHash($sharedSecret)
		{
				$this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->amount . $this->dateTime . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlUnreferencedRefundResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("UNREFERENCEDREFUND");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("ORDERID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->orderId));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("CARDREFERENCE");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->secureCardMerchantRef));

				$node = $requestXML->createElement("AMOUNT");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->amount));

				if($this->email !== NULL)
				{
					$node = $requestXML->createElement("EMAIL");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->email));
				}

				if($this->autoReady !== NULL)
				{
					$node = $requestXML->createElement("AUTOREADY");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->autoReady));

				}

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				$node = $requestXML->createElement("OPERATOR");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->operator));

				if($this->description !== NULL)
				{
					$node = $requestXML->createElement("DESCRIPTION");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->description));
				}

				return $requestXML->saveXML();
		}
}

/**

 *  Used for processing XML Unreferenced Refund through the WorldNet TPS XML Gateway.
 *
 *  Basic request is configured on initialisation and optional fields can be configured.
 */
class XmlVoiceIDRequest extends Request
{
		private $orderId;
		private $terminalId;
		private $dateTime;
		private $mobileNumber;
		private $email;
		private $amount = "";
		private $currency = "";
		private $hash;
		private $description;

		/**
		 *  Creates the SecureCard Registration/Update request for processing
		 *  through the WorldNetTPS XML Gateway
		 *
		 *  @param merchantRef A unique subscription identifier. Alpha numeric and max size 48 chars.

		 *  @param terminalId Terminal ID provided by WorldNet TPS
		 */
		public function XmlVoiceIDRequest($orderId,
				$terminalId,
				$mobileNumber,
				$email)
		{
				$this->dateTime = $this->GetFormattedDate();

				$this->orderId = $orderId;
				$this->terminalId = $terminalId;
				$this->mobileNumber = $mobileNumber;
				$this->email = $email;
		}
	   /**

		 *  Setter for Transaction Description
		 *
		 *  @param email Alpha-numeric field.
		 */
		public function SetVoicePayInformation($amount, $currency)
		{
				$this->amount = $amount;
				$this->currency = $currency;
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
		 *  Setter for hash value
		 *
		 *  @param sharedSecret
		 *  Shared secret either supplied by WorldNet TPS or configured under
		 *  Terminal Settings in the Merchant Selfcare System.

		 */
		public function SetHash($sharedSecret)
		{
				$this->hash = $this->GetRequestHash($this->terminalId . $this->orderId . $this->dateTime . $this->mobileNumber . $this->email . $this->currency . $this->amount . $sharedSecret);
		}
	   /**
		 *  Method to process transaction and return parsed response from the WorldNet TPS XML Gateway
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
		 *  @return XmlSecureCardRegResponse containing an error or the parsed payment response.
		 */
		public function ProcessRequest($sharedSecret, $testAccount)
		{
				return $this->ProcessRequestToGateway($sharedSecret, $testAccount, "worldnet");
		}

		public function ProcessRequestToGateway($sharedSecret, $testAccount, $gateway)
		{
				$this->SetHash($sharedSecret);
				$responseString = $this->SendRequestToGateway($this->GenerateXml(), $testAccount, $gateway);
				$response = new XmlVoiceIDResponse($responseString);
				return $response;
		}

		public function GenerateXml()
		{
				$requestXML = new DOMDocument("1.0");
				$requestXML->formatOutput = true;

				$requestString = $requestXML->createElement("VOICEIDREQUEST");
				$requestXML->appendChild($requestString);

				$node = $requestXML->createElement("ORDERID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->orderId));

				$node = $requestXML->createElement("TERMINALID");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->terminalId));

				$node = $requestXML->createElement("DATETIME");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->dateTime));

				$node = $requestXML->createElement("MOBILENUMBER");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->mobileNumber));

				$node = $requestXML->createElement("EMAIL");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->email));

				if($this->amount != "" && $this->currency != "")
				{
					$dcNode = $requestXML->createElement("VOICEIDPAYMENT");
					$requestString->appendChild($dcNode );

					$dcSubNode = $requestXML->createElement("AMOUNT");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->amount));
					$dcNode->appendChild($dcSubNode);

					$dcSubNode = $requestXML->createElement("CURRENCY");
					$dcSubNode ->appendChild($requestXML->createTextNode($this->currency));
					$dcNode->appendChild($dcSubNode);
				}

				$node = $requestXML->createElement("HASH");
				$requestString->appendChild($node);
				$node->appendChild($requestXML->createTextNode($this->hash));

				if($this->description !== NULL)
				{
					$node = $requestXML->createElement("DESCRIPTION");
					$requestString->appendChild($node);
					$node->appendChild($requestXML->createTextNode($this->description));
				}

				return $requestXML->saveXML();
		}
}

/**
  *  Holder class for parsed response. If there was an error there will be an error string
  *  otherwise all values will be populated with the parsed payment response values.
  *
  *  IsError should be checked before accessing any fields.
  *
  *  ErrorString will contain the error if one occurred.
  */
class XmlAuthResponse
{
		private $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		private $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		private $responseCode;
		public function ResponseCode()
		{
				return $this->responseCode;
		}

		private $responseText;
		public function ResponseText()
		{
				return $this->responseText;
		}

		private $approvalCode;
		public function ApprovalCode()
		{
				return $this->approvalCode;
		}

		private $authorizedAmount;
		public function AuthorizedAmount()
		{
				return $this->authorizedAmount;
		}

		private $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		private $avsResponse;
		public function AvsResponse()
		{
				return $this->avsResponse;
		}

		private $cvvResponse;
		public function CvvResponse()
		{
				return $this->cvvResponse;
		}

		private $uniqueRef;
		public function UniqueRef()
		{
				return $this->uniqueRef;
		}

		private $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlAuthResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "PAYMENTRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("PAYMENTRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->uniqueRef = $node->getElementsByTagName('UNIQUEREF')->item(0)->nodeValue;
									$this->responseCode = $node->getElementsByTagName('RESPONSECODE')->item(0)->nodeValue;
									$this->responseText = $node->getElementsByTagName('RESPONSETEXT')->item(0)->nodeValue;
									$this->approvalCode = $node->getElementsByTagName('APPROVALCODE')->item(0)->nodeValue;
									$this->authorizedAmount = $node->getElementsByTagName('AUTHORIZEDAMOUNT')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->avsResponse = $node->getElementsByTagName('AVSRESPONSE')->item(0)->nodeValue;
									$this->cvvResponse = $node->getElementsByTagName('CVVRESPONSE')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed response. If there was an error there will be an error string
  *  otherwise all values will be populated with the parsed payment response values.
  *
  *  IsError should be checked before accessing any fields.
  *
  *  ErrorString will contain the error if one occurred.
  */
class XmlRefundResponse
{
		private $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		private $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		private $responseCode;
		public function ResponseCode()
		{
				return $this->responseCode;
		}

		private $responseText;
		public function ResponseText()
		{
				return $this->responseText;
		}

		private $approvalCode;
		public function OrderId()
		{
				return $this->orderId;
		}

		private $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		private $uniqueRef;
		public function UniqueRef()
		{
				return $this->uniqueRef;
		}

		private $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlRefundResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "REFUNDRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("REFUNDRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->responseCode = $node->getElementsByTagName('RESPONSECODE')->item(0)->nodeValue;
									$this->responseText = $node->getElementsByTagName('RESPONSETEXT')->item(0)->nodeValue;
									$this->uniqueRef = $node->getElementsByTagName('UNIQUEREF')->item(0)->nodeValue;
									$this->orderId = $node->getElementsByTagName('ORDERID')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed response. If there was an error there will be an error string
  *  otherwise all values will be populated with the parsed payment response values.
  *
  *  IsError should be checked before accessing any fields.
  *
  *  ErrorString will contain the error if one occurred.
  */
class XmlPreAuthResponse
{
		private $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		private $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		private $responseCode;
		public function ResponseCode()
		{
				return $this->responseCode;
		}

		private $responseText;
		public function ResponseText()
		{
				return $this->responseText;
		}

		private $approvalCode;
		public function ApprovalCode()
		{
				return $this->approvalCode;
		}

		private $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		private $avsResponse;
		public function AvsResponse()
		{
				return $this->avsResponse;
		}

		private $cvvResponse;
		public function CvvResponse()
		{
				return $this->cvvResponse;
		}

		private $uniqueRef;
		public function UniqueRef()
		{
				return $this->uniqueRef;
		}

		private $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlPreAuthResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "PREAUTHRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("PREAUTHRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->uniqueRef = $node->getElementsByTagName('UNIQUEREF')->item(0)->nodeValue;
									$this->responseCode = $node->getElementsByTagName('RESPONSECODE')->item(0)->nodeValue;
									$this->responseText = $node->getElementsByTagName('RESPONSETEXT')->item(0)->nodeValue;
									$this->approvalCode = $node->getElementsByTagName('APPROVALCODE')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->avsResponse = $node->getElementsByTagName('AVSRESPONSE')->item(0)->nodeValue;
									$this->cvvResponse = $node->getElementsByTagName('CVVRESPONSE')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed response. If there was an error there will be an error string
  *  otherwise all values will be populated with the parsed payment response values.
  *
  *  IsError should be checked before accessing any fields.
  *
  *  ErrorString will contain the error if one occurred.
  */
class XmlPreAuthCompletionResponse
{
		private $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		private $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		private $responseCode;
		public function ResponseCode()
		{
				return $this->responseCode;
		}

		private $responseText;
		public function ResponseText()
		{
				return $this->responseText;
		}

		private $approvalCode;
		public function ApprovalCode()
		{
				return $this->approvalCode;
		}

		private $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		private $avsResponse;
		public function AvsResponse()
		{
				return $this->avsResponse;
		}

		private $cvvResponse;
		public function CvvResponse()
		{
				return $this->cvvResponse;
		}

		private $uniqueRef;
		public function UniqueRef()
		{
				return $this->uniqueRef;
		}

		private $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlPreAuthCompletionResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "PREAUTHCOMPLETIONRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("PREAUTHCOMPLETIONRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->uniqueRef = $node->getElementsByTagName('UNIQUEREF')->item(0)->nodeValue;
									$this->responseCode = $node->getElementsByTagName('RESPONSECODE')->item(0)->nodeValue;
									$this->responseText = $node->getElementsByTagName('RESPONSETEXT')->item(0)->nodeValue;
									$this->approvalCode = $node->getElementsByTagName('APPROVALCODE')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->avsResponse = $node->getElementsByTagName('AVSRESPONSE')->item(0)->nodeValue;
									$this->cvvResponse = $node->getElementsByTagName('CVVRESPONSE')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed response. If there was an error there will be an error string
  *  otherwise all values will be populated with the parsed payment response values.
  *
  *  IsError should be checked before accessing any fields.
  *
  *  ErrorString will contain the error if one occurred.
  */
class XmlRateResponse
{
		private $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		private $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		private $terminalCurrency;
		public function TerminalCurrency()
		{
				return $this->terminalCurrency;
		}

		private $cardCurrency;
		public function CardCurrency()
		{
				return $this->cardCurrency;
		}

		private $conversionRate;
		public function ConversionRate()
		{
				return $this->conversionRate;
		}
		private $foreignAmount;
		public function ForeignAmount()
		{
				return $this->foreignAmount;
		}

		private $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		private $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlRateResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{

						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "CARDCURRENCYRATERESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("CARDCURRENCYRATERESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->terminalCurrency = $node->getElementsByTagName('TERMINALCURRENCY')->item(0)->nodeValue;
									$this->cardCurrency = $node->getElementsByTagName('CARDCURRENCY')->item(0)->nodeValue;
									$this->conversionRate = $node->getElementsByTagName('CONVERSIONRATE')->item(0)->nodeValue;
									$this->foreignAmount = $node->getElementsByTagName('FOREIGNAMOUNT')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");


						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Base holder class for parsed SecureCard response. If there was an error there will be an error string
  *  otherwise all values will be populated with the parsed payment response values.
  */
class XmlSecureCardResponse
{
		protected $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		protected $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		protected $errorCode;
		public function ErrorCode()
		{
				return $this->errorCode;
		}

		protected $merchantRef;
		public function MerchantReference()
		{
				return $this->merchantRef;
		}

		protected $cardRef;
		public function CardReference()
		{
				return $this->cardRef;
		}

		protected $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		protected $hash;
		public function Hash()
		{
				return $this->hash;
		}
}

/**
  *  Holder class for parsed SecureCard registration response.
  */
class XmlSecureCardRegResponse extends XmlSecureCardResponse
{
		public function XmlSecureCardRegResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "SECURECARDREGISTRATIONRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("SECURECARDREGISTRATIONRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->cardRef = $node->getElementsByTagName('CARDREFERENCE')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed SecureCard update response.
  */
class XmlSecureCardUpdResponse extends XmlSecureCardResponse
{
		public function XmlSecureCardUpdResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "SECURECARDUPDATERESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("SECURECARDUPDATERESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->cardRef = $node->getElementsByTagName('CARDREFERENCE')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed SecureCard search response.
  */
class XmlSecureCardDelResponse extends XmlSecureCardResponse
{
		public function XmlSecureCardDelResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "SECURECARDREMOVALRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("SECURECARDREMOVALRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed SecureCard search response.
  */
class XmlSecureCardSearchResponse extends XmlSecureCardResponse
{
		protected $merchantRef;
		public function MerchantReference()
		{
				return $this->merchantRef;
		}

		protected $cardRef;
		public function CardReference()
		{
				return $this->cardRef;
		}

		private $cardType;
		public function CardType()
		{
				return $this->cardType;
		}

		private $expiry;
		public function CardExpiry()
		{
				return $this->expiry;
		}

		private $cardHolderName;
		public function CardHolderName()
		{
				return $this->cardHolderName;
		}

		protected $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlSecureCardSearchResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "SECURECARDSEARCHRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("SECURECARDSEARCHRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->cardRef = $node->getElementsByTagName('CARDREFERENCE')->item(0)->nodeValue;
									$this->cardType = $node->getElementsByTagName('CARDTYPE')->item(0)->nodeValue;
									$this->expiry = $node->getElementsByTagName('CARDEXPIRY')->item(0)->nodeValue;
									$this->cardHolderName = $node->getElementsByTagName('CARDHOLDERNAME')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Base holder class for parsed Subscription response. If there was an error there will be an error string
  *  otherwise all values will be populated with the parsed payment response values.
  */
class XmlSubscriptionResponse
{
		protected $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		protected $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		protected $errorCode;
		public function ErrorCode()
		{
				return $this->errorCode;
		}

		protected $merchantRef;
		public function MerchantReference()
		{
				return $this->merchantRef;
		}

		protected $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		protected $hash;
		public function Hash()
		{
				return $this->hash;
		}
}

/**
  *  Holder class for parsed Stored Subscription registration response.
  */
class XmlStoredSubscriptionRegResponse extends XmlSubscriptionResponse
{
		public function XmlStoredSubscriptionRegResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "ADDSTOREDSUBSCRIPTIONRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("ADDSTOREDSUBSCRIPTIONRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed Stored Subscription update response.
  */
class XmlStoredSubscriptionUpdResponse extends XmlSubscriptionResponse
{
		public function XmlStoredSubscriptionUpdResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "UPDATESTOREDSUBSCRIPTIONRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("UPDATESTOREDSUBSCRIPTIONRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed Stored Subscription deletion response.
  */
class XmlStoredSubscriptionDelResponse extends XmlSubscriptionResponse
{
		public function XmlStoredSubscriptionDelResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "DELETESTOREDSUBSCRIPTIONRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("DELETESTOREDSUBSCRIPTIONRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed Subscription registration response.
  */
class XmlSubscriptionRegResponse extends XmlSubscriptionResponse
{
		public function XmlSubscriptionRegResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "ADDSUBSCRIPTIONRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("ADDSUBSCRIPTIONRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed Subscription update response.
  */
class XmlSubscriptionUpdResponse extends XmlSubscriptionResponse
{
		public function XmlSubscriptionUpdResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "UPDATESUBSCRIPTIONRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("UPDATESUBSCRIPTIONRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed Subscription deletion response.
  */
class XmlSubscriptionDelResponse extends XmlSubscriptionResponse
{
		public function XmlSubscriptionDelResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "DELETESUBSCRIPTIONRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("DELETESUBSCRIPTIONRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->merchantRef = $node->getElementsByTagName('MERCHANTREF')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  Holder class for parsed Subscription Payment response.
  */
class XmlSubscriptionPaymentResponse
{
		private $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		private $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		private $responseCode;
		public function ResponseCode()
		{
				return $this->responseCode;
		}

		private $responseText;
		public function ResponseText()
		{
				return $this->responseText;
		}

		private $approvalCode;
		public function ApprovalCode()
		{
				return $this->approvalCode;
		}

		private $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		private $uniqueRef;
		public function UniqueRef()
		{
				return $this->uniqueRef;
		}

		private $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlSubscriptionPaymentResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}

								$this->isError = true;
						}
						else if (strpos($responseXml, "SUBSCRIPTIONPAYMENTRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("SUBSCRIPTIONPAYMENTRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->uniqueRef = $node->getElementsByTagName('UNIQUEREF')->item(0)->nodeValue;
									$this->responseCode = $node->getElementsByTagName('RESPONSECODE')->item(0)->nodeValue;
									$this->responseText = $node->getElementsByTagName('RESPONSETEXT')->item(0)->nodeValue;
									$this->approvalCode = $node->getElementsByTagName('APPROVALCODE')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}
/**
  *  Holder class for parsed Unreferenced Refund response.
  */
class XmlUnreferencedRefundResponse
{
		private $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		private $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		private $responseCode;
		public function ResponseCode()
		{
				return $this->responseCode;
		}

		private $responseText;
		public function ResponseText()
		{
				return $this->responseText;
		}

		private $orderId;
		public function OrderId()
		{
				return $this->orderId;
		}

		private $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		private $uniqueRef;
		public function UniqueRef()
		{
				return $this->uniqueRef;
		}

		private $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlUnreferencedRefundResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "UNREFERENCEDREFUNDRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("UNREFERENCEDREFUNDRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->responseCode = $node->getElementsByTagName('RESPONSECODE')->item(0)->nodeValue;
									$this->responseText = $node->getElementsByTagName('RESPONSETEXT')->item(0)->nodeValue;
									$this->uniqueRef = $node->getElementsByTagName('UNIQUEREF')->item(0)->nodeValue;
									$this->orderId = $node->getElementsByTagName('ORDERID')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}
/**
  *  Holder class for parsed VoiceID response.
  */
class XmlVoiceIDResponse
{
		private $isError = false;
		public function IsError()
		{
				return $this->isError;
		}

		private $errorString;
		public function ErrorString()
		{
				return $this->errorString;
		}

		private $responseCode;
		public function ResponseCode()
		{
				return $this->responseCode;
		}

		private $responseText;
		public function ResponseText()
		{
				return $this->responseText;
		}

		private $orderId;
		public function OrderId()
		{
				return $this->orderId;
		}

		private $dateTime;
		public function DateTime()
		{
				return $this->dateTime;
		}

		private $hash;
		public function Hash()
		{
				return $this->hash;
		}

		public function XmlVoiceIDResponse($responseXml)
		{
				$doc = new DOMDocument();
				$doc->loadXML($responseXml);
				try
				{
						if (strpos($responseXml, "ERROR"))
						{
								$responseNodes = $doc->getElementsByTagName("ERROR");
								foreach( $responseNodes as $node )
								{
									$this->errorCode = $node->getElementsByTagName('ERRORCODE')->item(0)->nodeValue;
									$this->errorString = $node->getElementsByTagName('ERRORSTRING')->item(0)->nodeValue;
								}
								$this->isError = true;
						}
						else if (strpos($responseXml, "VOICEIDRESPONSE"))
						{
								$responseNodes = $doc->getElementsByTagName("VOICEIDRESPONSE");

								foreach( $responseNodes as $node )
								{
									$this->responseCode = $node->getElementsByTagName('RESPONSECODE')->item(0)->nodeValue;
									$this->responseText = $node->getElementsByTagName('RESPONSETEXT')->item(0)->nodeValue;
									$this->orderId = $node->getElementsByTagName('ORDERID')->item(0)->nodeValue;
									$this->dateTime = $node->getElementsByTagName('DATETIME')->item(0)->nodeValue;
									$this->hash = $node->getElementsByTagName('HASH')->item(0)->nodeValue;
								}
						}
						else
						{
								throw new Exception("Invalid Response");
						}
				}
				catch (Exception $e)
				{
						$this->isError = true;
						$this->errorString = $e->getMessage();
				}
		}
}

/**
  *  For backward compatibility with older class names.
  */
class XmlStandardRequest extends XmlAuthRequest { }
class XmlStandardResponse extends XmlAuthResponse { }

/**
  * XML Functions - For internal use.
  */
function libxml_display_error($error)
{
	$errorString = "<br />";
	switch ($error->level)
	{
		case LIBXML_ERR_WARNING:
			$errorString .= "<b>Warning $error->code</b>: ";
			break;
		case LIBXML_ERR_ERROR:
			$errorString .= "<b>Error $error->code</b>: ";
			break;
		case LIBXML_ERR_FATAL:
			$errorString .= "<b>Fatal Error $error->code</b>: ";
			break;
	}
	$errorString .= trim($error->message);
	if ($error->file) $errorString .= " in <b>$error->file</b>";
	$errorString .= " on line <b>$error->line</b><br />";

	return $errorString;
}

function libxml_display_errors()
{
	$errorString = '';
	$errors = libxml_get_errors();
	foreach ($errors as $error) $errorString .= libxml_display_error($error);
	libxml_clear_errors();
	return $errorString;
}

// Enable user error handling
libxml_use_internal_errors(true);

?>

