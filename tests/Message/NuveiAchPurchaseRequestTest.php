<?php

namespace Omnipay\Nuvei\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Nuvei\Message\NuveiPurchaseRequest;

class NuveiAchPurchaseRequestTest extends TestCase
{

    public function testPurchaseSuccess()
    {
        $request = new NuveiPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'amount' => '12.00',
            'check' => array_merge($this->getValidAch(),["checkType" => "checking"]),
            'currency' => "USD",
            'transactionId' => "123"
        ]);
        $data = $request->getData();

        $this->assertEquals('2', $data['TERMINALTYPE']);

        $this->assertEquals('123', $data['ORDERID']);
        $this->assertEquals('12.00', $data['AMOUNT']);
        $this->assertEquals('WEB', $data['SEC_CODE']);

        $this->assertEquals('CHECKING', $data['ACCOUNT_TYPE']);

        $this->assertEquals('123456789', $data['ACCOUNT_NUMBER']);
        $this->assertEquals('021000021', $data['ROUTING_NUMBER']);
        $this->assertEquals('Example User', $data['ACCOUNT_NAME']);
        $this->assertNotNull($data['CHECK_NUMBER']);

        $this->assertEquals('USD', $data['CURRENCY']);
        $this->assertNotNull($data['DATETIME']);
        $this->assertNotNull($data['HASH']);
    }

    public function testPurchaseSuccessWithoutCheckType()
    {
        $request = new NuveiPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'amount' => '12.00',
            'check' => array_merge($this->getValidAch(),[]),
            'currency' => "USD",
            'transactionId' => "123"
        ]);
        $data = $request->getData();
        $this->assertEquals(null, $data['ACCOUNT_TYPE']);
    }

    public function testPurchaseSuccessAddressAddition()
    {
        $request = new NuveiPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'amount' => '12.00',
            'ach' => array_merge($this->getValidAch(),["email" => "example@mail.com","checkType" => "S"]),
            'currency' => "USD",
            'transactionId' => "123",
            "description" => "To Make Payment",
            'clientIp' => '1.2.3.4',
        ]);

        $data = $request->getData();

        var_dump($data);

        $this->assertEquals('2', $data['TERMINALTYPE']);

        $this->assertEquals('123', $data['ORDERID']);
        $this->assertEquals('12.00', $data['AMOUNT']);
        $this->assertEquals('WEB', $data['SEC_CODE']);

        $this->assertEquals('SAVINGS', $data['ACCOUNT_TYPE']);

        $this->assertEquals('123456789', $data['ACCOUNT_NUMBER']);
        $this->assertEquals('021000021', $data['ROUTING_NUMBER']);
        $this->assertEquals('Example User', $data['ACCOUNT_NAME']);
        $this->assertNotNull($data['CHECK_NUMBER']);

        $this->assertEquals('123 Billing St', $data['ADDRESS1']);
        $this->assertEquals('Billsville', $data['ADDRESS2']);
        $this->assertEquals('12345', $data['POSTCODE']);

        $this->assertEquals('US', $data['COUNTRY']);
        $this->assertEquals('(555) 123-4567', $data['PHONE']);
        $this->assertEquals('1.2.3.4', $data['IPADDRESS']);
        $this->assertEquals('example@mail.com', $data['EMAIL']);

        $this->assertEquals('To Make Payment', $data['DESCRIPTION']);

        $this->assertNotNull($data['DATETIME']);
        $this->assertNotNull($data['HASH']);

    }

    public function testPurchaseSuccessWithVerification()
    {
        $addons = [
            "license" => "123456",
            "licenseState" => "PA",
        ];

        $request = new NuveiPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize([
            'amount' => '12.00',
            'ach' => array_merge($this->getValidAch(),$addons),
            'currency' => "USD",
            'transactionId' => "123",
            "description" => "To Make Payment",
            'clientIp' => '1.2.3.4',
        ]);

        $data = $request->getData();

        var_dump($data);

        $this->assertEquals('2', $data['TERMINALTYPE']);

        $this->assertEquals('123', $data['ORDERID']);
        $this->assertEquals('12.00', $data['AMOUNT']);
        $this->assertEquals('WEB', $data['SEC_CODE']);


        $this->assertEquals('123456',$data['DL_NUMBER']);
        $this->assertEquals('PA',$data['DL_STATE']);

    }

    /**
     * Helper method used by gateway test classes to generate a valid test credit card
     */
    private function getValidAch()
    {
        return array(
            'firstName' => 'Example',
            'lastName' => 'User',
            'routingNumber' => '021000021',
            'accountNumber' => '123456789',
            'checkNumber' => rand(100,999),
            'billingAddress1' => '123 Billing St',
            'billingAddress2' => 'Billsville',
            'billingCity' => 'Billstown',
            'billingPostcode' => '12345',
            'billingState' => 'CA',
            'billingCountry' => 'US',
            'billingPhone' => '(555) 123-4567',
            'shippingAddress1' => '123 Shipping St',
            'shippingAddress2' => 'Shipsville',
            'shippingCity' => 'Shipstown',
            'shippingPostcode' => '54321',
            'shippingState' => 'NY',
            'shippingCountry' => 'US',
            'shippingPhone' => '(555) 987-6543',
        );
    }
}
