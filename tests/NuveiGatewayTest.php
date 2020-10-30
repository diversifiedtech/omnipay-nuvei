<?php

namespace Omnipay\Nuvei;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Nuvei\Gateway;
use Omnipay\Tests\GatewayTestCase;

class NuveiGatewayTest extends GatewayTestCase
{
    /** @var  Gateway */
    protected $gateway;

    /** @var  array */
    protected $options;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTerminalId('12345');
        $this->gateway->setSecret('secret');
        $this->gateway->setCurrency("USD");

        $this->options = array(
            'amount' => '13.00',
            'card' => $this->getValidCard(),
            'transactionId' => 'order2',
            'currency' => 'USD',
            'testMode' => true,
        );
    }

    public function testProperties()
    {
        $this->assertEquals('12345', $this->gateway->getTerminalId());
        $this->assertEquals('secret', $this->gateway->getSecret());
        $this->assertEquals('USD', $this->gateway->getCurrency());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $response = $this->gateway->purchase($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getCardReference());
        $this->assertEquals("VYYYYM",$response->getAuthorizationNumber());
        $this->assertEquals("H4U37ASO8I",$response->getTransactionReference());
        $this->assertEquals('order2',$response->getTransactionId());

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

    public function testAuthorizeSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $response = $this->gateway->authorize($this->options)->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getCardReference());
        $this->assertEquals("VYYYYM",$response->getAuthorizationNumber());
        $this->assertEquals("H4U37ASO8I",$response->getTransactionReference());
        $this->assertEquals('order2',$response->getTransactionId());

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
    public function testPurchaseFailureMissingAmount(){

        $this->expectException(InvalidRequestException::class);

        unset($this->options['amount']);
        $response = $this->gateway->purchase($this->options)->send();
    }

    public function testPurchaseFailureInvalidCard(){
        $this->expectException(InvalidCreditCardException::class);
        $this->options['card'] = new CreditCard([
            'firstName'            => 'Example',
            'lastName'             => 'Customer',
            'expiryMonth'          => '12',
            'expiryYear'           => '2019',
            'cvv'                  => '123',
        ]);
        $response = $this->gateway->purchase($this->options)->send();
    }
}
