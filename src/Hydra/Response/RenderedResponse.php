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

use Hydra\Request;
use Hydra\Response;

/**
 * HTTP Response holder. 
 */
class RenderedResponse extends Response {
    
    var $variables, $view;
    
    function __construct(Request $request, $title = null, $body = null) {
        parent::__construct($request);
        $this->variables = array(
            'title' => $title,
            'body' => $body,
        );
        $this->view = $this->request->isMain ? "default.$this->format.twig" : "partial.$this->format.twig";
    }
    
    function render($render_stream = true) {
        if (!isset($this->content)) {
            if ($this->view) {
                $this->variables += array(
                    'response' => $this,
                    'baseurl' => $this->app->core->baseurl,
                    'webroot' => $this->app->core->webroot,
                );
                $this->content = $this->app->hook('response.render', $this);
            } else {
                $this->content = $this->variables['body'];
            }
        }
        return parent::render($render_stream);
    }
}
