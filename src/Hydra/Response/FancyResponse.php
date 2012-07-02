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

namespace Hydra\Response;

use Hydra\Request;

/**
 * Response for action-specific views. 
 */
class FancyResponse extends RenderedResponse {
    
    function __construct(Request $request, array $variables = array(), $view = null) {
        parent::__construct($request);
        $this->variables = $variables;
        $this->view = $view ? $view : "$request->action.$this->format.twig";
    }
    
}
