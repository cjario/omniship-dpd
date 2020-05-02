<?php namespace Omniship\DPD\Message;

use Omniship\Common\Message\AbstractResponse;

class DPDPackageResponse extends AbstractResponse
{


    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        if (!empty($this->data) && $this->data['return']['status'] == 'OK') {
            return true;
        }
        return false;
    }

    public function getParcels()
    {
        if (!empty($this->data['return']['packages']['parcels'])) {
            return $this->data['return']['packages']['parcels'];
        }
        return null;
    }

    public function getSessionId()
    {
        if ($this->isSuccessful()) {
            return $this->data['return']['sessionId'];
        }
        return null;
    }

    public function getPackageId()
    {
        if ($this->isSuccessful()) {
            return $this->data['return']['packages']['packageId'];
        }
        return null;
    }


}
