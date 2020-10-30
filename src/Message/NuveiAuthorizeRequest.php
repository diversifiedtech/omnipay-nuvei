<?php
/**
 * Nuvei Purchase Request
 */
namespace Omnipay\Nuvei\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Nuvei\Base\XmlPaymentRequest;
use Omnipay\Nuvei\Message\AbstractNuveiRequest;

/**
 * Nuvei Purchase Request
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
 *     'terminalId' => '12341234',
 *     'secret'  => 'thisISmyPASSWORD',
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
 *     'transactionId'            => 12345,
 *     'clientIp'                 => $_SERVER['REMOTE_ADDR'],
 *     'card'                     => $card,
 * ));
 *
 * // USE A TRANS-ARMOR TOKEN TO PROCESS A PURCHASE:
 *
 * // Create a credit card object
 * $card = new CreditCard(array(
 *     'firstName'            => 'Example',
 *     'lastName'             => 'Customer',
 *     'expiryMonth'          => '01',
 *     'expiryYear'           => '2020',
 * ));
 *
 * // Do a purchase transaction on the gateway
 * $transaction = $gateway->purchase(array(
 *     'description'              => 'Your order for widgets',
 *     'amount'                   => '10.00',
 *     'cardReference'            => $yourStoredToken,
 *     'clientIp'                 => $_SERVER['REMOTE_ADDR'],
 *     'card'                     => $card,
 *     'tokenCardType'              => 'visa', // MUST BE VALID CONST FROM \omnipay\common\CreditCard
 * ));
 *
 *
 *
 *
 * $response = $transaction->send();
 * if ($response->isSuccessful()) {
 *     echo "Purchase transaction was successful!\n";
 *     $sale_id = $response->getTransactionReference();
 *     echo "Transaction reference = " . $sale_id . "\n";
 * }
 * </code>
 */
class NuveiAuthorizeRequest extends NuveiPurchaseRequest
{
    protected $action = self::TRAN_PREAUTH;

}

