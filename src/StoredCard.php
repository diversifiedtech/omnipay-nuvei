<?php
/**
 * Stored Card class
 */

namespace Omnipay\Nuvei;

use DateTime;
use DateTimeZone;
use Omnipay\Common\Helper;
use Omnipay\Common\ParametersTrait;
use Omnipay\Nuvei\Exception\InvalidAchException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Omnipay\Common\Exception\InvalidCreditCardException;

/**
 * Stored Card class
 *
 * This class defines and abstracts all of the Stored Card types used
 * throughout the Omnipay system.
 *
 * Example:
 *
 * <code>
 *   // Define Stored Card parameters, which should look like this
 *   $parameters = [
 *       'firstName' => 'Bobby',
 *       'lastName' => 'Tables',
 *       'accountNumber' => '856667',
 *       'routingNumber' => '072403004',
 *       'checkNumber' => '1002',
 *       'email' => 'testach@gmail.com',
 *   ];
 *
 *   // Create an Stored Card object
 *   $stored = new StoredCard($parameters);
 * </code>
 *
 * The full list of Stored Card attributes that may be set via the parameter to
 * *new* is as follows:
 *
 * * checkType
 * * accountNumber
 * * routingNumber
 * * checkNumber
 *
 * * driversLicense
 * * driversLicenseState
 * * ssn
 * * taxId
 * * militaryId
 *
 * * customer
 * * ecommerceFlag
 * * releaseType
 * * vip
 * * clerk
 * * device
 * * micr
 *
 * * title
 * * firstName
 * * lastName
 * * name
 * * company
 * * address1
 * * address2
 * * city
 * * postcode
 * * state
 * * country
 * * phone
 * * phoneExtension
 * * fax
 * * cvv
 * * billingTitle
 * * billingName
 * * billingFirstName
 * * billingLastName
 * * billingCompany
 * * billingAddress1
 * * billingAddress2
 * * billingCity
 * * billingPostcode
 * * billingState
 * * billingCountry
 * * billingPhone
 * * billingFax
 * * shippingTitle
 * * shippingName
 * * shippingFirstName
 * * shippingLastName
 * * shippingCompany
 * * shippingAddress1
 * * shippingAddress2
 * * shippingCity
 * * shippingPostcode
 * * shippingState
 * * shippingCountry
 * * shippingPhone
 * * shippingFax
 * * email
 * * birthday
 * * gender
 *
 * If any unknown parameters are passed in, they will be ignored.  No error is thrown.
 */
class StoredCard extends \Omnipay\Common\CreditCard
{
    /**
     * Validate this credit Stored Card. If the Stored Card is invalid, InvalidStored CardException is thrown.
     *
     * This method is called internally by gateways to avoid wasting time with an API call
     * when the credit Stored Card is clearly invalid.
     *
     * Generally if you want to validate the credit Stored Card yourself with custom error
     * messages, you should use your framework's validation library, not this method.
     *
     * @return void
     * @throws Exception\InvalidRequestException
     * @throws InvalidStoredCardException
     */
    public function validate()
    {

        $requiredParameters = array(
            'number' => 'Card Reference',
        );


        foreach ($requiredParameters as $key => $val) {
            if (!$this->getParameter($key)) {
                throw new InvalidCreditCardException("The $val is required");
            }
        }
    }

    public function getBrand()
    {
       return "SECURECARD";
    }

}
