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
 * A super-simple container with support for both service objects and methods.
 * 
 * Inject services in your hook files:
 *    $services["someclass.service.name"][] = function($params, ...) { ... return $service_instance };
 * 
 * Service access syntax:
 *  - $someclass->service__name->...
 *  - $someclass['service.name']->...
 * 
 * Inject methods in your hook files:
 *    $methods["someclass.method.name"][] = function($params, ...) {...};
 * 
 * Method access syntax:
 *  - $someclass->method__name($params, ...)
 *  - $someclass['method:method.name']($params, ...)
 * 
 * Together with the hooking system, this forms Hydra's plugins architecture.
 */
class Container implements \ArrayAccess {
    
    private $__name, $__allowInvalidProperties = false;
    
    /**
     * Contains all defined factories (for all classes). 
     */
    static protected $_containerCallbacks = array('methods' => array(), 'services' => array());
    
    function __construct($name = null) {
        if (!$name) {
           $name = strtolower(str_replace('\\', '.', get_class($this)));
        }
        $this->__name = $name;
    }
    
    private function __getMethod($method, &$is_remote) {
        $offset = $this->__name .'.'. str_replace('__', '.', $method);
        if (isset(self::$_containerCallbacks['methods'][$offset])) {
            $callbacks =& self::$_containerCallbacks['methods'][$offset];
            ksort($callbacks);
            $callback = end($callbacks);
            if (!is_callable($callback)) {
                throw new Exception\ContainerException("Remote method '$method' is not a valid callback.");
            }
            $is_remote = true;
            return $callback;
        }
        
        // Try a local call
        elseif (method_exists($this, "fallback__$method")) {
            $is_remote = false;
            return array($this, "fallback__$method");
        }
        
        $remotes = implode(', ', array_keys(self::$_containerCallbacks['methods']));
        throw new Exception\ContainerException("Undefined method or factory: $offset.\nKnown remotes: $remotes.");
    }
    
    /**
     * Method injector.
     */
    function __call($method, $args)
    {
        $callback = $this->__getMethod($method, $is_remote);
        if ($is_remote) {
            array_unshift($args, $this);
        }
        return call_user_func_array($callback, $args);
    }

    /**
     * Service injector.
     */
    function &__get($name) {
        // Try a remote call
        $offset = $this->__name .'.'. str_replace('__', '.', $name);
        if (isset(self::$_containerCallbacks['services'][$offset])) {
            $factories =& self::$_containerCallbacks['services'][$offset];
            ksort($factories);
            $factory = end($factories);
            if (!is_callable($factory)) {
                throw new Exception\ContainerException("Service factory '$name' is not a valid callback.");
            }
            $this->$name =& $factory($this);
            return $this->$name;
        }
        
        // Try a local call
        elseif (method_exists($this, "service__$name")) {
            $this->$name =& $this->{"service__$name"}($this);
            return $this->$name;
        }
        
        if (!$this->__allowInvalidProperties) {
            $services = implode(', ', array_keys(self::$_containerCallbacks['services']));
            throw new Exception\ContainerException("Undefined property or factory: $offset.\nKnown services: $services.");
        }
        $this->$name = array();
        return $this->$name;
    }
    
    function offsetExists($offset) {
        $name = str_replace('.', '__', $offset);
        return property_exists($this, $name) || 
               isset(self::$_containerCallbacks['services'][$offset]) || 
               method_exists($this, "service__$name");
    }

    function offsetGet($offset) {
        if (strpos($offset, 'method:') === 0) {
            $self = $this;
            $callback = $this->__getMethod(substr($offset, 7), $is_remote);
            $return = function () use ($callback, $self, $offset, $is_remote) {
                $args = func_get_args();
                if ($is_remote) {
                    array_unshift($args, $self);
                }
                return call_user_func_array($callback, $args);
            };
            return $return;
        }
        $name = str_replace('.', '__', $offset);
        $this->$name;
        return $this->$name;
    }

    function offsetSet($offset, $value) {
        $name = str_replace('.', '__', $offset);
        $this->$name = $value;
    }

    function offsetUnset($offset) {
        $name = str_replace('.', '__', $offset);
        unset($this->$name);
    }

}
