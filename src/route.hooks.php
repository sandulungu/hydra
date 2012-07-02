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

// Register core routes
$hooks['app.routes'][-1000][] = function () {
    return array(
        
        // Setup core routes: assets delivery.
        array('GET', 'vendor/%vendor:vendor_web_dir/%path*/', function($request, $vendor, $path) {
            if (substr($path, -4) == '.php') {
                throw new Exception\InvalidActionParamException("'.php' extension should not be specified in a vendor asset filename.");
            }
            return new Response\FileResponse($request, file_exists("$vendor/$path.php") ? "$vendor/$path.php" : "$vendor/$path");
        }),
        array('GET', 'plugins/%plugin:plugin_web_dir/%path*/', function($request, $plugin, $path) {
            if (substr($path, -4) == '.php') {
                throw new Exception\InvalidActionParamException("'.php' extension should not be specified in a plugin asset filename.");
            }
            return new Response\FileResponse($request, file_exists("$vendor/$path.php") ? "$vendor/$path.php" : "$plugin/$path");
        }),
                
        // About page
        array('GET', 'hydra/about', function($request) {
            return new Response\FancyResponse($request, array('title' => "About Hydra"));
        }),

    );
};

// Validator for vendor web folder
$methods['app.normalize.vendor_web_dir'][0] = function(App $app, $name) {
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

// Validator for plugin web folder
$methods['app.normalize.plugin_web_dir'][0] = function(App $app, $name) {
    $dir = "{$app->core->app_dir}/plugins/$name/web";
    return is_dir($dir) ? $dir : null;
};

// Application method for quick route binding.
$methods['app.route'][0] = function(App $app, $http_method, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
    if (!in_array($http_method, array('GET', 'POST', 'PUT', 'DELETE'))) {
        throw new \DomainException("Http method should be one of: 'GET', 'POST', 'PUT', 'DELETE', but '$http_method' given in $name annotation.");
    }
    $app->routes[] = array($http_method, $pattern, $callback, $requirements, $defaults);
    return $app;
};

// Gets a list of registered routes.
$services['app.routes'][0] = function (App $app) {
    $routes = $app->infoHook('app.routes', $app);

    // No home route defined? Show an information page.
    $routes[] = array('GET', '/', function() {
        return 'Please define your routes in <strong>web/index.php</strong> or create a controller in <strong>app/src/App/Controller/</strong> folder.';
    });
    
    return $routes;
};
