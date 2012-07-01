<?php

/**
 * This file is part of Hydra, the cozy RESTfull PHP5.3 micro-framework.
 *
 * @author      Sandu Lungu <sandu@lungu.info>
 * @package     hydra
 * @subpackage  core
 * @filesource
 * @license     http://www.opensource.org/licenses/MIT MIT
 */

namespace Hydra\Curl;

/**
 * OOP wrapper for curl_* functions
 *
 * Functional and OOP style mapping:
 *
 * curl_init($url);             |   $curl = new Curl($url);
 * curl_close($h);              |   unset($curl);
 * $e = curl_errno($h);         |   $e = $curl->errno();
 * $e = curl_error($h);         |   $e = $curl->error();
 * $i = curl_getinfo($h, $o);   |   $i = $curl->getInfo($o);
 * curl_setopt($opt, $val); ;   |   $curl->setOpt($opt, $val);
 * curl_setopt_array($array);   |   $curl->setOptArray($array); or $curl->setOpt($array)
 * curl_version($age)           |   Curl::version($age);
 * $h2 = curl_copy_handle($h);  |   $curl2 = clone($curl);
 * curl_exec($h);               |   $curl->exec();
 *
 * @author Alexey Karapetov <karapetov@gmail.com>
 */
class Curl {

    const DATA_TYPE_RAW = false;
    const DATA_TYPE_JSON = 'application/json';
    const DATA_TYPE_FORMDATA = 'multipart/form-data';

    /**
     * @var handler
     */
    private $__handle;
    public $headers = array();

    /**
     * @see curl_version()
     *
     * @param int $age
     * @return array
     */
    static function version($age = CURLVERSION_NOW) {
        return curl_version($age);
    }

    /**
     * @param string $url URL
     * @param string|false|null $certificate CA certificate filename
     */
    function __construct($url = null, $certificate = null) {
        $this->__handle = curl_init($url);
        if (isset($certificate)) {
            $this->setCAInfo($certificate);
        }
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @see curl_close()
     *
     * @return void
     */
    function __destruct() {
        curl_close($this->__handle);
    }

    /**
     * Copies handle using curl_copy_handle()
     *
     * @return void
     */
    function __clone() {
        $this->__handle = curl_copy_handle($this->__handle);
    }

    /**
     * @see curl_errno()
     *
     * @return int
     */
    function errno() {
        return curl_errno($this->__handle);
    }

    /**
     * @see curl_error
     *
     * @return string
     */
    function error() {
        return curl_error($this->__handle);
    }

    /**
     * @see curl_getinfo()
     *
     * @param int $opt CURLINFO_*
     * @return array|string
     */
    function getInfo($opt = 0) {
        return curl_getinfo($this->__handle, $opt);
    }

    /**
     * @see curl_setopt()
     *
     * @param int   $option Option code
     * @param mixed $value  Option value
     * @return boolean
     */
    function setOpt($option, $value) {
        return curl_setopt($this->__handle, $option, $value);
    }

    /**
     * @see curl_setopt_array()
     *
     * @param array $options Options
     * @return boolean
     */
    function setOptArray(array $options) {
        return curl_setopt_array($this->__handle, $options);
    }

    /**
     * Get curl handle
     *
     * @return resource
     */
    function getHandle() {
        return $this->__handle;
    }

    /**
     * Configures SSL certification chain
     *
     * @param string|false $certificate CA certificate filename
     */
    function setCAInfo($certificate) {
        if ($certificate) {
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
            $this->setOpt(CURLOPT_CAINFO, $certificate);
        } elseif ($certificate === false) {
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        }
    }
    
    /**
     * @see CURLOPT_USERPWD  
     */
    function setBasicAuth($username, $password = '') {
        $this->setOpt(CURLOPT_USERPWD, "$username:$password");
    }

    /**
     * Performs a POST request.
     * 
     * @param mixed $data Data to POST
     * @param string $data_type What content type to use for the data. 
     *   A few special content types will also auto-encode data.
     * @param mixed $charset
     * @return string Response
     */
    function post($data = null, $as_json = false) {
        if ($as_json) {
            $data = json_encode($data);
            $this->headers["Content-Type"] = "application/json;charset=utf-8";
        }
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->setOpt(CURLOPT_POST, true);
        return $this->exec();
    }

    /**
     * Performs a PUT request.
     * 
     * @param mixed $data Data to PUT
     * @param string $data_type What content type to use for the data. 
     *   A few special content types will also auto-encode data.
     * @param mixed $charset
     * @return string Response
     */
    function put($data = null, $as_json = false) {
        if ($as_json) {
            $data = json_encode($data);
            $this->headers["Content-Type"] = "application/json;charset=utf-8";
        }
        $this->setOpt(CURLOPT_POSTFIELDS, $data);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, "PUT");
        return $this->exec();
    }

    /**
     * Performs a DELETE request.
     * 
     * @param mixed $data Data to PUT
     * @param string $data_type What content type to use for the data. 
     *   A few special content types will also auto-encode data.
     * @param mixed $charset
     * @return string Response
     */
    function delete() {
        $this->setOpt(CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this->exec();
    }

    /**
     * Performs the request(s). By default it will be a GET request.
     * @see curl_exec()
     *
     * @param int $attempts Connection attempts (default is 1)
     * @param boolean $useException Throw \RuntimeException on failure
     * @return boolean|string
     */
    function exec($attempts = 1, $useException = true) {
        $attempts = (int) $attempts;
        if ($attempts < 1) {
            throw new \InvalidArgumentException("Attempts count ({$attempts}) is not positive.");
        }

        $headers = array();
        foreach ($this->headers as $name => $value) {
            if ($value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $headers[] = "$name: $v";
                    }
                } else {
                    $headers[] = "$name: $value";
                }
            }
        }
        $this->setOpt(CURLOPT_HTTPHEADER, $headers);
        
        $i = 0;
        while ($i++ < $attempts) {
            $result = curl_exec($this->__handle);
            if ($result !== false) {
                break;
            }
        }

        if ($useException && (false === $result)) {
            throw new \RuntimeException(
                ($attempts > 1 ? "{$this->error()} after {$attempts} attempt(s)." : $this->error()) 
                .' '. 
                var_export($this->getInfo(0), true)
            , $this->errno());
        }

        return $result;
    }

}
