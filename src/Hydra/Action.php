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
abstract class Action {
    
    var $name, $pattern, $params = array();
    
    static $METHODS_ALLOWED = array('GET', 'POST', 'PUT', 'DELETE');

    /**
     * Tries to match the $request with a $pattern.
     * 
     * @return bool|Action On success return an Action instance.
     */
    static function match(Request $request, $http_method, $pattern, \Closure $callback = null, $requirements = array(), array $defaults = array()) {
        if (is_string($requirements)) {
            $requirements = array('format' => $requirements);
        }
        $requirements += array('format' => 'html');
        if (($request->method == 'HEAD' ? 'GET' : $request->method) != $http_method) {
            return false;
        }
                
        $types = array('format' => 'safeString');
        $format_preg = substr($pattern, -1) == '/' ? '/?' : "(?:\.(?P<format>{$requirements['format']}))?";
        
        $preg = preg_replace_callback('/[%$]([A-Za-z0-9_]+)(?::([A-Za-z0-9_]+))?(\*)?/', function($matches) use ($requirements, &$types) {
            $name = $matches[1];
            $types[$name] = empty($matches[2]) ? 'safeString' : $matches[2];
            $chars = empty($matches[3]) ? '[^/]' : '.';
            return empty($requirements[$name]) ? "(?P<$name>$chars+?)" : "(?P<$name>{$requirements[$name]})";
        }, strtr(trim($pattern, '/'), array('.' => '\.')));

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
        
//        list($default_format) = explode('|', $requirements['format']);
        $params += $defaults;
        if (!isset($params['format'])) {
            if (!preg_match("`^{$requirements['format']}$`", 'html')) {
                return false;
            }
            $params['format'] = 'html';
        }
        
        $name = Utils::sluggify($pattern, "/(:[a-z0-9_]+|[^a-z0-9$%])+/", 'strtolower', 'homepage');
        $name = str_replace('%', '$', $name) .'.'. strtolower($http_method);
        
        return $callback ? 
            new Action\CallbackAction($callback, $params, $name, $pattern) :
            new Action\ControllerAction($params, $name, $pattern);
    }
    
    function __construct(array $params = array(), $name = 'default', $pattern = null) {
        $this->params = $params + array('format' => 'html');
        $this->name = $name;
        $this->pattern = $pattern;
    }
    
    /**
     * Executes the associated controller/action or callback.
     */
    abstract function execute(Request $request);
    
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
