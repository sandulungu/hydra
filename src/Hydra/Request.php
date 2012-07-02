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

namespace Hydra;

/**
 * Request holder.
 * 
 * @property Action $action
 * @property array $params
 */
class Request extends Container {
    
    /**
     * @var App
     */
    var $app;
    
    /**
     * @var Response 
     */
    var $response;
    
    var $query, $data, $method, $path, $isMain = false;

    function __construct(App $app, $path, $method = 'GET', array $query = array(), $data = null) {
        $this->app = $app;
        $this->query = $query;
        $this->data = $data;
        $this->method = $method;
        $this->path = $path;
        $this->isMain = empty($app->requests);
        parent::__construct('request');
    }
    
    /**
     * @return Response
     */
    function dispatch() {
        return $this->app->hook('request.dispatch', $this, $this->response);
    }

    /**
     * Current language code.
     * 
     * Multiplingual support should be implemented in an external service.
     */
    function service__lang() {
        return $this->app->config->i18n__default_lang;
    }
    
    /**
     * A shortcut for action params.
     */
    function &service__params() {
        return $this->action->params;
    }

    /**
     *  Match current path and get action.
     */
    function service__action() {
        $action = $this->app->hook('request.route', $this);
        if (!$action) {
            
            // Dynamic assets serving support
            if (file_exists("$this->path.php")) {
                return new Action(function(Request $request) {
                    return new Response\FileResponse($request, "$request->path.php");
                });
            }
            
            throw new Exception\NotFoundHttpException("No route matched path: {$this->path}.");
        }
        return $action;
    }

}