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

// Apply registered routes.
$hooks['request.route'][1000][] = function (Request $request) {
    $request->app->routes;
    
    // No user routes defined? Show an information page.
    if (!$request->app->routes__defined) {
        return new Action(function() {
            return 'Welcome! Please define your routes in <strong>web/index.php, app/config.php</strong> or <strong>app/hooks/route.hooks.php</strong>.';
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
$hooks['response.output_prepare'][-1000][] = function (Response $response) {
    if ($response->request->isMain) {
        foreach ($response->app->config->security['headers'] as $header => $value) {
            $response->headers[$header] = $value;
        }
    }
};
