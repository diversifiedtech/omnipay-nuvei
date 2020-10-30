<?php

namespace Omnipay\Nuvei\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Nuvei\Message\NuveiPurchaseRequest;

class NuveiPurchaseRequestTest extends TestCase
{

    public function testPurchaseSuccess()
    {
        $request = new NuveiPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'amount' => '12.00',
            'card' => $this->getValidCard(),
            'currency' => "USD",
            'transactionId' => "123"
        ]);

        // $this->assertEquals(31,$request->getApiVersion());
        $data = $request->getData();

        $this->assertEquals('2', $data['TERMINALTYPE']);
        $this->assertEquals('7', $data['TRANSACTIONTYPE']);

        $this->assertEquals('123', $data['ORDERID']);
        $this->assertEquals('12.00', $data['AMOUNT']);
        $this->assertEquals('4111111111111111', $data['CARDNUMBER']);
        $this->assertEquals('VISA', $data['CARDTYPE']);
        $this->assertEquals('USD', $data['CURRENCY']);
        $this->assertEquals('Example User',$data['CARDHOLDERNAME']);

        $this->assertNotNull($data['CARDEXPIRY']);
        $this->assertNotNull($data['CVV']);
        $this->assertNotNull($data['HASH']);
    }

    public function testPurchaseSuccessAddressAddition()
    {
        $request = new NuveiPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'amount' => '12.00',
            'card' => array_merge($this->getValidCard(),["email" => "example@mail.com"]),
            'currency' => "USD",
            'transactionId' => "123",
            "description" => "To Make Payment",
            'clientIp' => '1.2.3.4',
        ]);
        $request->setAutoReady("Y");
        $request->setIssueNo("1234");
        $request->setMpiref("MPIREF");
        $request->setDevice("1234ABC");

        $data = $request->getData();

        $this->assertEquals('2', $data['TERMINALTYPE']);
        $this->assertEquals('7', $data['TRANSACTIONTYPE']);

        $this->assertEquals('123', $data['ORDERID']);
        $this->assertEquals("123 Billing St",$data["ADDRESS1"]);
        $this->assertEquals("Billsville",$data["ADDRESS2"]);
        $this->assertEquals("12345",$data["POSTCODE"]);

        $this->assertEquals("To Make Payment",$data["DESCRIPTION"]);

    }



    public function testPurchaseSuccessMaestroType()
    {
        $options = [
            'amount' => '12.00',
            'card' => $this->getValidCard(),
            'transactionId' => "123",
            'currency' => 'USD'
        ];

        $options['card']['number'] = '6304000000000000';

        $request = new NuveiPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($options);

        $data = $request->getData();

        $this->assertEquals('2', $data['TERMINALTYPE']);
        $this->assertEquals('7', $data['TRANSACTIONTYPE']);

        $this->assertEquals('MAESTRO', $data['CARDTYPE']);
    }
}
