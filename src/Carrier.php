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
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setApiKey($value)
    {
        return $this->setParameter('apiKey', $value);
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
