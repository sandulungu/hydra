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

namespace Hydra;

/**
 * Cookies provider.
 * 
 * Performs transparent saving on value set/unset. 
 * 
 * @property string $lang
 * @property array $mongodb
 * @property array $pdo
 * @property array $routes
 * @property array $twig
 */
class Cookies implements \ArrayAccess {
    
    /**
     * @var App 
     */
    protected $_app;
    
    protected $_data;

    function __construct($app) {
        $this->_app = $app;
        $this->_data =& $_COOKIE;
    }

    function set($name, $value, $expires_in = null, $path = null, $domain = null, $secure = null, $httponly = null) {
        $this->_data[$name] = $value;
        setcookie(
            $name, $value, 
            time() + isset($expires_in) ? $expires_in : $this->_app->config['cookies.expires_in'], 
                     isset($path)       ? $path       : $this->_app->config['cookies.path'], 
                     isset($domain)     ? $domain     : $this->_app->config['cookies.domain'], 
                     isset($secure)     ? $secure     : $this->_app->config['cookies.secure'], 
                     isset($httponly)   ? $httponly   : $this->_app->config['cookies.httponly']
        );
    }
    
    function __isset($name) {
        return isset($this->_data[str_replace('__', '.', $name)]);
    }
    
    function __get($name) {
        return $this->_data[str_replace('__', '.', $name)];
    }
    
    function __set($name, $value) {
        return $this->set(str_replace('__', '.', $name), $value);
    }
    
    function __unset($name) {
        return $this->offsetUnset(str_replace('__', '.', $name));
    }
    
    function offsetExists($offset) {
        return array_key_exists($offset, $this->_data);
    }

    function offsetGet($offset) {
        return $this->_data[$offset];
    }

    function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    function offsetUnset($offset) {
        $this->set($offset, '');
        unset($this->_data[$offset]);
    }

}
