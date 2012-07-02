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

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler as SymfonyHandler;

class ExceptionHandler extends \Symfony\Component\HttpKernel\Debug\ExceptionHandler {
    
    /**
     * @var App
     */
    public $app;
    
    function __construct($debug) {
        parent::__construct($debug);
    }
    
    protected function _handleNested($ex, $innerEx) {
        $class = get_class($innerEx);
        $ex = new \Exception("$class occured during exception handling: {$innerEx->getMessage()}", $innerEx->getCode(), $ex);
        $handler = new SymfonyHandler(true);
        $handler->handle($ex);
    }


    function handle(\Exception $exception) {
        if ($this->app) {
            try {
                $exception = $this->app->hook('app.exception', null, $exception);
            } catch (\Exception $innerEx) {
                return $this->_handleNested($exception, $innerEx);
            }
        }
        if ($exception) {
            if ($this->app && !$this->app->core->debug) {
                try {
                    $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;
                    $response = new Response\FancyResponse(
                        new Request\StaticRequest($this->app), 
                        array(
                            'code' => $code,
                            'title' => SymfonyResponse::$statusTexts[$code],
                            'exception' => $exception,
                        ), 
                        'error.html.twig'
                    );
                    $response->statusCode = $code;
                    $response->send();
                    return;
                } catch (\Exception $innerEx) {
                    return $this->_handleNested($exception, $innerEx);
                }
            }
            parent::handle($exception);
        }
    }
    
}