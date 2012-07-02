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

namespace Hydra\Request;

use Hydra\App;
use Hydra\Request;

/**
 * A Request with a prepopulated Response.
 */
class StaticRequest extends HttpRequest {
    
    var $action, $params = array('format' => 'html'); // Kill services
    
    function __construct(App $app, $response = null) {
        parent::__construct($app, null);
        $this->response = $response;
    }

}