<?php

namespace Omnipay\Nuvei\Exception;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Exception\OmnipayException;

/**
 * Invalid Ach Exception
 *
 * Thrown when a ach is invalid or missing required fields.
 */
class InvalidNuveiResponseException extends InvalidResponseException
{

	protected $errorCode;
	protected $rawXml;
	public function __construct($message, $errorCode = null,$rawXml = null,$code = 0, $previous = null)
	{
		if($errorCode == null){
			$errorCode = "E";
		}

		$this->errorCode = $errorCode;
		$this->rawXml = $rawXml;

		parent::__construct($message, $code, $previous);
	}

	public function getErrorCode(){
		return $this->errorCode;
	}

	public function getRawXml(){
		return $this->rawXml;
	}
}
