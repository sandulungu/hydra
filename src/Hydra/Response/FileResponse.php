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

namespace Hydra\Response;

use Hydra\Request;
use Hydra\Response;
use Hydra\Utils;

/**
 * Webservice response holder.
 * 
 * @property string $body
 */
class FileResponse extends Response {
    
    var $filename, $isPhp;

    function __construct(Request $request, $filename, $force_download = false) {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new \Hydra\Exception\NotFoundHttpException("File not found: $filename");
        }
        
        $this->isPhp = preg_match('/\.php$/', $filename);
        $this->filename = $filename;
        
        if ($force_download) {
            $this->headers['Content-Type'] = 'application/octet-stream';
        } else {

            // Try to guess Content-Type
            if (!$this->isPhp) {
                $this->headers['Content-Type'] = $request->app->mimetype__guesser->guess($filename);
            } 
            elseif ($request->app->config->response['guess_php_contentType']) {
                $this->headers['Content-Type'] = $request->app->mimetype__extensionGuesser->guess(substr($filename, 0, -4));
            }
        }
        
        if (!$this->isPhp) {
            $this->headers['Content-Length'] = filesize($filename);
        }

        $response = $this;
        parent::__construct($request, function() use ($response) {
            
            // We don't want any left-over output when sending a file.
            ob_end_clean();
 
            if ($response->isPhp) {
                $request = $response->request;
                $app = $response->app;
                require $response->filename;
            } else {
                if ($response->app->config->response['x_sendfile']) {
                    $filename = realpath($response->filename);
                    header("X-Sendfile: $filename");
                } else {
                    set_time_limit(0); //Set the execution time to infinite.
                    readfile($response->filename);
                }
            }
        });
    }
    
}
