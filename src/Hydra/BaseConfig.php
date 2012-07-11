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
 * Base configuration class.
 */
class BaseConfig implements \ArrayAccess {
    
    protected $_data;

    function __construct(array &$data = array()) {
        $this->_data =& $data;
    }

    function all() {
        return $this->_data;
    }
    
    function get($name, $default = null) {
        return $this->__isset($name) ? $this->__get($name) : $default;
    }
    
    function __isset($name) {
        return isset($this->_data[str_replace('__', '.', $name)]);
    }
    
    function &__get($name) {
        return $this->_data[str_replace('__', '.', $name)];
    }
    
    function __set($name, $value) {
        $this->_data[str_replace('__', '.', $name)] = $value;
    }
    
    function __unset($name) {
        unset($this->_data[str_replace('__', '.', $name)]);
    }
    
    function offsetExists($offset) {
        return $this->__isset($offset);
    }

    function offsetGet($offset) {
        return $this->__get($offset);
    }

    function offsetSet($offset, $value) {
        $this->__set($offset, $value);
    }

    function offsetUnset($offset) {
        $this->__unset($offset);
    }

}
