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
        $this->validate('fid', 'username', 'password');
        $requestData = [
            "RequestHeader" => [
                "SpecVersion"    => $this->getSpec(),
                "CustomerId"     => $this->getCustomerId(),
                "RequestId"      => uniqid(),
                "RetryIndicator" => 0,
            ],
            "TerminalId"    => $this->getTerminalId(),
            "Payment"       => [
                "Amount"      => [
                    "Value"        => $this->getAmountInteger(),
                    "CurrencyCode" => $this->getCurrency(),
                ],
                "OrderId"     => $this->getTransactionId(),
                "Description" => $this->getDescription(),
            ],
            "ReturnUrls"    => [
                "Success" => $this->getReturnUrl(),
                "Fail"    => $this->getCancelUrl(),
            ],
        ];

        return $requestData;
    }

    /**
     * Copies the arrays
     * @param array $array
     * @return array
     */
    private function _arrayCopy(array $array)
    {
        $result = array();

        foreach ($array as $key => $value) {
            $result[$key] = (is_array($value) ? $this->_arrayCopy($value) : $value);
        }

        return $result;
    }


    public function createPackage(
        array $sender,
        array $parcels,
        array $receiver,
        $payer = 'SENDER',
        array $services = [],
        $ref = ''
    ) {
        //validate
        if (count($parcels) == 0) {
            throw new Exception('Parcel data are missing', 101);
        }

        if (count($receiver) == 0) {
            throw new Exception('Receiver data are missing', 102);
        }

        if (is_null($sender) || !is_array($sender) || count($sender) == 0) {
            throw new Exception('Sender data are required', 103);
        }

        if (strlen($ref) > 27) {
            throw new Exception('REF field exceeds 27 chars', 104);
        } else {
            $ref = str_split($ref, 9);
        }

        if (strtoupper($payer) != 'SENDER' && strtoupper($payer) != 'RECEIVER') {
            throw new Exception('Wrong payer type (SENDER or RECEIVER)', 105);
        }

        $package = [
            'sender'    => $this->_arrayCopy($sender),
            'payerType' => strtoupper($payer),
            'receiver'  => $this->_arrayCopy($receiver),
            'parcels'   => $this->_arrayCopy($parcels),
            'services'  => $this->_arrayCopy($services),
            'ref1'      => (isset($ref[0]) ? $ref[0] : ''),
            'ref2'      => (isset($ref[1]) ? $ref[1] : ''),
            'ref3'      => (isset($ref[2]) ? $ref[2] : ''),
        ];

        $this->validatePackage($package);

        // return validated data
        return $package;
    }

    /**
     * Validate package
     * @param array $package
     * @return boolean
     */
    public function validatePackage(array $package)
    {
        if (!isset($package['parcels']) || count($package['parcels']) == 0) {
            throw new Exception('Package validation error - missing `parcels` data in package', 101);
        }

        if (!isset($package['sender']) || count($package['sender']) == 0) {
            throw new Exception('Package validation error - missing `sender` data in package', 101);
        }

        if (!isset($package['receiver']) || count($package['receiver']) == 0) {
            throw new Exception('Package validation error - missing `receiver` data in package', 101);
        }

        if (!isset($package['payerType'])) {
            throw new Exception('Package validation error - missing `payerType` field in package', 101);
        }

        $senderReq = ['name', 'address', 'city', 'countryCode', 'postalCode'];
        if (strtoupper($package['payerType']) == 'SENDER') {
            $senderReq[] = 'fid';
        }

        if (count(array_intersect_key(array_flip($senderReq), $package['sender'])) !== count($senderReq)) {
            throw new Exception('Package validation error - Sender requires the fields: ' . implode(',', $senderReq),
                102);
        }

        $receiverReq = ['name', 'address', 'city', 'countryCode', 'postalCode'];
        if (strtoupper($package['payerType']) == 'RECEIVER') {
            $receiverReq[] = 'fid';
        }

        if (count(array_intersect_key(array_flip($receiverReq), $package['receiver'])) !== count($receiverReq)) {
            throw new Exception('Package validation error - Receiver requires the fields: ' . implode(',',
                    $receiverReq), 102);
        }

        $parcelReq = ['weight'];

        foreach ($package['parcels'] as $parcel) {
            if (count(array_intersect_key(array_flip($parcelReq), $parcel)) !== count($parcelReq)) {
                throw new Exception('Package validation error - Parcel requires the fields: ' . implode(',',
                        $parcelReq), 102);
            }
        }

        return true;
    }


    /**
     * Set sender data
     * @param array $sender
     */
    public function setSender(array $sender)
    {
        $this->setParameter('sender', $sender);
    }

    /**
     * Get sender data
     * @return array
     */
    public function getSender()
    {
        return $this->getParameter('sender');
    }

    public function setSessionId($value)
    {
        return $this->setParameter('sessionId', $value);
    }

    /**
     * Get session id
     * @return string
     */
    public function getSessionId()
    {
        return $this->getParameter('sessionId');
    }


    /**
     * Send the request with specified data
     *
     * @param mixed $data The data to send
     */
    public function sendData($data)
    {
        $params = [
            'openUMLV1'                 => [
                'packages' => $this->createPackage($data['sender'], $data['parcel'], $data['receiver'], 'SENDER',
                    $data['services'] ?? [], $data['ref'] ?? null),
            ],
            'pkgNumsGenerationPolicyV1' => self::PKG_NUMS_GEN_ERR_POLICY,
            'authDataV1'                => $this->_authData(),
            'langCode'                  => 'DE'
        ];

        return $this->sendRequest(self::GET, $this->endpoint, $params);
    }


    public function createResponse($response)
    {
        return new DPDPackageResponse($this, $response);
    }
}
