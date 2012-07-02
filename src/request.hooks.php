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

// Apply registered routes.
$hooks['request.route'][1000][] = function (Request $request) {
    $request->app->routes;
    
    // No user routes defined? Show an information page.
    if (!$request->app->routes__defined) {
        return new Action(function() {
            return 'Please define your routes in <strong>web/index.php or create a controller in <strong>app/src/App/Controller/</strong> folder.';
        });
    }
    
    foreach ($request->app->routes as $args) {
        $route = call_user_func_array('Hydra\Action::match', array(-1 => $request) + $args);
        if ($route) {
            return $route;
        }
    }
};

// Check CRSF token.
$hooks['request.dispatch'][-1000][] = function (Request $request, &$response, App $app) {
    if ($app->config->security['token.autocheck'] && !in_array($request->method, array('HEAD', 'GET'))) {
        $param = $app->config->security['token.param'];
        if (empty($request->data[$param])) {
            throw new Exception\InvalidTokenHttpException("Security token missing. All non-GET methods should post a 'token' parameter.");
        }
        if ($request->data[$param] != $app->security__token) {
            throw new Exception\InvalidTokenHttpException('An invalid or expired token has been specified.');
        }
    }
};

// Default dispatcher.
$hooks['request.dispatch'][0][] = function (Request $request, &$response) {
    
    if (!$response && $request->action) {
        $response = $request->action->execute($request);
    }
    
    if (is_string($response)) {
        $response = $request->app->config->response['render_string'] ?
            new Response\RenderedResponse($request, null, $response) :
            new Response($request, $response);
    }
    
    elseif ($response instanceof \Closure) {
        $response = new Response($request, $response);
    }
    
    elseif (isset($response) && !$response instanceof Response) {
        $response = new Response\DataResponse($request, $response);
    }
};

// If no view was rendered so far, then there's something wrong.
$hooks['response.render'][1000][] = function (Response $response) {
    throw new \LogicException("View not rendered: {$response->view}");
};

// Security related headers.
$hooks['response.before_send'][-1000][] = function (Response $response) {
    if ($response->request->isMain) {
        $response->headers += $response->app->config->security['headers'];
    }
};

// Better exception output for JSON, JS and other non-HTML requests
$hooks['app.exception'][1000][] = function(\Exception $ex, $dummy, App $app) {
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
