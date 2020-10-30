<?php

namespace Omnipay\Nuvei\Message;

use Omnipay\Nuvei\Message\NuveiAuthorizeRequest;
use Omnipay\Tests\TestCase;

class NuveiAuthorizeRequestTest extends TestCase
{

    public function testAuthorizeSuccess()
    {
        $request = new NuveiAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'amount' => '12.00',
            'card' => $this->getValidCard(),
            'currency' => "USD",
            'transactionId' => "123"
        ]);

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
}
