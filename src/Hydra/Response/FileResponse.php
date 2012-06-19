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

namespace Hydra\Response;

use Hydra\Request;
use Hydra\Response;

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
        
        $app = $request->app;
        parent::__construct($request);
        $this->filename = $filename;
        $phpfile = preg_match('/\.php$/', $filename);
        
        if ($force_download) {
            $this->headers['Content-Type']         = 'application/octet-stream';
            $this->headers['Content-Dispositions'] = 'attachment; filename=' . basename($phpfile ? sustr($filename, 0, -4) : $filename);
        } else {
            
            // In case of dynamically generated content, Content-Type cann't be guessed at this stage
            if (!$phpfile) {
                $this->headers['Content-Type'] = $app->mimetype__guesser->guess($filename);
            }
        }
        if (!$phpfile) {
            $this->headers['Content-Length'] = filesize($filename);
        }

        $this->content = function() use ($filename, $phpfile, $app) {
            
            // We don't want any left-over output when sending a file.
            ob_end_clean();
 
            if ($phpfile) {
                require $filename;
            } else {
                if ($app->config['response.x_sendfile']) {
                    $filename = realpath($filename);
                    header("X-Sendfile: $filename");
                } else {
                    set_time_limit(0); //Set the execution time to infinite.
                    readfile($filename);
                }
            }
        };
    }
    
}
