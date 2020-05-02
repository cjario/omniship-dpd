<?php

namespace Omniship\DPD\Message;

use http\Client\Response;

abstract class AbstractRequest extends \Omniship\Common\Message\AbstractRequest
{
    const POST = 'POST';
    const GET  = 'GET';

    const BASE_URL      = 'https://shipper-ws.dpd.ch/soap/wsdl/';
    const BASE_URL_TEST = 'https://dpdservicesdemo.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?WSDL';

    /**
     * @var string
     */
    protected $apiVersion = "";


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
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return Response
     */
    protected function sendRequest($method, $endpoint, array $data = null)
    {

        $url      = "{$this->getEndpoint()}{$endpoint}";
        $postData = $this->getData();
        $headers  = [
            'Content-Type'  => 'application/json; charset=utf-8',
            'Accept'        => 'application/json',
            'Authorization' => 'Basic ' . $this->getAuthCredentials(),

        ];

        $response = $this->httpClient->request(
            $method,
            $url,
            [
                'AUTH-KEY' => $this->getApiKey()
            ],
            ($data === null || empty($data)) ? null : json_encode($data)
        );

        return $this->createResponse(json_decode($response->getBody()->getContents(), true));
    }

    protected function createResponse($response)
    {
        return $this->response = new Response($this, $response);
    }

    protected function getEndpoint()
    {
        return ($this->getTestMode() ? self::BASE_URL_TEST : self::BASE_URL);
    }

}
