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

use Hydra\Request;
use Hydra\Response;

/**
 * Webservice response holder.
 * 
 * @property string $body
 */
class DataResponse extends RenderedResponse {
    
    var $data;

    function __construct(Request $request, $data = array(), $title = 'Data dump') {
        parent::__construct($request, $title);
        $this->data = $data;
    }
    
    function render() {
        if (!isset($this->content)) {
            $this->variables['body'] = $this->app["method:dump.$this->format"]($this->data);
            parent::render();
        }
    }
    
}