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
class Cookie implements \ArrayAccess {
    
    /**
     * @var App 
     */
    protected $_app;
    
    protected $_data;

    function __construct(App $app) {
        $this->_app = $app;
        $this->_data =& $_COOKIE;
    }

    function set($offset, $value, $expiresIn = null, $path = null, $domain = null, $secure = null, $httponly = null) {
        $name = str_replace('.', '__', $offset);
        $this->_data[$name] = $value;
        setcookie(
            $name, $value, 
            time() + isset($expiresIn)  ? $expiresIn  : $this->_app->config['cookie']['expiresIn'], 
                     isset($path)       ? $path       : $this->_app->config['cookie']['path'], 
                     isset($domain)     ? $domain     : $this->_app->config['cookie']['domain'], 
                     isset($secure)     ? $secure     : $this->_app->config['cookie']['secure'], 
                     isset($httponly)   ? $httponly   : $this->_app->config['cookie']['httponly']
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
        $name = str_replace('.', '__', $offset);
        return array_key_exists($name, $this->_data);
    }

    function offsetGet($offset) {
        $name = str_replace('.', '__', $offset);
        return $this->_data[$name];
    }

    function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    function offsetUnset($offset) {
        $this->set($offset, '');
        $name = str_replace('.', '__', $offset);
        unset($this->_data[$name]);
    }

}
