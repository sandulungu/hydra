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

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * HTTP Response holder. 
 */
class Response {
    
    static protected $_headers = array();
    
    var $data, $headers, $variables, $view, $content, $body, $statusCode = 200;
    
    /**
     * @var App
     */
    var $app;
    
    /**
     * @var Request
     */
    var $request;

    function __construct(Request $request, $body = '') {
        $this->app = $request->app;
        $this->request = $request;
        $request->response = $this;
        
        $this->body = $body;
        $this->format = $request->params['format'];
        $this->view = $this->request->isMain ? "default.$this->format.twig" : "partial.$this->format.twig";
        $this->headers =& self::$_headers;
    }
    
    function render($render_stream = true) {
        if (!isset($this->content)) {
            if ($this->view) {
                $this->variables['response'] = $this;
                $this->variables['baseurl'] = $this->app->core->baseurl;
                $this->variables['webroot'] = $this->app->core->webroot;
                $this->content = $this->app->hook('response.render', $this);
            } else {
                $this->content = $this->body;
            }
        }
        if ($render_stream && $this->content instanceof \Closure) {
            ob_start();
            $this->content($this);
            $this->content = ob_get_clean();
        }
        $this->app->hook('response.after_render', $this);
        return $this->content;
    }
    
    function output() {
        $this->app->hook('response.output_prepare', $this);
        
        // Render before sending headers, as they may be changed in sub-actions.
        $this->render(false);
        
        // Send headers.
        if ($this->statusCode != 200 && isset(SymfonyResponse::$statusTexts[$this->statusCode])) {
            header(sprintf('HTTP/1.0 %s %s', $this->statusCode, SymfonyResponse::$statusTexts[$this->statusCode]));
        }
        if ($this->headers) {
            foreach($this->headers as $header => $value) {
                header("$header: $value");
            }
        }
        
        // Streaming support.
        if ($this->content instanceof \Closure) {
            
            // Make sure we're not buffering the output.
            while (ob_get_level()) {
                ob_end_flush();
            }
            ob_implicit_flush();
            
            $callback = $this->content;
            $callback($this);
        } else {
            echo $this->app->hook('response.output', $this, $this->content);
        }
        
        return $this;
    }
}
