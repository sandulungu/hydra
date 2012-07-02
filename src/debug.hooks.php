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

$start = microtime(true);

$hooks['app.routes'][0][] = function(App $app) {
    if ($app->core->debug) {
        return array(
            array('GET', 'hydra/debug/config', function(Request $request) {
                return new Response\DataResponse($request, $request->app->config->all(), "Debug info » Configuration");
            }),
            array('GET', 'hydra/debug/routes', function(Request $request) {
                return new Response\DataResponse($request, $request->app->routes, "Debug info » Routes");
            }),
            array('GET', 'hydra/debug/env', function(Request $request) {
                return new Response\DataResponse($request, $_ENV, "Debug info » Environment");
            }),
            array('GET', 'hydra/debug/server', function(Request $request) {
                return new Response\DataResponse($request, $_SERVER, "Debug info » Server options");
            }),
            array('GET', 'hydra/debug/cookie', function(Request $request) {
                return new Response\DataResponse($request, $_COOKIE, "Debug info » Cookies");
            }),
            array('GET', 'hydra/debug/session', function(Request $request) {
                $request->app->session; // Start session
                return new Response\DataResponse($request, $_SESSION, "Debug info » Session");
            }),
            array('GET', 'hydra/debug/phpinfo', function(Request $request) {
                return function() { phpinfo(); };
            }),
            array('GET', 'hydra/debug/batch', function(Request $request) {
                $request->app->session['batch']['test'] = array(1, 2, 3, 4);
                return new \Hydra\Response\RedirectResponse($request, 'hydra/batch/test');
            }),
        );
    }
};

// Set default configuration options.
$hooks['response.send'][1000][] = function (&$config, &$dummy, App $app) use ($start) {
    if ($app->core->debug) {
        $app->monolog__debug->info("Generation time: " . (microtime(true) - $start));
        $app->monolog__debug->info("Total time: " . (microtime(true) - $_SERVER['REQUEST_TIME']));
    }
};

// Test batch operation
$methods['request.batch.test.info'][0] = function() {
    return array('redirect_uri' => '');
};
$methods['request.batch.test.prepare'][0] = function(\Hydra\Request $request, $data) {
    $request->app->session["batch.test.progress"] = 0;
    return "Processing step $data of 4";
};
$methods['request.batch.test.process'][0] = function(\Hydra\Request $request, $data) {
    if ($data == 1) {
        // Output HTML
        usleep(1500000);
        echo "This is some HTML <b>visible</b> to the user.";
    }
    elseif ($data == 2) {
        // Partial task.
        usleep(200000);
        $p =& $request->app->session["batch.test.progress"];
        $p++;
        return $p < 10 ? false : true;
    }
    elseif ($data == 3) {
        // Partial task with progress indicator.
        $p =& $request->app->session["batch.test.progress"];
        $p += 2;
        return $p < 100 ? "$p%" : true;
    } 
    else {
        // Error handling test.
        throw new \RuntimeException('Some error thrown.');
    }
};
