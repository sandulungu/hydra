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

namespace Hydra;

/**
 * Cookies provider.
 * 
 * Performs transparent saving on value set/unset. 
 * 
 * @property string $hydra__token
 * @property string $hydra
 */
class Cookie extends BaseConfig {
    
    /**
     * @var App 
     */
    protected $_app;
    
    protected $_data;

    function __construct(App $app) {
        $this->_app = $app;
        $this->_data =& $_COOKIE;
    }

    function set($name, $value, $expiresIn = null, $path = null, $domain = null, $secure = null, $httponly = null) {
        $name = str_replace('.', '__', $name);
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
        return isset($this->_data[str_replace('.', '__', $name)]);
    }
    
    function &__get($name) {
        return $this->_data[str_replace('.', '__', $name)];
    }
    
    function __set($name, $value) {
        $this->set($name, $value);
    }
    
    function __unset($name) {
        $name = str_replace('.', '__', $name);
        $this->set($name, '');
        unset($this->_data[$name]);
    }

}
