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

// Register core routes
$hooks['app.routes'][-1000][] = function () {
    return array(
        
        // Setup core routes: assets delivery.
        array('vendor/%vendor:vendor_web_dir/%path*/', function($request, $vendor, $path) {
            return new Response\FileResponse($request, "$vendor/$path");
        }),
        array('plugins/%plugin:plugin_web_dir/%path*/', function($request, $plugin, $path) {
            return new Response\FileResponse($request, "$plugin/$path");
        }),
                
        // About pages
        array('hydra/about', function($request) {
            return new Response\FancyResponse($request, array('title' => "About Hydra"));
        }),     
    );
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

// Application methods for quick route binding.
$methods['app.route.get'][] = function(App $app, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
    $app->routes__defined = true;
    $app->routes[] = array($pattern, $callback, $requirements, $defaults);
    return $app;
};

$methods['app.route.post'][] = function(App $app, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
    $requirements['method'] = 'POST';
    return $app->route__get($pattern, $callback, $requirements, $defaults);
};

$methods['app.route.put'][] = function(App $app, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
    $requirements['method'] = 'PUT';
    return $app->route__get($pattern, $callback, $requirements, $defaults);
};

$methods['app.route.delete'][] = function(App $app, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
    $requirements['method'] = 'DELETE';
    return $app->route__get($pattern, $callback, $requirements, $defaults);
};

// Gets a list of registered routes.
$services['app.routes'][] = function (App $app) {
    return $app->infoHook('app.routes', $app);
};