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

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

// Apply registered routes.
$hooks['request.route'][1000][] = function (Request $request) {
    foreach ($request->app->routes as $args) {
        $route = call_user_func_array('Hydra\Action::match', array(-1 => $request) + $args);
        if ($route) {
            return $route;
        }
    }
};

// Check CRSF token for Authenticated users.
$hooks['request.dispatch'][-1000][] = function (Request $request, &$response, App $app) {
    if ($app->config->security['token.autocheck'] && !in_array($request->method, array('HEAD', 'GET'))) {
        
        // Anonymous users, Bots and web-service Consumers don't need this type of security.
        if (!$app->user instanceof User\Authenticated) {
            return;
        }
        
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
        $response = $request->app->config->response['renderString'] ?
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
    if ($response->statusCode != 200 && isset(SymfonyResponse::$statusTexts[$response->statusCode])) {
        array_unshift($response->headers, sprintf('HTTP/1.1 %s %s', $response->statusCode, SymfonyResponse::$statusTexts[$response->statusCode]));
    }
    if ($response->request->isMain) {
        $response->headers += $response->app->config->security['headers'];
    }
};

// Security related headers.
$hooks['response.send'][1000][] = function (Response $response, &$content) {
    echo $content;
};

$hooks['response.send_headers'][1000][] = function (Response $response, &$headers) {
    foreach($headers as $name => $value) {
        if ($value) {
            if (is_array($value)) {
                $replace = true;
                foreach ($value as $v) {
                    header("$name: $v", $replace);
                    $replace = false;
                }
            } 
            elseif ($value === false) {
                header_remove($name);
            }
            elseif (isset($value)) {
                is_int($name) ? header($value, false) : header("$name: $value");
            }
        }
    }
};
