<?php

namespace Omnipay\Nuvei\Feature;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Nuvei\Ach;
use Omnipay\Nuvei\Exception\InvalidAchException;
use Omnipay\Nuvei\Exception\InvalidNuveiResponseException;
use Omnipay\Nuvei\Gateway;
use Omnipay\Omnipay;
use Omnipay\Tests\GatewayTestCase;
use PHPUnit\Framework\TestCase;

class DemoNuveiAchGatewayTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->gateway = Omnipay::create('Nuvei');

        $this->gateway->initialize([
            'terminalid' => $_ENV['DEMO_TERMINAL_ID'],
            'currency' => "USD",
            'secret' => $_ENV['DEMO_SECRET'],
            'testMode'  => true,
        ]);
    }

    /**
     * Everything was successful
     */
    public function test_ach_nuvei_purchase_success()
    {

        $AN = rand(0,9999999999);
        $CN = rand(0,9999);

        $ach = new Ach([
            'firstName'            => 'Example',
            'lastName'             => 'Customerx',
            'routingNumber'        => '021000021',
            'accountNumber'        => $AN,
            'checkNumber'          => $CN,
            'checkType'            => 'CHECKING',
        ]);

        $id = Gateway::generateUniqueOrderId();

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '12.00',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'ach'                      => $ach,
        ])->send();

        $this->assertTrue($response->isSuccessful());

        $this->assertNull($response->getCardReference());

        $this->assertNotNull($response->getAuthorizationNumber());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals($id,$response->getTransactionId());


        $this->assertEquals($response->getCode(),"E");
        $this->assertEquals($response->getResponseCode(),"E");
        $this->assertEquals($response->getBankResponseCode(),null);

        $this->assertEquals($response->getMessage(),"ACCEPTED");
        $this->assertEquals($response->getResponseMessage(),"ACCEPTED");

        $this->assertEquals($response->getCvvResponse(),null);
        $this->assertEquals($response->getAvsResponse(),null);

        $this->assertEquals($response->getCardType(),null);
        $this->assertEquals($response->getCardNumber(),null);
    }

    public function test_ach_nuvei_purchase_success_with_additional_fields()
    {

        $AN = rand(0,9999999999);
        $CN = rand(0,9999);
        $id = Gateway::generateUniqueOrderId();

        $ach = new Ach([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'routingNumber'        => '021000021',
            'accountNumber'        => $AN,
            'checkNumber'          => $CN,
            'address1'             => '1 Scrubby Creek Road',
            'country'              => 'AU',
            'city'                 => 'Scrubby Creek',
            'postcode'             => '4999',
            'state'                => 'PA',
            'phone'                => '5551234567',
            'email'                => 'example@email.com',
            'checkType'            => 'C',
            'release_type'         => 'D',
            'vip'                  => false,
            'clerk'                => 'AAAA',
            'device'               => 'BBBB',
            'micr'                 => 'CCCC',
            'ecommerce_flag'       => 7,
            'license'              => "123456789",
            'license_state'        => "PA",
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '10.00',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'ach'                      => $ach,
            // 'customerIDType'           => 0
        ])->send();

        $this->assertTrue($response->isSuccessful());

        $this->assertNull($response->getCardReference());

        $this->assertNotNull($response->getAuthorizationNumber());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals($id,$response->getTransactionId());


        $this->assertEquals($response->getCode(),"E");
        $this->assertEquals($response->getResponseCode(),"E");
        $this->assertEquals($response->getBankResponseCode(),null);

        $this->assertEquals($response->getMessage(),"ACCEPTED");
        $this->assertEquals($response->getResponseMessage(),"ACCEPTED");

        $this->assertEquals($response->getCvvResponse(),null);
        $this->assertEquals($response->getAvsResponse(),null);

        $this->assertEquals($response->getCardType(),null);
        $this->assertEquals($response->getCardNumber(),null);
    }

    public function test_ach_purchase_with_address()
    {

        $AN = rand(0,9999999999);
        $CN = rand(0,9999);
        $id = Gateway::generateUniqueOrderId();

        $ach = new Ach([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'routingNumber'        => '021000021',
            'accountNumber'        => $AN,
            'checkNumber'          => $CN,
            'checkType'            => 'CHECKING',
            'address1'             => '1 Scrubby Creek Road',
            'country'              => 'AU',
            'city'                 => 'Scrubby Creek',
            'postcode'             => '4999',
            'state'                => 'PA',
            'phone'                => '5551234567',
            'email'                => 'example@email.com'
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '10.00',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'ach'                      => $ach,
        ])->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function test_ach_purchase_with_auth()
    {
        $AN = rand(0,9999999999);
        $CN = rand(0,9999);
        $id = Gateway::generateUniqueOrderId();

        $ach = new Ach([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'routingNumber'        => '021000021',
            'accountNumber'        => $AN,
            'checkNumber'          => $CN,
            'checkType'            => 'C',
            'address1'             => '1 Scrubby Creek Road',
            'country'              => 'AU',
            'city'                 => 'Scrubby Creek',
            'postcode'             => '4999',
            'state'                => 'PA',
            'phone'                => '5551234567',
            'email'                => 'example@email.com',
            'license'              => '123456',
            'license_state'        => 'PA',
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '10.00',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'ach'                      => $ach,
        ])->send();

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * An exception was thrown before the request was made because of invalid input
     */
    public function test_ach_purchase_exception()
    {
        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage("The amount parameter is required");

        $AN = rand(0,9999999999);
        $CN = rand(0,9999);

        $ach = new Ach([
            'firstName'            => 'Example',
            'lastName'             => 'Customerx',
            'routingNumber'        => '021000021',
            'accountNumber'        => $AN,
            'checkNumber'          => $CN,
            'checkType'            => 'CHECKING',
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            // 'amount'                   => '10.00',
            'transactionId'            => 12345,
            'clientIp'                 => "1.2.3.4",
            'ach'                     => $ach,
        ])->send();

    }

    /**
     * An exception was thrown before the request was made because of invalid input
     */
    public function test_ach_purchase_ach_exception()
    {
        $this->expectException(InvalidAchException::class);
        $this->expectExceptionMessage("The routing number is required");
        $ach = new Ach([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            // 'routingNumber'        => '021000021',
            'accountNumber'        => '2020',
            'checkNumber'          => '123',
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '10.00',
            'transactionId'            => 12345,
            'clientIp'                 => "1.2.3.4",
            'check'                     => $ach,
        ])->send();

    }

    /**
     * An exception was thrown because the response came back in a bad format
     */
    public function test_ach_purchase_error()
    {

        $this->expectException(InvalidNuveiResponseException::class);
        $this->expectExceptionMessage("Order Already Processed");

        $id = Gateway::generateUniqueOrderId();

        $AN = rand(0,9999999999);
        $CN = rand(0,9999);

        $ach = new Ach([
            'firstName'            => 'Example',
            'lastName'             => 'Customerx',
            'routingNumber'        => '021000021',
            'accountNumber'        => $AN,
            'checkNumber'          => $CN,
            'checkType'            => 'CHECKING',
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '100.05',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'ach'                     => $ach,
        ])->send();

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '100.05',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'ach'                     => $ach,
        ])->send();
    }

    /**
     * No exception thrown but the payment was unsuccessful
     */
    public function test_ach_purchase_failure()
    {
        $ach = new Ach([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'routingNumber'        => '021000021',
            'accountNumber'        => '2020',
            'checkNumber'          => '123',
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '5299.00',
            'transactionId'            => 12345,
            'clientIp'                 => "1.2.3.4",
            'ach'                     => $ach,
        ])->send();

        $this->assertFalse($response->isSuccessful());

        $this->assertNull($response->getAuthorizationNumber());
        $this->assertNull($response->getCardReference());

        $this->assertNotNull($response->getTransactionTag());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertNotNull($response->getSequenceNo());

        $this->assertEquals($response->getCode(),"00");
        $this->assertEquals($response->getMessage(),"Transaction not approved");
        $this->assertEquals($response->getBankCode(),"299");
        $this->assertEquals($response->getExactMessage(),"Transaction Normal");
        $this->assertEquals($response->getBankMessage(),"Transaction not approved");
    }
}
