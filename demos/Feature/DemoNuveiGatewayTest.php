<?php

namespace Omnipay\FirstData\Feature;

use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\FirstData\Ach;
use Omnipay\FirstData\Exception\InvalidAchException;
use Omnipay\Omnipay;
use Omnipay\Tests\GatewayTestCase;
use PHPUnit\Framework\TestCase;

class DemoNuveiGatewayTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = Omnipay::create('Nuvei');

        var_dump($this->gateway);
        die();
        $this->gateway->initialize([
            'terminalid' => $_ENV['DEMO_GATEWAYID'],
            'orderid' => $_ENV['DEMO_PASSWORD'],
            'currency' => $_ENV['DEMO_HMAC'],
            'amount' => $_ENV['DEMO_KEYID'],
                        'gateway' => $_ENV['DEMO_KEYID'],

            'testMode'  => true,
        ]);

    }

    public function test_do_the_thing(){

    }

}
