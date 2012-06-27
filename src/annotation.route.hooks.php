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

// Register annotated routes
$hooks['app.routes'][0][] = function (App $app) {
    $routes = $app->cache('annotation.routes', function() use ($app) {
        $routes = array();
        foreach (Utils::listFilesRecursive($app->core->app_src_dir) as $file) {
            if (!preg_match('/Controller\.php$/', $file)) {
                continue;
            }
            
            $classname = str_replace('/', '\\', substr($file, strlen($app->core->app_src_dir), -4));
            $routes = array_merge($routes, $app->annotationsReader->forClass($classname, array('route')));
        }
        return $routes;
    });
    
    $app->routes__defined = $app->routes__defined || $routes;
    return $routes;
};
    
// @route annotation parser.
$methods['annotation.route'][] = function(AnnotationsReader $reader, $annotation) {
    static $prefixes;
    if ($annotation['type'] != 'method') {
        $prefixes[$annotation['class']] = $annotation['value'];
        return;
    }
    
    $requirements = $defaults = array();
    @list($http_method, $pattern, $format, $json) = preg_split('/\s+/', $annotation['value'], 4);
    if ($json) {
        extract(json_decode($json, true));
    }
    
    if (!empty($prefixes[$annotation['class']])) {
        $pattern = rtrim($prefixes[$annotation['class']], '/') .'/'. ltrim($pattern, '/');
    }
    
    $name = $defaults['%controller'] = $annotation['class'];
    $name .= "::{$annotation['method']}()";
    if (substr($annotation['method'], -6) != 'Action') {
        throw new \LogicException("All routed controller actions should be prefixed with 'Action'. Please rename $name.");
    }
    $defaults['action'] = substr($annotation['method'], 0 , -6);
    
    if (!in_array($http_method, array('GET', 'POST', 'PUT', 'DELETE'))) {
        throw new \DomainException("Http method should be one of: 'GET', 'POST', 'PUT', 'DELETE', but '$http_method' given in $name annotation.");
    }
    $requirements['method'] = $http_method;
    
    if ($format) {
        $requirements['format'] = $format;
    }
    
    return array($pattern, null, $requirements, $defaults);
};
