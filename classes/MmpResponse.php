<?php

/**
 * @package MmproApiV1Package
 * @author Sergio Gamanyuk <bearwebua@gmail.com> https://github.com/bearwebua/
 * @link https://github.com/lisamatch/api-php-client
 * @copyright 2003-2014 Matchmaking Pro, Inc
 * @version 1.0
 */
class MmpResponse
{

    /**
     * @var boolean
     */
    public $status = false;

    /**
     * @var mixed - response data
     */
    public $data = [];

    /**
     * @var int - http code of request
     */
    public $httpCode = 500;

    /**
     * @var string - message if any
     */
    public $message = '';

    /**
     * @var mixed - additional information if any (warnings for example)
     */
    public $info = [];

    /**
     * @var array  - original array of response
     */
    public $originResponse = array();

    /**
     * Prepares response data
     * @param array $data - data from API response
     * @return MmpResponse
     */
    public function reply(array $data)
    {

        $this->originResponse = $data; // in case we got something weird. For debugging reasons

        if (isset($data['status'])) {
            $this->status = (bool) $data['status'];
        }
        if (isset($data['data'])) {
            $this->data = $data['data'];
        }
        if (isset($data['httpCode'])) {
            $this->httpCode = (int) $data['httpCode'];
        }
        if (isset($data['message'])) {
            $this->message = $data['message'];
        }
        if (isset($data['info'])) {
            $this->info = $data['info'];
        }

        return $this;
    }

}
