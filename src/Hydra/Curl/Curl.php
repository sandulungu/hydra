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
class Curl
{
    
    const DATA_TYPE_RAW        = false;
    const DATA_TYPE_JSON       = 'application/json';
    const DATA_TYPE_URLENCODED = 'application/x-www-form-urlencoded';
    
    /**
     * Performs a POST request.
     * 
     * @param string $url Url to POST to
     * @param mixed $data Data to POST
     * @param string|false|null $certificate CA certificate filename
     * @param string $data_type What content type to use for the data. 
     *   A few special content types will also auto-encode data.
     * @param mixed $charset
     * @return string Response
     */
    static function post($url, $data, $certificate = null, $data_type = self::DATA_TYPE_JSON, $charset = 'utf-8') {
        $curl = new static($url, $certificate);
        $curl->setOpt(CURLOPT_POST, true);
        $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        
        if ($data_type == self::DATA_TYPE_JSON) {
            $data = json_encode($data);
        }
        elseif ($data_type == self::DATA_TYPE_URLENCODED && !is_string($data)) {
            $data = http_build_query($data);
        }
        $curl->setOpt(CURLOPT_POSTFIELDS, $data);
        
        if ($data_type) {
            $curl->setOpt(CURLOPT_HTTPHEADER, array("Content-Type: $data_type;charset=$charset"));
        }
        
        return $curl->exec();
    }
    
    /**
     * @var handler
     */
    private $__handle;

    /**
     * @param string $url URL
     * @param string|false|null $certificate CA certificate filename
     */
    public function __construct($url = null, $certificate = null)
    {
        $this->__handle = curl_init($url);
        if (isset($certificate)) {
            $this->setCAInfo($certificate);
        }
    }
    
    /**
     *
     * @param string|false $certificate CA certificate filename
     */
    public function setCAInfo($certificate) {
        if ($certificate) {
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, true);
            $this->setOpt(CURLOPT_SSL_VERIFYHOST, 2);
            $this->setOpt(CURLOPT_CAINFO, $certificate);
        } 
        elseif ($certificate === false) {
            $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        }
    }

    /**
     * Get curl handle
     *
     * @return resource
     */
    public function getHandle()
    {
        return $this->__handle;
    }

    /**
     * @see curl_close()
     *
     * @return void
     */
    public function __destruct()
    {
        curl_close($this->__handle);
    }

    /**
     * @see curl_errno()
     *
     * @return int
     */
    public function errno()
    {
        return curl_errno($this->__handle);
    }

    /**
     * @see curl_error
     *
     * @return string
     */
    public function error()
    {
        return curl_error($this->__handle);
    }

    /**
     * @see curl_exec()
     *
     * @param int $attempts Connection attempts (default is 1)
     * @param boolean $useException Throw \RuntimeException on failure
     * @return boolean|string
     */
    public function exec($attempts = 1, $useException = true)
    {
        $attempts = (int) $attempts;

        if ($attempts < 1)
        {
            throw new \InvalidArgumentException("Attempts count ({$attempts}) is not positive");
        }

        $i = 0;
        while ($i++ < $attempts)
        {
            $result = curl_exec($this->__handle);
            if ($result !== false)
            {
                break;
            }
        }

        if ($useException && (false === $result))
        {
            throw new \RuntimeException("{$this->error()} after {$attempts} attempt(s). " . var_export($this->getInfo(0), true), $this->errno());
        }

        return $result;
    }

    /**
     * @see curl_getinfo()
     *
     * @param int $opt CURLINFO_*
     * @return array|string
     */
    public function getInfo($opt = 0)
    {
        return curl_getinfo($this->__handle, $opt);
    }

    /**
     * @see curl_setopt()
     *
     * @param int   $option Option code
     * @param mixed $value  Option value
     * @return boolean
     */
    public function setOpt($option, $value)
    {
        return curl_setopt($this->__handle, $option, $value);
    }

    /**
     * @see curl_setopt_array()
     *
     * @param array $options Options
     * @return boolean
     */
    public function setOptArray(array $options)
    {
        return curl_setopt_array($this->__handle, $options);
    }

    /**
     * @see curl_version()
     *
     * @param int $age
     * @return array
     */
    public static function version($age = CURLVERSION_NOW)
    {
        return curl_version($age);
    }

    /**
     * Copies handle using curl_copy_handle()
     *
     * @return void
     */
    public function __clone()
    {
        $this->__handle = curl_copy_handle($this->__handle);
    }
}