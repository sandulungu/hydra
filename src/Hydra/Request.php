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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * HTTP Request holder.
 * 
 * This class does not contain cookies or server environment variable, as they are only relevant to the main request.
 * If needed, just use PHP's native $_COOKIE and $_SERVER.
 * 
 * @property Action $action
 * @property array $params
 */
class Request extends Container {
    
    /**
     * @var App
     */
    var $app;
    
    var $query, $data, $method, $path, $response, $isMain = false;

    function __construct(App $app, $path, $method = 'GET', $query = array(), $data = null) {
        $this->app = $app;
        $this->query = $query;
        $this->data = $data;
        $this->method = $method;
        $this->path = $path;
        $this->isMain = empty($app->requests);
        parent::__construct('request');
    }
    
    function dispatch() {
        return $this->app->hook('request.dispatch', $this);
    }

    /**
     * Current language code. Multiplingual support should be implemented in an external service.
     */
    function service__lang() {
        return $this->app->config->l;
    }
    
    /**
     * A lazy-loading shortcut for action params.
     */
    function &service__params() {
        return $this->action->params;
    }

    /**
     *  Match current path and get action.
     */
    function service__action() {
        $action = $this->app->hook('request.route_action', $this);
        if (!$action) {
            throw new NotFoundHttpException("No route matched path: {$this->path}.");
        }
        return $action;
    }

}