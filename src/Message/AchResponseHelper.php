<?php
/**
 * Nuvei Response
 */
namespace Omnipay\Nuvei\Message;

/**
 * Nuvei Response Extras
 */
trait AchResponseHelper{


    /**
     * Get the email address
     *
     * @return string
     */
    public function getCheckNumber()
    {
        return $this->getDataItem('check_number');
    }

    /**
     * Get the check type
     *
     * @return string
     */
    public function getCheckType()
    {
        return $this->getDataItem('check_type');
    }

    /**
     * Get the release type
     *
     * @return char
     */
    public function getReleaseType()
    {
        return $this->getDataItem('release_type');
    }

    /**
     * Get the vip
     *
     * @return boolean
     */
    public function getVip()
    {
        return $this->getDataItem('vip');
    }

    /**
     * Get the clerk ID
     *
     * @return boolean
     */
    public function getClerk()
    {
        return $this->getDataItem('clerk_id');
    }

    /**
     * Get the MICR
     *
     * @return boolean
     */
    public function getMicr()
    {
        return $this->getDataItem('micr');
    }

    /**
     * Get the E-commerce Flag
     *
     * @return boolean
     */
    public function getEcommerceFlag()
    {
        return $this->getDataItem('ecommerce_flag');
    }

    /**
     * Get text of receipt
     *
     * @return boolean
     */
    public function getCtr()
    {
        return $this->getDataItem('ctr');
    }
}
