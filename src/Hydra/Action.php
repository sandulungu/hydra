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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class is used for default routing and action dispatching.
 */
class Action {
    
    protected $_callback;
    var $params = array(); 

    /**
     * Tries to match the $request with a $pattern.
     * 
     * @return bool|Action On success return an Action instance.
     */
    static function match(Request $request, $pattern, \Closure $callback = null, $requirements = array(), $defaults = array()) {
        $requirements += array('format' => 'html', 'method' => 'GET|HEAD');
        if (!empty($requirements['method'])) {
            if (!preg_match("/^{$requirements['method']}$/", $request->method)) {
                return false;
            }
        }
                
        $types = array('format' => 'string');
        $preg = preg_replace_callback('/%([a-z0-9_]+)(?::([a-z0-9_]+))?/', function($matches) use ($requirements, &$types) {
            $name = $matches[1];
            $types[$name] = empty($matches[2]) ? 'string' : $matches[2];
            return empty($requirements[$name]) ? "(?P<$name>[^./]+?)" : "(?P<$name>{$requirements[$name]})";
        }, strtr($pattern, array('.', '\.')));
        if (!preg_match("`^$preg(?:\.(?P<format>{$requirements['format']}))?$`", $request->path, $matches)) {
            return false;
        }

        $params = array();
        foreach ($matches as $name => $match) {
            if (is_int($name)) {
                continue;
            }
            $params[$name] = $types[$name] ? $request->app["method:normalize.{$types[$name]}"]($match) : $match;
            if ($params[$name] === null) {
                throw new \RuntimeException("Route matched, but invalid value for $name param detected: $match");
            }
        }
        $params += $defaults + array('format' => 'html');
        if (!$callback) {
            $params += $defaults + array('class_prefix' => 'App\Controller\\', 'controller' => 'Default', 'action' => 'execute');
        }
        
        $name = (empty($requirements['method']) ? 'all' : $requirements['method']) . "_$pattern";
        $name = preg_replace('/[^a-z0-9]+/', '_', strtolower($name));
        return new static($callback, $params, $name);
    }
    
    function __construct(\Closure $callback = null, $params = array('format' => 'html'), $name = 'default') {
        $this->_callback = $callback;
        $this->params = $params;
        $this->name = $name;
    }
    
    /**
     * Executes the associated controller/action or callback.
     */
    function execute(Request $request) {
        
        // Use controller and action
        if (!$this->_callback) {
            $controller_class = $this->params['class_prefix'] . preg_replace('/[^a-z0-9]+/i', '', $this->params['controller']) . 'Controller';
            if (!class_exists($controller_class)) {
                throw new NotFoundHttpException("Controller not found: $controller_class.");
            }
            $controller = new $controller_class($request);
            
            $action = preg_replace('/[^a-z0-9]+/i', '', $this->params['action']) . 'Action';
            if (!method_exists($controller, $action)) {
                throw new NotFoundHttpException("Action '$action' not not defined in controller: $controller_class.");
            }
            return call_user_func_array(
                array($controller, $action), 
                array_diff_key($this->params, array('class_prefix' => true, 'controller' => true, 'action' => true))
            );
        }
        
        return call_user_func($this->_callback, $request);
    }
    
    function __toString() {
        return $this->name;
    }
    
}
