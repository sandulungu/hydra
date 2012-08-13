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
        $prefixes[$annotation['class']] = $annotation['value'];
        return;
    }
    
    $requirements = $defaults = array();
    @list($http_method, $pattern, $format, $json) = preg_split('/\s+/', $annotation['value'], 4);
    
    // Format is optional.
    if ($format && $format{0} == '{') {
        $format = null;
        list($http_method, $pattern, $json) = preg_split('/\s+/', $annotation['value'], 3);
    }
    
    if ($json) {
        extract(json_decode($json, true));
    }
    
    // Append Controller @route <prefix>.
    if (!empty($prefixes[$annotation['class']])) {
        $pattern = rtrim($prefixes[$annotation['class']], '/') .'/'. ltrim($pattern, '/');
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
