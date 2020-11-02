<?php
/**
 * Nuvei Purchase Request
 */
namespace Omnipay\Nuvei\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Nuvei\Base\XmlAchPaymentRequest;
use Omnipay\Nuvei\Base\XmlCardPaymentRequest;
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
class NuveiPurchaseRequest extends AbstractNuveiRequest
{

    protected $action = self::TRAN_PURCHASE;
    const ACCOUNT_TYPE = [
        "C" => "CHECKING",
        "S" => "SAVINGS"
    ];

    public function getData()
    {
        $data = parent::getData();

        if($this->isCard()){
            return $this->getCardData($data);
        }else if ($this->isAch()){
            return $this->getAchData($data);
        }
        throw new InvalidRequestException('Invalid Payment Method (Must be "card" or "check")');

    }

    protected function getCardData($data){
        $this->validate('amount','transactionId', 'card');
        $this->getCard()->validate();

        $request = new XmlCardPaymentRequest(
            $this->getTerminalId(),
            $this->getTransactionId(),
            $this->getCurrency(),
            $this->getAmount(),
            $this->getCard()->getNumber(),
            self::getCardType($this->getCard()->getBrand())
        );

        $request->SetNonSecureCardCardInfo(
            $this->getCard()->getExpiryDate("my"),
            $this->getCard()->getName()
        );
        $request->SetCvv($this->getCard()->getCvv());

        $request->SetAvs(
            $this->getCard()->getAddress1(),
            $this->getCard()->getAddress2(),
            $this->getCard()->getPostcode(),
        );
        $request->SetCity($this->getCard()->getCity());
        $request->SetRegion($this->getCard()->getState());
        $request->SetCountry($this->getCard()->getCountry());
        $request->SetPhone($this->getCard()->getPhone());

        $request->SetEmail($this->getCard()->getEmail());
        $request->SetIPAddress($this->getClientIp());
        $request->SetDescription($this->getDescription());

        $request->SetAutoReady($this->getAutoReady());
        $request->SetIssueNo($this->getIssueNo());
        $request->SetMpiref($this->getMpiref());
        $request->SetDeviceID($this->getDevice());

        if($this->getMulticur()){
            //Must come before SetHash
            $request->SetMultiCur();
        }
        $request->SetHash($this->getSecret());

        return $request->toArray();
    }

    protected function getAchData($data){
        $this->validate('amount', 'ach');
        $this->getAch()->validate();

        $request = new XmlAchPaymentRequest(
            $this->getTerminalId(),
            $this->getTransactionId(),
            $this->getCurrency(),
            $this->getAmount(),
            $this->getAch()->getAccountNumber(),
            $this->getAch()->getRoutingNumber(),
            $this->getAch()->getName()
        );


        $request->SetAccountType($this->getAccountType());


        $request->setCheckNumber($this->getAch()->getCheckNumber());


        $request->SetAvs(
            $this->getAch()->getAddress1(),
            $this->getAch()->getAddress2(),
            $this->getAch()->getPostcode(),
        );
        $request->SetCity($this->getAch()->getCity());
        $request->SetRegion($this->getAch()->getState());
        $request->SetCountry($this->getAch()->getCountry());
        $request->SetPhone($this->getAch()->getPhone());

        $request->SetEmail($this->getAch()->getEmail());
        $request->SetIPAddress($this->getClientIp());
        $request->SetDescription($this->getDescription());

        $request->SetLicenseNumber($this->getAch()->getLicense());
        $request->SetLicenseState($this->getAch()->getLicenseState());

        $request->SetHash($this->getSecret());

        return $request->toArray();
    }



    public function getTokenCardType()
    {
        return $this->getParameter('tokenCardType');
    }

    public function setTokenCardType($value)
    {
        return $this->setParameter('tokenCardType', $value);
    }

    public function getAccountType(){
        $checkType = strtoupper($this->getAch()->getCheckType());
        if($checkType != null && isset(static::ACCOUNT_TYPE[$checkType])){
            $checkType = static::ACCOUNT_TYPE[$checkType];
        }
        return $checkType;
    }
}

