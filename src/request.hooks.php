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

// Default dispatcher.
$hooks['request.dispatch'][0][] = function (Request $request, &$response) {
    
    // Dispatch action.
    if ($request->action) {
        $response = $request->action->execute($request);
    }
    
    // We have a simple, text response.
    if (is_string($response)) {
        $response = new Response($request, $response);
    }
    
    // We have a data response.
    elseif (is_array($response)) {
        $response = new Response\DataResponse($request, $response);
    }
};

// If no view was rendered so far, then there's something wrong.
$hooks['response.render'][1000][] = function (Response $response) {
    throw new \LogicException("View not rendered: {$response->view}");
};
