<?php

namespace Omnipay\Nuvei\Base;

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
        if(!$requestXML->schemaValidate($XMLSchemaFile)) {
            throw new OmnipayException('<b>XML VALIDATION FAILED AGAINST SCHEMA:</b>' . $XMLSchemaFile . libxml_display_errors());
        }

        unset($requestXML);

        // Initialisation
        $ch=curl_init();

        // Set parameters
        curl_setopt($ch, CURLOPT_URL, $serverUrl);

        // Return a variable instead of posting it directly
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

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
