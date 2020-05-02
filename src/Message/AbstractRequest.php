<?php

namespace Omniship\DPD\Message;

use http\Client\Response;
use yii\base\BaseObject;

abstract class AbstractRequest extends \Omniship\Common\Message\AbstractRequest
{
    const POST = 'POST';
    const GET  = 'GET';

    const PKG_NUMS_GEN_ERR_POLICY = "ALL_OR_NOTHING"; //STOP_ON_FIRST_ERROR, IGNORE_ERRORS
    const PKG_SPLB_GEN_ERR_POLICY = "STOP_ON_FIRST_ERROR"; // IGNORE_ERRORS
    const PKG_PROT_GEN_ERR_POLICY = "IGNORE_ERRORS";
    const PKG_PICK_GEN_ERR_POLICY = "IGNORE_ERRORS";

//    const BASE_URL      = 'https://shipper-ws.dpd.ch/soap/wsdl/';
    const BASE_URL      = 'https://dpdservicesdemo.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?WSDL';
    const BASE_URL_TEST = 'https://dpdservicesdemo.dpd.com.pl/DPDPackageObjServicesService/DPDPackageObjServices?WSDL';

    /**
     * @var string
     */
    protected $apiVersion = "1";


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
     * Get auth data
     * @return array
     */
    protected function _authData()
    {
        return [
            'masterFid' => $this->getFid(),
            'login'     => $this->getUsername(),
            'password'  => $this->getPassword(),
        ];
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return Response
     */
    protected function sendRequest($method, $endpoint, array $params = null)
    {

        $url = "{$this->getEndpoint()}{$endpoint}";

        $obj         = new \stdClass();
        $obj->method = 'generatePackagesNumbersV' . $this->apiVersion;

        try {

            $soapClient = new \SoapClient($this->getEndpoint());
            // api method call
            $response = $soapClient->__soapCall('generatePackagesNumbersV' . $this->apiVersion, [$params]);

//            echo "<pre>";
//            print_r($result);
//            echo "</pre>";
//            die;

//            // debug results
//            if (isset($this->config->debug) && $this->config->debug) {
//                var_dump($result);
//            }

            // get status
//            $status = ($this->apiVersion > 1) ? $result->return->Status : $result->return->status;
//
//            // check status
//            if ($status == 'OK') {
//
//                $this->sessionId = ($this->apiVersion > 1) ? $result->return->SessionId : $result->return->sessionId;
//
//                $obj->success   = true;
//                $obj->sender    = $this->getSender();
//                $obj->packageId = ($this->apiVersion > 1) ? $result->return->Packages->PackageId : $result->return->packages->packageId;
//                $obj->parcels   = ($this->apiVersion > 1) ? $result->return->Packages->Parcels->Parcel : $result->return->packages->parcels;
//
//            } else {
//                $obj->success = false;
//            }
//
//            return $obj;

        } catch (SoapFault $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        return $this->createResponse(json_decode(json_encode($response), true));
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
