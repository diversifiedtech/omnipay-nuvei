<?php

namespace Omnipay\Nuvei\Base;

use DOMDocument;
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

    public function toArray(){
    	return [];
    }
}
