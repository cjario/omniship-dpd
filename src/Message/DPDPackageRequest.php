<?php namespace Omniship\DPD\Message;

use Omniship\Common\Message\ResponseInterface;
use Omniship\DPD\Message\AbstractRequest;

class DPDPackageRequest extends AbstractRequest
{


    protected $endpoint = '/postage/parcel/domestic/size.json';

    /**
     * @return array
     * @throws \Omniship\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        $this->validate('apiKey');
        return [];
    }


    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     */
    public function sendData($data)
    {
        return $this->sendRequest(self::GET, $this->endpoint, $data);
//        return $this->response = new DPDPackageResponse($this, $response);
    }

    public function createResponse($response)
    {
        return new DPDPackageResponse($this, $response);
    }
}
