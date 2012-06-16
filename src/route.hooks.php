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

$hooks['app.config'][-1000][] = function(&$config) {
    $config['routes'] = array();
};

$hooks['request.route_action'][1000][] = function (Request $request, &$route) {
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
