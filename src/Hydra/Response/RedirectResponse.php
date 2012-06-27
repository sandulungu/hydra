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

namespace Hydra\Response;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use Hydra\Request\HttpRequest;
use Hydra\Response;

/**
 * HTTP Response holder. 
 */
class RedirectResponse extends Response {
    
    var $variables, $view;
    
    function __construct(HttpRequest $request, $uri = '') {
        parent::__construct($request);
        if (!$uri || !preg_match('`^(/|[a-z0-9]+://)`', $uri)) {
            $uri = $request->baseurl .'/'. ltrim($uri, '/');
        }
        $this->headers['Location'] = $uri;
    }

}
