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

namespace Hydra\Action;

use Hydra\Request;
use Hydra\Action;

class CallbackAction extends Action {
    
    protected $_callback;

    function __construct(\Closure $callback, array $params = array(), $name = 'default', $pattern = null) {
        $this->_callback = $callback;
        parent::__construct($params, $name, $pattern);
    }
    
    function execute(Request $request) {
        return $this->_invokeAction($request, $this->_callback);
    }
    
}
