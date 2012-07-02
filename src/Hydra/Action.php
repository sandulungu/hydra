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
 * Class is used for default routing and action dispatching.
 */
class Action {
    
    protected $_callback;
    var $name, $pattern, $params = array(); 

    /**
     * Tries to match the $request with a $pattern.
     * 
     * @return bool|Action On success return an Action instance.
     */
    static function match(Request $request, $pattern, \Closure $callback = null, array $requirements = array(), array $defaults = array()) {
        $requirements += array('format' => 'html', 'method' => 'GET');
        if (($request->method == 'HEAD' ? 'GET' : $request->method) != $requirements['method']) {
            return false;
        }
                
        $types = array('format' => 'string');
        $format_preg = substr($pattern, -1) == '/' ? '' : "(?:\.(?P<format>{$requirements['format']}))?";
        
        $preg = preg_replace_callback('/[%$]([a-z0-9_]+)(?::([a-z0-9_]+))?(\*)?/', function($matches) use ($requirements, &$types) {
            $name = $matches[1];
            $types[$name] = empty($matches[2]) ? 'safe_string' : $matches[2];
            $chars = empty($matches[3]) ? '[^/]' : '.';
            return empty($requirements[$name]) ? "(?P<$name>$chars+?)" : "(?P<$name>{$requirements[$name]})";
        }, strtr(trim($pattern, '/'), array('.', '\.')));
        
        if (!preg_match("`^$preg$format_preg$`", $request->path, $matches)) {
            return false;
        }

        $params = array();
        foreach ($matches as $name => $match) {
            if (is_int($name)) {
                continue;
            }
            $method = "normalize__{$types[$name]}";
            $params[$name] = $types[$name] ? $request->app->$method($match) : $match;
            if ($params[$name] === null) {
                throw new Exception\InvalidActionParamException("Route matched, but invalid value for '$name' param detected: $match.");
            }
        }
        list($default_format) = explode('|', $requirements['format']);
        $params += $defaults + array('format' => $default_format);
        
        $name = strtolower($pattern ?: 'homepage');
        $name = preg_replace('/:[a-z0-9_]+/', '', $name);
        $name = preg_replace('/[^a-z0-9$]+/', '_', str_replace('%', '$', $name)) .'.'. strtolower($requirements['method']);
        return new static($callback, $params, $name, $pattern);
    }
    
    function __construct(\Closure $callback = null, array $params = array('format' => 'html'), $name = 'default', $pattern = null) {
        $this->_callback = $callback;
        $this->params = $params;
        $this->name = $name;
        $this->pattern = $pattern;
    }
    
    /**
     * Executes the associated controller/action or callback.
     */
    function execute(Request $request) {
        
        // Use controller and action
        if (!$this->_callback) {
            $this->params += array(
                '%controller'  => 'App\Controller\%sController', 
                'controller'    => 'Default', 
                'action'        => '__invoke'
            );
            $controller_class = sprintf($this->params['%controller'], ucfirst(preg_replace('/[^a-z0-9]+/i', '', $this->params['controller'])));
            if (!class_exists($controller_class)) {
                throw new Exception\InvalidControllerException("Controller not found: $controller_class.");
            }
            $controller = new $controller_class($request);
            
            $action_method = preg_replace('/[^a-z0-9]+/i', '', $this->params['action']) . 'Action';
            if (!method_exists($controller, $action_method)) {
                throw new Exception\InvalidControllerActionException("Action '$action_method' not not defined in controller: $controller_class.");
            }
            return $this->_invokeAction($request, $controller, $action_method);
        }
        
        return $this->_invokeAction($request, $this->_callback);
    }
    
    /**
     * Inteligent invoker, mapping request parameters to method ones, using reflection.
     */
    protected function _invokeAction(Request $request, $class, $method = '__invoke') {
        $reflection = new \ReflectionMethod($class, $method);
        $args = array();
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            if ($name == 'request') {
                $args[] = $request;
            } else {
                if (!isset($this->params[$name])) {
                    if ($parameter->isOptional()) {
                        continue;
                    }
                    $classname = get_class($class);
                    $call = $class instanceof \Closure ? 'closure' : "$classname::$method()";
                    throw new \LogicException("Parameter '$name' in $call call has no corresponding param in route pattern '$this->pattern'.");
                }
                $args[] = $this->params[$name];
            }
        }
        return call_user_func_array(array($class, $method), $args);
    }
    
    function __toString() {
        return $this->name;
    }
    
}
