<?php

/**
 * MmpAPIClient class. Wrapper for MmpAPIProcessor
 *
 * Requirements:
 *
 * PHP 5.3+
 * cURL
 *
 * @package MmproApiV1Package
 * @author Sergio Gamanyuk <bearwebua@gmail.com> https://github.com/bearwebua/
 * @link https://github.com/lisamatch/api-php-client
 * @copyright 2003-2014 Matchmaking Pro, Inc
 * @version 1.0
 */
class MmpAPIClient
{

    /**
     * @var MmpAPIProcessor
     */
    protected $processor;

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
    }

    /**
     * Fires request to API
     * For each call of this method creates new object
     * @param string $method - http method. For example: "GET", "POST", "DELETE", "PUT"
     * @param string $res - resource identificator. For example "member/100"
     * @param array $data - array of extra data
     * @return MmpResponse - structure with response data
     */
    public function call($method, $res, $data = array())
    {
        $this->processor = new MmpAPIProcessor($this->publicKey, $this->privateKey, $this->sslCertPath);
        return $this->processor->call($method, $res, $data); //delegate the call to processor
    }

    /**
     * Getter
     * @return MmpAPIProcessor
     */
    public function getProcessor()
    {
        return $this->processor;
    }

}
