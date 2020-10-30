<?php

namespace Omnipay\Nuvei\Base;

use DOMDocument;
use Exception;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Nuvei\Base\Request;
use Omnipay\Nuvei\Exception\InvalidNuveiResponseException;
/**
  *  Holder class for parsed response. If there was an error there will be an error string
  *  otherwise all values will be populated with the parsed payment response values.
  *
  *  IsError should be checked before accessing any fields.
  *
  *  ErrorString will contain the error if one occurred.
  */
class XmlPaymentResponse
{

	const DEFAULT_ERROR_CODE = "E";
	const DEFAULT_ERROR_MESSAGE = "Invalid Response";

	private $paymentMethod = "card";
	public function PaymentMethod()
	{
		return $this->paymentMethod;
	}

	private $responseCode;
	public function ResponseCode()
	{
		return $this->responseCode;
	}

	private $bankResponseCode;
	public function BankResponseCode()
	{
		return $this->bankResponseCode;
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

	private $data;
	public function getData(){
		return $this->data;
	}

	public function __construct($responseXml)
	{
		$doc = new DOMDocument();

		try{
			$doc->loadXML($responseXml);
		}catch(Exception $e){
			throw new InvalidResponseException("Invalid Xml Response",$e->getCode());
		}

		if (strpos($responseXml, "PAYMENTRESPONSE"))
		{
			$responseNodes = $doc->getElementsByTagName("PAYMENTRESPONSE");

			foreach( $responseNodes as $node )
			{
				$this->uniqueRef = $this->getValue($node,'UNIQUEREF');
				$this->responseCode = $this->getValue($node,'RESPONSECODE');
				$this->responseText = $this->getValue($node,'RESPONSETEXT');
				$this->approvalCode = $this->getValue($node,'APPROVALCODE');
				$this->bankResponseCode = $this->getValue($node,'BANKRESPONSECODE');

				$this->dateTime = $this->getValue($node,'DATETIME');
				$this->avsResponse = $this->getValue($node,'AVSRESPONSE');
				$this->cvvResponse = $this->getValue($node,'CVVRESPONSE');
				$this->hash = $this->getValue($node,'HASH');

				$this->data = [
					'uniqueRef' => $this->uniqueRef,
					'responseCode' => $this->responseCode,
					'responseText' => $this->responseText,
					'approvalCode' => $this->approvalCode,
					'bankResponseCode' => $this->bankResponseCode,
					'dateTime' => $this->dateTime,
					'avsResponse' => $this->avsResponse,
					'cvvResponse' => $this->cvvResponse,
					'hash' => $this->hash,
				];
			}
		}
		else if (strpos($responseXml, "PAYMENTACHRESPONSE"))
		{
			$this->paymentMethod = "ach";
			$responseNodes = $doc->getElementsByTagName("PAYMENTACHRESPONSE");

			foreach( $responseNodes as $node )
			{
				$this->uniqueRef = $this->getValue($node,'UNIQUEREF');
				$this->responseCode = $this->getValue($node,'RESPONSECODE');
				$this->responseText = $this->getValue($node,'RESPONSETEXT');
				$this->bankResponseCode = $this->getValue($node,'BANKRESPONSECODE');

				$this->approvalCode = $this->getValue($node,'APPROVALCODE');
				$this->dateTime = $this->getValue($node,'DATETIME');
					// $this->avsResponse = $this->getValue($node,'AVSRESPONSE');
					// $this->cvvResponse = $this->getValue($node,'CVVRESPONSE');

				$this->hash = $this->getValue($node,'HASH');

				$this->data = [
					'uniqueRef' => $this->uniqueRef,
					'responseCode' => $this->responseCode,
					'responseText' => $this->responseText,
					'approvalCode' => $this->approvalCode,
					'bankResponseCode' => $this->bankResponseCode,
					'dateTime' => $this->dateTime,
					'hash' => $this->hash,
				];
			}
		}
		else if (strpos($responseXml, "ERROR"))
		{
			$errorCode = static::DEFAULT_ERROR_CODE;
			$errorString = static::DEFAULT_ERROR_MESSAGE;
			$responseNodes = $doc->getElementsByTagName("ERROR");
			foreach( $responseNodes as $node )
			{
				$errorCode = $this->getValue($node,'ERRORCODE');
				$errorString = $this->getValue($node,'ERRORSTRING');
			}
			throw new InvalidNuveiResponseException($errorString,$errorCode,$responseXml);
		}
		else
		{
			throw new InvalidNuveiResponseException(static::DEFAULT_ERROR_MESSAGE,static::DEFAULT_ERROR_CODE,$responseXml);
		}

	}

	private function getValue($node,$KEY){
		$element = $node->getElementsByTagName($KEY);
		if($element !== null){
			$item = $element->item(0);
			if($item !== null){
				return $item->nodeValue;
			}
		}
	}
}
