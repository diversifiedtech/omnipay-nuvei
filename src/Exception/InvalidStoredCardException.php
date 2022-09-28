<?php

namespace Omnipay\Nuvei\Exception;

use Omnipay\Common\Exception\OmnipayException;

/**
 * Invalid Ach Exception
 *
 * Thrown when a ach is invalid or missing required fields.
 */
class InvalidStoredCardException extends \Exception implements OmnipayException
{
}
