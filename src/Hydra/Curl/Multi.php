<?php
/**
 * This file is part of Hydra, the cozy RESTfull PHP5.3 micro-framework.
 *
 * @link        https://github.com/z7/hydra
 * @author      Sandu Lungu <sandu@lungu.info>
 * @package     hydra
 * @subpackage  core
 * @filesource
 * @license     http://www.opensource.org/licenses/MIT MIT
 */

namespace Hydra\Curl;

/**
 * OOP wrapper for curl_multi_* fuctions
 *
 * Functional to OOP style mapping
 *
 * curl_multi_init();                           |   $cm = new Multi;
 * curl_multi_close($h);                        |   unset($cm);
 * $i = curl_multi_add_handle($mh, $ch);        |   $i = $cm->add($curl);
 * $i = curl_multi_remove_handle($mh, $ch);     |   $i = $cm->remove($curl);
 * $i = curl_multi_exec($mh, $running);         |   $i = $cm->exec($running);
 * $s = curl_multi_getcontent($ch);             |   $s = $cm->getContent($curl);
 * $a = curl_multi_info_read($mh, $msgs);       |   $a = $cm->infoRead($msgs)
 * $i = curl_multi_select($mh, $timeout);       |   $i = $cm->select($timeout);
 *
 * @author Alexey Karapetov <karapetov@gmail.com>
 */
class Multi
{
    /**
     * curl handle
     *
     * @var handler
     */
    private $__handle;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->__handle = curl_multi_init();
    }

    /**
     * @see curl_multi_close()
     *
     * @return void
     */
    public function __destruct()
    {
        curl_multi_close($this->__handle);
    }

    /**
     * @see curl_multi_add_handle()
     *
     * @param Curl $curl Добавляемый объект
     * @return int
     */
    public function add(Curl $curl)
    {
        return curl_multi_add_handle($this->__handle, $curl->getHandle());
    }

    /**
     * @see curl_multi_exec()
     *
     * @param int $stillRunning Flag
     * @return int (One of CURLM_* constants)
     */
    public function exec(&$stillRunning)
    {
        return curl_multi_exec($this->__handle, $stillRunning);
    }

    /**
     * @see curl_multi_getcontent()
     *
     * @return string
     */
    public function getContent(Curl $curl)
    {
        return curl_multi_getcontent($curl->getHandle());
    }

    /**
     * @see curl_multi_info_read()
     *
     * @param int $msgs
     * @return array
     */
    public function infoRead(&$msgs = null)
    {
        return curl_multi_info_read($this->__handle, $msgs);
    }

    /**
     * @see curl_multi_remove_handle()
     *
     * @param Curl $curl Handle to remove
     * @return int
     */
    public function remove(Curl $curl)
    {
        return curl_multi_remove_handle($this->__handle, $curl->getHandle());
    }

    /**
     * @see curl_multi_select()
     *
     * @param float $timeout Таймаут блокирования
     * @return int
     */
    public function select($timeout = 1.0)
    {
        return curl_multi_select($this->__handle, $timeout);
    }
}
