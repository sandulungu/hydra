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

if ($app->core->debug) {
    $start = microtime(true);

    $hooks['app.routes'][0][] = function() {
        return array(
            array('GET', 'hydra/debug/config', function(Request $request) {
                return new Response\DataResponse($request, $request->app->config->getArrayCopy(), "Debug info » Configuration");
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
    };

    // Set default configuration options.
    $hooks['response.send'][1000][] = function () use ($start, $app) {
        $app->monolog__debug->info("Generation time: " . (microtime(true) - $start));
        $app->monolog__debug->info("Total time: " . (microtime(true) - $_SERVER['REQUEST_TIME']));
    };

    // Test batch operation
    $methods['request.batch.test.info'][0] = function() {
        return array('redirect_uri' => '');
    };
    $methods['request.batch.test.prepare'][0] = function(Request $request, $data) {
        $request->app->session["batch.test.progress"] = 0;
        return "Processing step $data of 4";
    };
    $methods['request.batch.test.process'][0] = function(Request $request, $data) {
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
}

// Better exception output for JSON, JS and other non-HTML requests
$hooks['app.exception'][1000][] = function(\Exception $ex) use ($app) {
    $request = reset($app->requests);
    $format = null;
    $debug = $app->core->debug;
    
    // Get format from path info
    if ($request) {
        $format = Utils::fileExt($request->path);
    }
    
    // Get format from active route
    if ($request && isset($request->params)) {
        $format = $request->params['format'];
    }
    
    // Get format from response
    if ($request && $request->response) {
        $response = $request->response;
        if ($response->format) {
            $format = $request->response->format;
        }

        if (!empty($response->headers['Content-Type'])) {
            $format_guess = \Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser::getInstance()
                ->guess($response->headers['Content-Type']);
            if ($format_guess) {
                $format = $format_guess;
            }
        }
    }
    
    // Return a valid JSON response in case of error
    if ($format == 'json') {
        if (!headers_sent()) {
            header("Content-Type: application/json");
        }
        ob_end_clean();
        echo $app->dump__json(array(
            'error' => array(
                'code' => $ex->getCode(),
                'message' => $debug ? "$ex" : $ex->getMessage(),
                'trace' => $debug ? $ex->getTraceAsString() : null,
            ),
        ));
        return false;
    }
};
