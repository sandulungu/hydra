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
    
    // We have a simple text response.
    if (is_string($response)) {
        $response = new Response($request, $response);
    }
    
    // This should be the default case.
    elseif (is_array($response)) {
        if ($request->params['format'] != 'html') {
            $response = new Response\DataResponse($request, $response);
        } else {
            $response = new Response\FancyResponse($request, $response);
        }
    }
};
