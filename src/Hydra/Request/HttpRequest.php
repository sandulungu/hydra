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

namespace Hydra\Request;

use Hydra\App;
use Hydra\Request;

/**
 * HTTP Request holder.
 * 
 * TODO: Reverse proxy and http auth support.
 * 
 * @property string $webroot
 * @property string $webhost
 * @property string $baseurl
 * @property bool   $https
 */
class HttpRequest extends Request {
    
    var $server;
    
    function __construct(App $app, $path, $method = 'GET', array $query = array(), $data = null, array $server = array()) {
        $this->server = $server ? $server : $_SERVER;
        parent::__construct($app, $path, $method, $query, $data);
    }
    
    /**
     * @return bool True if the request is over SSL.
     */
    function service__https() {
        return !empty($this->server['HTTPS']) && $this->server['HTTPS'] != 'off';
    }
    
    /**
     * @return string Remote IP address.
     */
    function service__clientIp() {
        return $this->server['SERVER_ADDR'];
    }

    /**
     * Get baseurl and webroot.
     */
    function service__baseurl() {
        $url_rewritten = isset($_SERVER['REDIRECT_URL']);
        $script = $_SERVER['SCRIPT_NAME'];
        $uri = $_SERVER['REQUEST_URI'];
        if ($url_rewritten) {
            $uri_lowercase = strtolower($uri);
            for($i = 0; $i < strlen($uri) && $i < strlen($script) && $uri_lowercase{$i} == $script{$i}; $i++) {}
            $this->webroot = $baseurl = rtrim(substr($uri, 0, $i - 1), '/');
        } else {
            $baseurl = substr($uri, 0, strlen($script));
            $this->webroot = dirname($baseurl);
        }
        return $baseurl;
    }
    
    function service__webroot() {
        $this->webroot = null; // prevent recursion
        $this->baseurl;
        return $this->webroot;
    }

    /**
     * @return string Host URL, ex: https://example.com:8888 
     */
    function service__webhost() {
        $proto = $this->https ? 'https' : 'http';
        $port = $this->server['SERVER_PORT'];
        $port = $port == 80 && !$this->https || $port == 443 && $this->https ? '' : ":$port";
        return "$proto://{$this->server['SERVER_NAME']}$port";
    }

}