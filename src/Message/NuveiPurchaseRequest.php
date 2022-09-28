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

        if($this->isCard() || $this->isStoredCard()){
            return $this->getCardData($data);
        }else if ($this->isAch() || $this->isStoredAch()){
            return $this->getAchData($data);
        }
        throw new InvalidRequestException('Invalid Payment Method (Must be "card" or "check")');

    }

    protected function getCardData($data){
        if($this->isStoredCard()){
            $this->validate('amount','transactionId', 'storedCard');
            $card = $this->getStoredCard();
        }else{
            $this->validate('amount','transactionId', 'card');
            $card = $this->getCard();
        }

        $card->validate();

        $request = new XmlCardPaymentRequest(
            $this->getTerminalId(),
            $this->getTransactionId(),
            $this->getCurrency(),
            $this->getAmount(),
            $card->getNumber(),
            self::getCardType($card->getBrand())
        );

        $request->SetNonSecureCardCardInfo(
            $card->getExpiryDate("my"),
            $card->getName()
        );

        $request->SetCvv($card->getCvv());

        $request->SetAvs(
            $card->getAddress1(),
            $card->getAddress2(),
            $card->getPostcode()
        );
        $request->SetCity($card->getCity());
        $request->SetRegion($card->getState());
        $request->SetCountry($card->getCountry());
        $request->SetPhone($card->getPhone());

        $request->SetEmail($card->getEmail());
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
        if($this->isStoredAch()){
            $this->validate('amount','transactionId', 'storedAch');
            $ach = $this->getStoredAch();
        }else{
            $this->validate('amount','transactionId', 'ach');
            $ach = $this->getAch();
        }
        $ach->validate();
        $request = new XmlAchPaymentRequest(
            $this->getTerminalId(),
            $this->getTransactionId(),
            $this->getCurrency(),
            $this->getAmount(),
            $ach->getAccountNumber(),
            $ach->getRoutingNumber(),
            $ach->getName()
        );

        $request->setIsStoredAch($this->isStoredAch());


        $request->SetAccountType($this->getAccountType($ach));


        $request->setCheckNumber($ach->getCheckNumber());


        $request->SetAvs(
            $ach->getAddress1(),
            $ach->getAddress2(),
            $ach->getPostcode()
        );
        $request->SetCity($ach->getCity());
        $request->SetRegion($ach->getState());
        $request->SetCountry($ach->getCountry());
        $request->SetPhone($ach->getPhone());

        $request->SetEmail($ach->getEmail());
        $request->SetIPAddress($this->getClientIp());
        $request->SetDescription($this->getDescription());

        $request->SetLicenseNumber($ach->getLicense());
        $request->SetLicenseState($ach->getLicenseState());

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

    public function getAccountType($ach){
        $checkType = strtoupper($ach->getCheckType());
        if($checkType != null && isset(static::ACCOUNT_TYPE[$checkType])){
            $checkType = static::ACCOUNT_TYPE[$checkType];
        }
        return $checkType;
    }
}

