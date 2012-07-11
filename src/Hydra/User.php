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
 * User provider.
 * 
 * For performace and ease-of-use considerations, this object is serialized in 
 * Session and restored in subsequent requests. 
 * 
 * Note however, that only the properties starting with "s__" 
 * (for instance intialized services with the "s." prefix) will be serialized.
 */
class User extends Container implements \Serializable {
    
    /**
     * @var App
     */
    var $app;
    
    function __construct(App $app) {
        $this->app = $app;
    }
    
    function serialize () {
        $properties = get_object_vars($object);
        foreach ($properties as $name => $value) {
            if (substr($name{0}, 0, 3) !== 's__') {
                unset($properties[$name]);
            }
        }
        return serialize($properties);
    }
    
    function unserialize ($serialized) {
        parent::__construct();
        foreach (unserialize($serialized) as $name => $value) {
            $this->$name = $value;
        }
    }
    
}
