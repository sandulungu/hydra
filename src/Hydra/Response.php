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
    
    var $headers, $format, $content, $statusCode = 200;
    
    /**
     * @var App
     */
    var $app;
    
    /**
     * @var Request
     */
    var $request;

    function __construct(Request $request, $content = null) {
        $this->app = $request->app;
        $this->request = $request;
        $request->response = $this;
        $this->headers =& self::$_headers;
        
        // Set Content-Type header
        // Note: MimeTypeGuesser is a heavy class, so don't load it unnecessary
        $this->format = $request->params['format'];
        if ($request->isMain && $this->format != 'html') {
            $this->headers['Content-Type'] = $request->app->mimetype__extensionGuesser->guess($this->format);
        }
                
        $this->content = $content;
    }
    
    function render($render_stream = true) {
        if ($render_stream && $this->content instanceof \Closure) {
            ob_start();
            $this->content($this);
            $this->content = ob_get_clean();
        }
        $this->app->hook('response.after_render', $this);
        return $this->content;
    }
    
    /**
     * Formats cache-control headers.
     *
     * @param int  $ttl  Time to live in seconds
     * @param bool $send True to send headers immediately, false to append to $this->headers.
     */
    function expires($ttl = 3600, $send = false) {
        if ($ttl) {
            $headers['Expires'] = gmdate(DATE_RFC2822, time() + $ttl);
        } else {
            // No cache
        }
        
        if ($send) {
            $this->_sendHeaders($headers);
        } else {
            $this->headers = $headers + $this->headers;
        }
        return $this;
    }
    
    protected function _sendHeaders($headers) {
        foreach($headers as $header => $value) {
            if ($value) {
                if (is_array($value)) {
                    $replace = true;
                    foreach ($value as $v) {
                        header("$header: $v", $replace);
                        $replace = false;
                    }
                } else {
                    header("$header: $value");
                }
            }
        }
    }
    
    function output() {
        $this->app->hook('response.output_prepare', $this);
        
        // Render before sending headers, as they may be changed in sub-actions.
        $this->render(false);
        
        // Send headers.
        if ($this->statusCode != 200 && isset(SymfonyResponse::$statusTexts[$this->statusCode])) {
            header(sprintf('HTTP/1.1 %s %s', $this->statusCode, SymfonyResponse::$statusTexts[$this->statusCode]));
        }
        if ($this->headers) {
            $this->_sendHeaders($this->headers);
        }
        
        // Streaming support.
        if ($this->content instanceof \Closure) {
            $this->app->hook('response.output', $this);
            $callback = $this->content;
            $callback($this);
        } else {
            echo $this->app->hook('response.output', $this, $this->content);
        }
        
        return $this;
    }
}
