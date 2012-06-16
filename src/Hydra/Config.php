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
 * Wrapper class for configuration options.
 * 
 * Performs transparent saving on value set/unset. 
 * 
 * @property string $lang
 * @property array $mongodb
 * @property array $pdo
 * @property array $routes
 * @property array $twig
 */
class Config implements \ArrayAccess {
    
    protected $_owner, $_cachedData, $_data;

    function __construct($owner, &$cachedData) {
        $this->_owner = $owner;
        $this->_cachedData = $cachedData;
        $this->_data = array_merge(
            $this->_cachedData,
            $this->_owner->config__persist()
        );
    }
    
    function __isset($name) {
        return isset($this->_data[str_replace('__', '.', $name)]);
    }
    
    function __get($name) {
        return $this->_data[str_replace('__', '.', $name)];
    }
    
    function __set($name, $value) {
        return $this->offsetSet(str_replace('__', '.', $name), $value);
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
        $this->_data = array_merge(
            $this->_cachedData,
            $this->_owner->config__persist($offset, $value)
        );
    }

    function offsetUnset($offset) {
        $this->_data = array_merge(
            $this->_cachedData,
            $this->_owner->config__persist($offset, null, true)
        );
    }

}
