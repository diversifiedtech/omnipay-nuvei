<?php

namespace Omnipay\Nuvei\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Nuvei\Exception\InvalidNuveiResponseException;
use Omnipay\Nuvei\Message\NuveiPurchaseRequest;
use Omnipay\Tests\TestCase;

class NuveiPurchaseResponseTest extends TestCase
{
    public function testPurchaseSuccess()
    {
        $mock = $this->getMockRequest();
        $mock->shouldReceive('getTransactionId')->once()->andReturn("12345");
        $response = new NuveiResponse($this->getMockRequest(), file_get_contents(__DIR__ . "/../Xml/NuveiSuccess.xml"));

        $this->assertTrue($response->isSuccessful());

        $this->assertEquals('A', $response->getCode());
        $this->assertEquals('APPROVAL', $response->getMessage());

        $this->assertEquals('A', $response->getResponseCode());
        $this->assertEquals('APPROVAL', $response->getResponseMessage());

        $this->assertEquals('U', $response->getAvsResponse());
        $this->assertEquals('M', $response->getCvvResponse());

        $this->assertEquals('12345', $response->getTransactionId());
        $this->assertEquals('H4U37ASO8I', $response->getTransactionReference());
        $this->assertEquals('VYYYYM', $response->getAuthorizationNumber());

        $this->assertEquals([
            'uniqueRef' => 'H4U37ASO8I',
            'responseCode' => 'A',
            'responseText' => 'APPROVAL',
            'approvalCode' => 'VYYYYM',
            'bankResponseCode' => '00',
            'dateTime' => '2020-10-29T18:57:34',
            'avsResponse' => 'U',
            'cvvResponse' => 'M',
            'hash' => '27d608b0dfa791c6533918bdaedc7b90'
        ],$response->getData());

    }

    public function testPurchaseError()
    {
        $mock = $this->getMockRequest();
        $mock->shouldReceive('getTransactionId')->once()->andReturn("12345");

        $response = new NuveiResponse($this->getMockRequest(),file_get_contents(__DIR__ . "/../Xml/NuveiFailure.xml"));

        $this->assertFalse($response->isSuccessful());

        $this->assertEquals('D', $response->getCode());
        $this->assertEquals('Do Not Honor', $response->getMessage());

        $this->assertEquals('D', $response->getResponseCode());
        $this->assertEquals('Do Not Honor', $response->getResponseMessage());

        $this->assertEquals('U', $response->getAvsResponse());
        $this->assertEquals('M', $response->getCvvResponse());

        $this->assertEquals('12345', $response->getTransactionId());
        $this->assertEquals('KMQ1AWDQKC', $response->getTransactionReference());
        $this->assertEquals('', $response->getAuthorizationNumber());

    }

    public function testGatewayError()
    {
        $xmlResponse = file_get_contents(__DIR__ . "/../Xml/NuveiError.xml");
        try{
            $response = new NuveiResponse($this->getMockRequest(),$xmlResponse);

        }catch(\Exception $e){
            $this->assertTrue($e instanceOf InvalidNuveiResponseException);
            $this->assertEquals("Invalid HASH field",$e->getMessage());
            $this->assertEquals(0,$e->getCode());
            $this->assertEquals("E",$e->getErrorCode());
            $this->assertEquals($xmlResponse,$e->getRawXml());
        }
    }

    public function testGatewayResponseNotXML()
    {
        $this->expectException(InvalidResponseException::class);
        $this->expectExceptionMessage("Invalid Xml Response");

        $response = new NuveiResponse($this->getMockRequest(),"There was a problem");
    }
}
