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

use Hydra\Exception;
use Hydra\Request;
use Hydra\Action;

class ControllerAction extends Action {
    
    function execute(Request $request) {
        $this->params += array(
            // we use '.' character here for an obscure security reason >:)
            'controller.class' => 'App\Controller\%sController',
            'controller'        => 'Default', 
            'action'            => 'default'
        );
        $controller_class = sprintf($this->params['controller.class'], ucfirst(preg_replace('/[^a-z0-9]+/i', '', $this->params['controller'])));
        if (!class_exists($controller_class)) {
            throw new Exception\InvalidControllerException("Controller not found: $controller_class.");
        }
        $controller = new $controller_class($request);

        $action_method = preg_replace('/[^a-z0-9]+/i', '', $this->params['action']) . 'Action';
        if (!method_exists($controller, $action_method)) {
            throw new Exception\InvalidControllerActionException("Action '$action_method' not not defined in controller: $controller_class.");
        }
        return $this->_invokeAction($request, $controller, $action_method);
    }
    
}
