<?php

namespace Omniship\DPD;

use Omniship\Common\AbstractCarrier;
use Omniship\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * DPD Carrier provides a wrapper for DPD API.
 * Please have a look at links below to have a high-level overview and see the API specification
 *
 * @see https://developers.auspost.com.au/apis/pac/getting-started
 *
 */
class Carrier extends AbstractCarrier
{

    public function getName()
    {
        return 'DPD';
    }

    /**
     * @return string
     */
    public function getFid()
    {
        return $this->getParameter('fid');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFid($value)
    {
        return $this->setParameter('fid', $value);
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Set sender data
     * @param array $sender
     */
    public function setSender(array $sender)
    {
        $this->sender = $sender;
    }

    /**
     * Get sender data
     * @return array
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Get session id
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }


    /**
     * Initialize this carrier with default parameters
     *
     * @param array $parameters
     * @return $this
     */
    public function initialize(array $parameters = array())
    {
        $this->parameters = new ParameterBag;

        // set default parameters
        foreach ($this->getDefaultParameters() as $key => $value) {
            if (is_array($value)) {
                $this->parameters->set($key, reset($value));
            } else {
                $this->parameters->set($key, $value);
            }
        }

        Helper::initialize($this, $parameters);


        return $this;
    }

    public function getDefaultParameters()
    {

        $settings = parent::getDefaultParameters();
        return $settings;
    }

    public function package(array $parameters = [])
    {
        return $this->createRequest('\Omniship\DPD\Message\DPDPackageRequest', $parameters);
    }

}
