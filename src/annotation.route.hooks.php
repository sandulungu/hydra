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

// Register annotated routes
$hooks['app.routes'][0][] = function (App $app) {
    $routes = $app->cache('annotation.routes', function() use ($app) {
        $routes = array();
        $app_src_dir = "{$app->core->app_dir}/src";
        foreach (Utils::listFilesRecursive($app_src_dir) as $file) {
            if (!preg_match('/Controller\.php$/', $file)) {
                continue;
            }
            $classname = str_replace('/', '\\', substr($file, strlen($app_src_dir), -4));
            
            $reflection = new \ReflectionClass($classname);
            if ($reflection->isAbstract()) continue;
            
            // Prepare prefix
            $app->annotationsReader->forClass($classname, array('route'));
            
            // Load routes
            $routes = array_merge(
                $routes, 
                $app->annotationsReader->forClassMethods($classname, array('route'))
            );
        }
        return $routes;
    });
    
    return $routes;
};
    
// @route annotation parser.
$methods['annotation.route'][0] = function(AnnotationsReader $reader, $annotation) {
    static $prefixes;
    if ($annotation['type'] != 'method') {
        return $prefixes[$annotation['class']] = trim($annotation['value'], '/');
    }
    
    // Extract the optional $http_method
    @list($http_method, $value) = preg_split('/\s+/', $annotation['value'], 2);
    if (strpos($http_method, '/') !== false || $http_method == '.') {
        $http_method = 'GET';
        $value = $annotation['value'];
    }
    
    @list($pattern, $format, $json) = preg_split('/\s+/', $value, 3);
    
    // Format is optional.
    if ($format && $format{0} == '{') {
        $format = null;
        list($pattern, $json) = preg_split('/\s+/', $value, 2);
    }
    
    $requirements = $defaults = array();
    if ($json) {
        extract(json_decode($json, true));
    }

    // Append Controller @route <prefix>.
    if (!empty($prefixes[$annotation['class']])) {
        $pattern = $pattern == '.' ? $prefixes[$annotation['class']] :
            $prefixes[$annotation['class']] .'/'. ltrim($pattern, '/');
    }
    
    $name = $defaults['controller.class'] = $annotation['class'];
    $name .= "::{$annotation['method']}()";
    if (substr($annotation['method'], -6) != 'Action') {
        throw new \LogicException("All routed controller actions should be suffixed with 'Action'. Please rename $name.");
    }
    $defaults['action'] = substr($annotation['method'], 0 , -6);
    
    if (!in_array($http_method, Action::$METHODS_ALLOWED)) {
        $methods = implode("', '", Action::$METHODS_ALLOWED);
        throw new \DomainException("Http method should be one of: '$methods', but '$http_method' given in $name annotation.");
    }
    
    if ($format) {
        $requirements['format'] = $format;
    }
    
    return array($http_method, $pattern, null, $requirements, $defaults);
};
