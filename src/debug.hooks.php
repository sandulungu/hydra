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

$start = microtime(true);

$hooks['app.routes'][0][] = function(App $app) {
    if ($app->core->debug) {
        return array(
            array('hydra/debug/config', function(Request $request) {
                return new Response\DataResponse($request, $request->app->config->all(), "Debug info » Configuration");
            }),
            array('hydra/debug/routes', function(Request $request) {
                return new Response\DataResponse($request, $request->app->routes, "Debug info » Routes");
            }),
            array('hydra/debug/env', function(Request $request) {
                return new Response\DataResponse($request, $_ENV, "Debug info » Environment");
            }),
            array('hydra/debug/server', function(Request $request) {
                return new Response\DataResponse($request, $_SERVER, "Debug info » Server options");
            }),
            array('hydra/debug/cookie', function(Request $request) {
                return new Response\DataResponse($request, $_COOKIE, "Debug info » Cookies");
            }),
            array('hydra/debug/session', function(Request $request) {
                $request->app->session; // Start session
                return new Response\DataResponse($request, $_SESSION, "Debug info » Session");
            }),
            array('hydra/debug/phpinfo', function(Request $request) {
                return function() { phpinfo(); };
            }),
        );
    }
};

// Set default configuration options.
$hooks['response.output'][1000][] = function (&$config, &$dummy, App $app) use ($start) {
    if ($app->core->debug) {
        $app->monolog__debug->info("Generation time: " . (microtime(true) - $start));
        $app->monolog__debug->info("Total time: " . (microtime(true) - $_SERVER['REQUEST_TIME']));
    }
};
