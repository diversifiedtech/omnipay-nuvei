<?php
/**
 * Nuvei XML Response
 */

namespace Omnipay\Nuvei\Message;

use Exception;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Nuvei\Base\XmlPaymentResponse;
use Omnipay\Nuvei\Exception\InvalidNuveiResponseException;
use Omnipay\Nuvei\Message\AbstractNuveiRequest;
use Omnipay\Nuvei\Message\AchResponseHelper;
use Omnipay\Nuvei\Message\ResponseHelper;

/**
 * Nuvei XML Response
 *
 * ### Quirks
 *
 * This gateway requires both a transaction reference (aka an authorization number)
 * and a transaction tag to implement either voids or refunds.  These are referred
 * to in the documentation as "tagged refund" and "tagged voids".
 *
 * The transaction reference returned by this class' getTransactionReference is a
 * concatenated value of the authorization number and the transaction tag.
 */
class NuveiResponse extends AbstractResponse
{
    // use ResponseHelper, AchResponseHelper;
    const INVALID_RESPONSE_STRING = "Invalid Response";
    protected $isCard = false;

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;

        $this->isCard = $this->request->isCard();

        // var_dump($data);
// die();
        $this->data = new XmlPaymentResponse($data);

    }

    protected function checkHash(RequestInterface $request, $response){
        if($this->expectedHash($request) !=  $this->data->Hash()){
            throw new \Exception();
        }
    }

    protected function expectedHash(RequestInterface $request){
        return md5(
            $request->getTerminalId() .
            $this->data->UniqueRef() .
            ($request->getMulticur() == true ? $request->getCurrency() : '') .
            $request->getAmount() .
            $this->data->DateTime() .
            $this->data->ResponseCode() .
            $this->data->ResponseText() .
            $request->getSecret()
        );
    }

    /**
     * Check Successful status
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        $code = $this->isCard ? "A" : "E";
        return ($this->data->ResponseCode() == $code) ? true : false;

    }

    /**
     * Get Data
     *
     * Gets an array of data from the response
     *
     * @return array
     */
    public function getData(){
        return $this->data->getData();
    }

    /**
     * AVS response
     *
     * Note that transactions can still be authorised, even if the AVS response is no match
     * or if there is a failed response. AVS responses are for indication to the merchant
     * only and usually they do not influence the overall authorisation result.
     * This can vary per the cardholders bank though (i.e. the issuing bank).
     *
     * A - Address matches, ZIP does not. The first five numerical characters contained
     * in the address match with those stored at the VIC or issuer’s center.
     * However, the zip code does not match.
     * E - Ineligible transaction.
     * N - Neither address nor ZIP matches. Neither the first five numerical characters
     * contained in the address match with those stored at the VIC nor the
     * issuer’s center, nor the zip code match.
     * R - Retry (system unavailable or timed out).
     * S - Card type not supported. The card type for this transaction is not supported by AVS.
     * AVS can verify addresses for Visa cards, MasterCard, proprietary cards,
     * and private label transactions.
     * U - Address information unavailable.
     * G - Address information unavailable, International - Visa Only.
     * The address information was not available at the VIC or the issuer’s center.
     * W - Nine-digit zip match, address does not match. The nine-digit Postal
     * zip code matches that stored at the VIC or the card issuer's center.
     * However, the first five numerical characters contained in the address do not match.
     * X - Exact match (nine-digit zip and address). Both the nine-digit zip and postal code.
     *
     * @return integer
     */
    public function getAvsResponse()
    {
        return $this->data->AvsResponse();
    }

    /**
     * CVV response
     *
     * Note that transactions can still be authorised even if the CVV response is no match or if there
     * is a failure in response. CVV responses are for indication to the merchant only,
     * and they usually do not influence the overall authorisation result.
     * This can vary per the cardholder's bank, though (i.e. the issuing bank).
     *
     * M - CVV Match.
     * N - CVV No Match.
     * P - Not Processed.
     * S - CVV should be on the card but the merchant indicates it is not.
     * U - User is unregistered.
     *
     * @return integer
     */
    public function getCvvResponse()
    {
        return $this->data->CvvResponse();
    }

    /**
     * Get the authorization number
     *
     * This is the authorization number returned by the cardholder’s financial
     * institution when a transaction has been approved. This value overrides any
     * value sent for the Request Property of the same name.
     *
     * @return mixed
     */
    public function getAuthorizationNumber()
    {
        return $this->data->ApprovalCode();
    }

    /**
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        return $this->data->UniqueRef();
    }

    /**
     * Get the transaction ID as generated by the merchant website.
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->request->getTransactionId();
    }

    /**
     * Get Message
     *
     * A human readable message response and if not then the bank message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->getResponseMessage();
    }

    /**
     * Get Bank Response Message
     *
     * A message provided by the financial institution describing the Response code above.
     *
     * @return string
     */
    public function getResponseMessage(){
        return $this->data->ResponseText();
    }

    /**
     * Get the error code.
     *
     * This property indicates the processing status of the transaction. Please refer
     * to the section on Exception Handling for further information. The Transaction_Error
     * property will return True if this property is not “00”.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getResponseCode();
    }

    /**
     * Get the response code.
     *
     * @return string
     */
    public function getResponseCode()
    {
        return $this->data->ResponseCode();
    }

    /**
     * Get the bank response code.
     *
     * @return string
     */
    public function getBankResponseCode()
    {
        return $this->data->BankResponseCode();
    }

    /**
     * Get the credit card reference for a completed transaction.
     *
     * @return string
     */
    public function getCardReference()
    {
        return null;
    }

    /**
     * gets the card type
     *
     * @return string
     */
    public function getCardType()
    {
        $card = $this->request->getCard();
        if($card == null){
            return null;
        }
        return AbstractNuveiRequest::getCardType($this->request->getCard()->getBrand());
    }

    /**
     * gets the last four of card number
     *
     * @return string
     */
    public function getCardNumber()
    {
        $card = $this->request->getCard();
        if($card == null){
            return null;
        }
        $num = $card->getNumber();
        return str_repeat('#', strlen($num) - 4) . substr($num, -4);
    }

}
