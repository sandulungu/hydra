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
class ArrayObject implements \ArrayAccess, \IteratorAggregate, \Countable {
    
    protected $_data;

    function __construct(array &$data = array()) {
        $this->_data =& $data;
    }
    
    function getIterator () {
        return new \ArrayIterator(&$this->_data); // this line will trigger a call_time_pass_reference notice
    }
    
    function count() {
        return count($this->_data);
    }

    /**
     * Get a reference to the inner array object.
     * 
     * This function may be useful for passing by reference to functions
     * expecting an array parameter. Example: sorting functions.
     * 
     * @return array
     */
    function &getArray() {
        return $this->_data;
    }
    
    /**
     * @see \ArrayObject
     */
    function getArrayCopy() {
        return $this->_data;
    }
    
    /**
     * @see \ArrayObject
     */
    function append($value) {
        $this->_data[] = $value;
    }
    
    /**
     * Getter.
     * 
     * A time-saver function that may be used instead of a ternary construction
     * for options that may not be set.
     */
    function get($name, $default = null) {
        return $this->__isset($name) ? $this->__get($name) : $default;
    }
    
    /**
     * Setter.
     * 
     * Useful in Twig templates, where one cannot set properties for an object directly.
     */
    function set($name, $value) {
        $this->__set($name, $value);
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
