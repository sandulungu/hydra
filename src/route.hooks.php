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

// Load annotated routes
$hooks['app.config'][-1000][] = function(&$config, $dummy, App $app) {
    $config['routes'] = $app->cache('request.route.annotated', function() use ($app) {
        $routes = array();
        foreach (Utils::listFilesRecursive($app->core->app_src_dir) as $file) {
            if (!preg_match('/Controller\.php$/', $file)) {
                continue;
            }
            
            $classname = str_replace('/', '\\', substr($file, strlen($app->core->app_src_dir), -4));
            $routes = array_merge($routes, $app->annotationsReader->forClass($classname));
        }
        return $routes;
    });
};

$hooks['request.route'][1000][] = function (Request $request, &$route) {
    if (empty($request->app->routes) && empty($request->app->config->routes)) {
        return new Action(function() {
            return 'Welcome! Please define your routes in <strong>web/index.php, app/config.php</strong> or <strong>app/hooks/route.hooks.php</strong>.';
        });
    }
    foreach (array_merge($request->app->config->routes, $request->app->routes) as $args) {
        $route = call_user_func_array('Hydra\Action::match', array(-1 => $request) + $args);
        if ($route) {
            return $route;
        }
    }
};

$methods['annotation.route'][0] = function(AnnotationsReader $reader, $annotation) {
    $requirements = $defaults = array();
    @list($http_method, $pattern, $json) = preg_split('/\s+/', $annotation['value'], 3);
    if ($json) {
        extract(json_decode($json, true));
    }
    
    $name = $defaults['%controller'] = $annotation['class'];
    if ($annotation['type'] == 'method') {
        $defaults['action'] = substr($annotation['method'], 0 , -6);
        $name .= "::{$defaults['action']}()";
    }
    
    if (!in_array($http_method, array('GET', 'POST', 'PUT', 'DELETE'))) {
        throw new \DomainException("Http method should be one of: 'GET', 'POST', 'PUT', 'DELETE', but '$http_method' given in $name annotation.");
    }
    $requirements['method'] = $http_method;
    
    return array($pattern, null, $requirements, $defaults);
};

$methods['app.route.get'][0] = function(App $app, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {

    // Initialize routes array
    if (!isset($app->routes)) {
        $app->routes = array();
    }

    $app->routes[] = array($pattern, $callback, $requirements, $defaults);
    return $app;
};

$methods['app.route.post'][0] = function(App $app, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
    $requirements['method'] = 'POST';
    return $app->route__get($pattern, $callback, $requirements, $defaults);
};

$methods['app.route.put'][0] = function(App $app, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
    $requirements['method'] = 'PUT';
    return $app->route__get($pattern, $callback, $requirements, $defaults);
};

$methods['app.route.delete'][0] = function(App $app, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
    $requirements['method'] = 'DELETE';
    return $app->route__get($pattern, $callback, $requirements, $defaults);
};
