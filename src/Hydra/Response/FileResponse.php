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
    
    var $filename;

    function __construct(Request $request, $filename, $force_download = false) {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new \Hydra\Exception\NotFoundHttpException("File not found: $filename");
        }
        
        $this->filename = $filename;
        $phpfile = preg_match('/\.php$/', $filename);
        
        if ($force_download) {
            $this->headers['Content-Type'] = 'application/octet-stream';
        } else {

            // Try to guess Content-Type
            if (!$phpfile) {
                $this->headers['Content-Type'] = $request->app->mimetype__guesser->guess($filename);
            } 
            elseif ($request->app->config->response['guess_php_contentType']) {
                $this->headers['Content-Type'] = $request->app->mimetype__extensionGuesser->guess(substr($filename, 0, -4));
            }
        }
        
        if (!$phpfile) {
            $this->headers['Content-Length'] = filesize($filename);
        }

        parent::__construct($request, function() use ($filename, $phpfile, $request) {
            
            // We don't want any left-over output when sending a file.
            ob_end_clean();
 
            if ($phpfile) {
                $response = $request->response;
                $app = $request->app;
                require $filename;
            } else {
                if ($request->app->config->response['x_sendfile']) {
                    $filename = realpath($filename);
                    header("X-Sendfile: $filename");
                } else {
                    set_time_limit(0); //Set the execution time to infinite.
                    readfile($filename);
                }
            }
        });
    }
    
}
