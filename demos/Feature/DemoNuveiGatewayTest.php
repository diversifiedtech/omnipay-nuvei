<?php

namespace Omnipay\Nuvei\Feature;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Nuvei\Ach;
use Omnipay\Nuvei\Exception\InvalidAchException;
use Omnipay\Nuvei\Exception\InvalidNuveiResponseException;
use Omnipay\Nuvei\Gateway;
use Omnipay\Omnipay;
use Omnipay\Tests\GatewayTestCase;
use PHPUnit\Framework\TestCase;

class DemoNuveiGatewayTest extends TestCase
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
    public function test_nuvei_purchase_success()
    {
        $card = new CreditCard([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'number'               => '4111111111111111',
            'expiryMonth'          => '12',
            'expiryYear'           => '2026',
            'cvv'                  => '123',
        ]);
        $id = Gateway::generateUniqueOrderId();

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '10.00',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'card'                     => $card,
        ])->send();

        $this->assertTrue($response->isSuccessful());

        $this->assertNull($response->getCardReference());

        $this->assertNotNull($response->getAuthorizationNumber());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals($id,$response->getTransactionId());


        $this->assertEquals($response->getCode(),"A");
        $this->assertEquals($response->getResponseCode(),"A");
        $this->assertEquals($response->getBankResponseCode(),"00");

        $this->assertEquals($response->getMessage(),"APPROVAL");
        $this->assertEquals($response->getResponseMessage(),"APPROVAL");

        $this->assertEquals($response->getCvvResponse(),"M");
        $this->assertEquals($response->getAvsResponse(),"U");

        $this->assertEquals($response->getCardType(),"VISA");
        $this->assertEquals($response->getCardNumber(),"############1111");
    }


    /**
     * Everything was successful
     */
    public function test_purchase_success_with_address()
    {
        $id = Gateway::generateUniqueOrderId();

        $card = new CreditCard([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'number'               => '4111111111111111',
            'expiryMonth'          => '12',
            'expiryYear'           => '2026',
            'cvv'                  => '123',
            'address1'             => '1 Scrubby Creek Road',
            'address2'             => 'Apts.',
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
            'card'                     => $card,
        ])->send();

        $this->assertTrue($response->isSuccessful());

        $this->assertNull($response->getCardReference());

        $this->assertNotNull($response->getAuthorizationNumber());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals($id,$response->getTransactionId());

        $this->assertEquals($response->getCode(),"A");
        $this->assertEquals($response->getResponseCode(),"A");

        $this->assertEquals($response->getMessage(),"APPROVAL");
        $this->assertEquals($response->getResponseMessage(),"APPROVAL");

        $this->assertEquals($response->getCardType(),"VISA");
        $this->assertEquals($response->getCardNumber(),"############1111");

    }


    /**
     * An exception was thrown before the request was made because of invalid input
     */
    public function test_purchase_exception()
    {
        $id = Gateway::generateUniqueOrderId();

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage("The amount parameter is required");
        $card = new CreditCard([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'number'               => '4111111111111111',
            'expiryMonth'          => '12',
            'expiryYear'           => '2019',
            'cvv'                  => '123',
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            // 'amount'                   => '10.00',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'card'                     => $card,
        ])->send();
    }

    /**
     * An exception was thrown before the request was made because of invalid card
     */
    public function test_purchase_credit_card_exception()
    {
        $id = Gateway::generateUniqueOrderId();

        $this->expectException(InvalidCreditCardException::class);
        $this->expectExceptionMessage("The credit card number is required");
        $card = new CreditCard([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            // 'number'               => '4111111111111111',
            'expiryMonth'          => '12',
            'expiryYear'           => '2019',
            'cvv'                  => '123',
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '10.00',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'card'                     => $card,
        ])->send();

    }
    /**
     * An exception was thrown because the response came back in a bad format
     */
    public function test_purchase_error()
    {

        $id = Gateway::generateUniqueOrderId();

        $this->expectException(InvalidNuveiResponseException::class);
        $this->expectExceptionMessage("Order Already Processed");
        $card = new CreditCard([
            'firstName'            => 'test',
            'lastName'             => '',
            'number'               => '4111111111111111',
            'expiryMonth'          => '12',
            'expiryYear'           => '2026',
            'cvv'                  => '123',
        ]);

        $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '10.03',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'card'                     => $card,
        ])->send();
        $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '10.03',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'card'                     => $card,
        ])->send();
    }

    /**
     * No exception thrown but the payment was unsuccessful
     */
    public function test_purchase_failure()
    {
        $id = Gateway::generateUniqueOrderId();

        $card = new CreditCard([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'number'               => '4111111111111111',
            'expiryMonth'          => '12',
            'expiryYear'           => '2026',
            'cvv'                  => '123',
        ]);

        $response = $this->gateway->purchase([
            'description'              => 'Your order for widgets',
            'amount'                   => '1000.05',
            'transactionId'            => $id,
            'clientIp'                 => "1.2.3.4",
            'card'                     => $card,
        ])->send();

        $this->assertFalse($response->isSuccessful());

        $this->assertNull($response->getCardReference());

        $this->assertNotNull($response->getAuthorizationNumber());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals($id,$response->getTransactionId());


        $this->assertEquals($response->getCode(),"D");
        $this->assertEquals($response->getResponseCode(),"D");

        $this->assertEquals($response->getMessage(),"Do Not Honor");
        $this->assertEquals($response->getResponseMessage(),"Do Not Honor");

        $this->assertEquals($response->getCardType(),"VISA");
        $this->assertEquals($response->getCardNumber(),"############1111");
    }

}
