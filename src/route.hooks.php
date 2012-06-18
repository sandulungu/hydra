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

$hooks['app.config'][-1000][] = function(&$config, $dummy, App $app) {
    $config['routes'] = array();
};

$methods['app.normalize.vendor_web_dir'][] = function(App $app, $name) {
    if ($name == 'hydra') {
        return "{$app->core->core_dir}/web";
    }
    if ($name == 'data') {
        return "{$app->core->data_dir}/web";
    }
    if (empty($app->config['vendor.web_dirs'][$name])) {
        return null;
    }
    return $app->config['vendor.web_dirs'][$name];
};

$methods['app.normalize.plugin_web_dir'][] = function(App $app, $name) {
    $dir = "{$app->core->app_plugins_dir}/$name/web";
    return is_dir($dir) ? $dir : null;
};

// Apply registered routes.
$hooks['request.route'][1000][] = function (Request $request, &$route) {
    $app = $request->app;
    
    // Setup core routes: assets delivery.
    $core = array(
        array('vendor/%vendor:vendor_web_dir/%path*/', function($vendor, $path) use ($request, $app) {
            return new Response\FileResponse($request, "$vendor/$path");
        }),
        array('plugins/%plugin:plugin_web_dir/%path*/', function($plugin, $path) use ($request, $app) {
            return new Response\FileResponse($request, "$plugin/$path");
        }),
    );
    
    // Load annotated routes
    $annotated = array_merge($app->config['routes'], $app->cache('request.route.annotated', function() use ($app) {
        $routes = array();
        foreach (Utils::listFilesRecursive($app->core->app_src_dir) as $file) {
            if (!preg_match('/Controller\.php$/', $file)) {
                continue;
            }
            
            $classname = str_replace('/', '\\', substr($file, strlen($app->core->app_src_dir), -4));
            $routes = array_merge($routes, $app->annotationsReader->forClass($classname));
        }
        return $routes;
    }));
    
    // No user routes defined? Show an information page.
    if (!$annotated && empty($app->routes)) {
        return new Action(function() {
            return 'Welcome! Please define your routes in <strong>web/index.php, app/config.php</strong> or <strong>app/hooks/route.hooks.php</strong>.';
        });
    }
    
    // Match routes.
    $routes = array_merge($core, (array)$app->routes, $annotated, $app->config->routes);
    foreach ($routes as $args) {
        $route = call_user_func_array('Hydra\Action::match', array(-1 => $request) + $args);
        if ($route) {
            return $route;
        }
    }
};

// @route annotation parser.
$methods['annotation.route'][0] = function(AnnotationsReader $reader, $annotation) {
    static $prefixes;
    if ($annotation['type'] != 'method') {
        $prefixes[$annotation['class']] = $annotation['value'];
        return;
    }
    
    $requirements = $defaults = array();
    @list($http_method, $pattern, $format, $json) = preg_split('/\s+/', $annotation['value'], 3);
    if ($json) {
        extract(json_decode($json, true));
    }
    
    if (!empty($prefixes[$annotation['class']])) {
        $pattern = rtrim($prefixes[$annotation['class']], '/') .'/'. ltrim($pattern, '/');
    }
    
    $name = $defaults['%controller'] = $annotation['class'];
    $defaults['action'] = substr($annotation['method'], 0 , -6);
    $name .= "::{$defaults['action']}()";
    
    if (!in_array($http_method, array('GET', 'POST', 'PUT', 'DELETE'))) {
        throw new \DomainException("Http method should be one of: 'GET', 'POST', 'PUT', 'DELETE', but '$http_method' given in $name annotation.");
    }
    $requirements['method'] = $http_method;
    
    if ($format) {
        $requirements['format'] = $format;
    }
    
    return array($pattern, null, $requirements, $defaults);
};

// Application methods for quick route binding.
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
