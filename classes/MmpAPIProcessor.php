<?php

/**
 * @package MmproApiV1Package
 * @author Sergio Gamanyuk <bearwebua@gmail.com> https://github.com/bearwebua/
 * @link https://github.com/lisamatch/api-php-client
 * @copyright 2003-2014 Matchmaking Pro, Inc
 * @version 1.0
 */
class MmpAPIProcessor
{

    /**
     * @var string - public key - To get one you need to login to CRM
     */
    protected $publicKey;

    /**
     * @var string - private key - To get one you need to login to CRM. Keep it safe from world
     */
    protected $privateKey;

    /**
     * @var string - absolute path to SSL certificate
     */
    protected $sslCertPath;

    /**
     * @var MmpResponse - Response structure
     */
    protected $responseObj;

    /**
     * @var string - URL to send API requests to
     */
    protected $url = 'https://api.matchmakercrm.com/v1/';

    /**
     * @var string - request resource. For example: 'member/100' OR 'member/100/comments/show'
     */
    protected $requestedRes;

    /**
     * @var string - http method to use for request.
     */
    protected $requestedMethod;

    /**
     * @var array - array with extra data to send with request. For example
     * <pre>
     * array (
     *     'limit' => '100',
     *     'offset' => '50',
     *     'order' => 'id-desc',
     *     'data' => array(
     *        'MemberContactDetails_firstName' => 'John',
     *        'MemberContactDetails_lastName' => 'Smith',
     *        'MemberContactDetails_email' => 'Smith',
     *        'MemberMatchingData_matchGender' => 'female',
     *     )
     * )
     * </pre>
     *
     */
    protected $requestedData;

    /**
     * @var array - array with data ready to be send. It is sorted and has auth params
     */
    protected $preparedData;

    //**************************************************************************

    /**
     * Constructor
     * @param string $publicKey - public key
     * @param string $privateKey - private/secret key
     * @param string $sslCertPath - absolute path to cert file in the system
     */
    public function __construct($publicKey, $privateKey, $sslCertPath)
    {
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->sslCertPath = $sslCertPath;
        $this->responseObj = new MmpResponse;
    }

    /**
     * Prepares data to be sent as well as fires request to API
     * @param string $method - http method. For example: "GET", "POST", "DELETE", "PUT"
     * @param string $res - resource identificator. For example "member/100"
     * @param array $data - array of extra data
     * @return MmpResponse - structure with response data
     */
    public function call($method, $res, $data = array())
    {

        try {

            if ($this->isCertExist() === false) {
                throw new MmpAPIClientException('SSL certificate not exist. Pleae check the path to file', 400);
            }

            $this->requestedRes = $res;
            $this->requestedData = $data;
            $this->requestedMethod = $method;

            // convert resource into assoc array
            $a = explode("/", $res);
            if (empty($a)) {
                throw new MmpAPIClientException('Wrong resource format', 400);
            }
            $requestArray = array();
            for ($n = 1; $n < count($a); $n += 2) {
                $requestArray[$a[$n - 1]] = $a[$n];
            }

            $this->preparedData = array_merge($this->requestedData, $requestArray); //merge request body with resource array
            $this->preparedData['accessKey'] = $this->publicKey;
            $this->preparedData['accessTime'] = time();
            $this->preparedData['accessMethod'] = $method;
            $this->sanitize($this->preparedData);

            ksort($this->preparedData, SORT_STRING); //(ksort -> string sort alphabetically from lowest to highest)

            $this->preparedData['accessHash'] = hash_hmac('sha256', json_encode($this->preparedData), $this->privateKey); // hashing message and append it to request body

            $response = $this->fireRequest();
        } catch (Exception $exc) {
            $response = array(
                'info' => get_class($exc) . ': ' . $exc->getMessage(),
                'httpCode' => $exc->getCode(),
            );
        }

        return $this->responseObj->reply($response);
    }

    /**
     * Converts all values to strings recursively
     * @param array $data
     */
    protected function sanitize(array &$data)
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $this->sanitize($value);
            } else {
                $value = (string) $value;
            }
        }
    }

    /**
     * Check if certificate file exist
     * @return boolean
     */
    protected function isCertExist()
    {
        return file_exists($this->sslCertPath);
    }

    /**
     * Executes http request
     * @throws MmpAPIClientException
     * @return array - decoded API response as assoc. array
     */
    protected function fireRequest()
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "php-api-client-v1");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

        if (strtolower($this->requestedMethod) === 'get') {
            curl_setopt($ch, CURLOPT_URL, $this->url . $this->requestedRes . '?' . http_build_query($this->preparedData));
        } else if (strtolower($this->requestedMethod) === 'post') {
            curl_setopt($ch, CURLOPT_URL, $this->url . $this->requestedRes);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->preparedData));
        } else if (strtolower($this->requestedMethod) === 'delete') {
            curl_setopt($ch, CURLOPT_URL, $this->url . $this->requestedRes . '?' . http_build_query($this->preparedData));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        } else if (strtolower($this->requestedMethod) === 'put') {
            throw new MmpAPIClientException('Unsupported method', 405);
        } else {
            throw new MmpAPIClientException('Unsupported method', 405);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CAINFO, $this->sslCertPath);

        $result = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            // Check for errors and display the error message
            $error_message = curl_strerror($errno);
            curl_close($ch);
            throw new MmpAPIClientException("Request is invlaid" . "cURL error ({$errno}):\n {$error_message}", 500);
        }
        curl_close($ch);

        if (empty($result)) {
            throw new MmpAPIClientException("Response body from server is empty", 500);
        } else {
            if (is_string($result)) {
                $resp = json_decode($result, true);
                if (empty($resp)) {
                    throw new MmpAPIClientException("Response body is invalid (wrong format). Got the string: " . @serialize($result), 500);
                } else {
                    return $resp; //success
                }
            } else {
                throw new MmpAPIClientException("Response body is invalid (wrong format). Got the following: " . @serialize($result), 500);
            }
        }
    }

}
