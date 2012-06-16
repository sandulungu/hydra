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

// If no view was rendered so far, then there's something wrong.
$hooks['response.render'][1000][] = function (Response $response) {
    throw new \LogicException("View not rendered: {$response->view}");
};

// Set default Content-Types.
$hooks['response.output_prepare'][0][] = function (Response $response) {
    $format = $response->request->params['format'];
    if (empty($response->headers['Content-Type']) && $format == 'json') {            
        $response->headers['Content-Type'] = 'application/json';
    }
};
