<?php
/**
 * Nuvei  Gateway
 */
namespace Omnipay\Nuvei;

use Omnipay\Common\AbstractGateway;
use Omnipay\Nuvei\Message\NuveiAuthorizeRequest;
use Omnipay\Nuvei\Message\NuveiPurchaseRequest;

/**
 * Nuvei Xml Gateway
 *
 * API details for the Nuvei Xml Gateway are at the links below.
 *
 * ### Example
 *
 * <code>
 * // Create a gateway for the Nuvei Gateway
 * // (routes to GatewayFactory::create)
 * $gateway = Omnipay::create('Nuvei');
 *
 * // Initialise the gateway
 * $gateway->initialize(array(
 *     'gatewayId' => '12341234',
 *     'password'  => 'thisISmyPASSWORD',
 *     'testMode'  => true, // Or false when you are ready for live transactions
 * ));
 *
 * // Create a credit card object
 * $card = new CreditCard(array(
 *     'firstName'            => 'Example',
 *     'lastName'             => 'Customer',
 *     'number'               => '4222222222222222',
 *     'expiryMonth'          => '01',
 *     'expiryYear'           => '2020',
 *     'cvv'                  => '123',
 *     'email'                => 'customer@example.com',
 *     'billingAddress1'      => '1 Scrubby Creek Road',
 *     'billingCountry'       => 'AU',
 *     'billingCity'          => 'Scrubby Creek',
 *     'billingPostcode'      => '4999',
 *     'billingState'         => 'QLD',
 * ));
 *
 * // Do a purchase transaction on the gateway
 * $transaction = $gateway->purchase(array(
 *     'description'              => 'Your order for widgets',
 *     'amount'                   => '10.00',
 *     'transactionId'            => 12345, // OrderId
 *     'clientIp'                 => $_SERVER['REMOTE_ADDR'],
 *     'currency'                 => 'USA'
 *     'card'                     => $card,
 * ));
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     echo "Purchase transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 *
 * ### Test Accounts
 *
 * Test accounts can be obtained here:
 * https://provisioning.demo.globalgatewaye4.Nuvei.com/signup
 * Note that only USD transactions are supported for test accounts.
 *
 * Once you have created a test account, log in to the gateway here:
 * https://testpayments.nuvei.com/merchant/selfcare/controller/selfcareindex
 * You must request test credentials
 *
 * Test credit card numbers can be found here:
 * https://support.Nuvei.com/hc/en-us/articles/204504235-Using-test-credit-card-numbers
 *
 * ### Quirks
 *
 * This gateway accepts XML requests.
 * This gateway expects a unique orderId for every transaction
 * This gateway expects a secret code that gets hashed with the rest
 * of the parameters for validation
 */
class Gateway extends AbstractGateway
{

    public static function generateUniqueOrderId(){
        return substr(md5(microtime() . uniqid()), 0, 20);
    }

    public function getName()
    {
        return 'Nuvei XML Gateway';
    }

    public function getDefaultParameters()
    {
        return array(
            'terminalId' => '',
            'secret' => '',
            'testMode' => false,
        );
    }

    /**
     * Get Terminal ID
     *
     * Calls to the API are secured with a terminal ID and
     * secret.
     *
     * @return string
     */
    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    /**
     * Set Terminal ID
     *
     * Calls to the API are secured with a terminal ID and
     * secret.
     *
     * @return Gateway provides a fluent interface.
     */
    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    /**
     * Get Secret
     *
     * Calls to the API are secured with a terminal ID and
     * secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * Set Secret
     *
     * Calls to the API are secured with a terminal ID and
     * secret.
     *
     * @return Gateway provides a fluent interface.
     */
    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }


    /**
     * Create a purchase request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Nuvei\Message\NuveiPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest(NuveiPurchaseRequest::class, $parameters);
    }

    /**
     * Create an authorize request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Nuvei\Message\NuveiAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest(NuveiAuthorizeRequest::class, $parameters);

    }

    /**
     * Create a capture request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Nuvei\Message\NuveiCaptureRequest
     */
    // public function capture(array $parameters = array())
    // {
    //     return $this->createRequest(NuveiPurchaseRequest::class, $parameters);
    // }

    /**
     * Create a refund request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Nuvei\Message\NuveiRefundRequest
     */
    // public function refund(array $parameters = array())
    // {
    //     return $this->createRequest(NuveiPurchaseRequest::class, $parameters);
    // }

    /**
     * Create a void request.
     *
     * @param array $parameters
     *
     * @return \Omnipay\Nuvei\Message\NuveiVoidRequest
     */
    // public function void(array $parameters = array())
    // {
    //     return $this->createRequest(NuveiPurchaseRequest::class, $parameters);
    // }
}
