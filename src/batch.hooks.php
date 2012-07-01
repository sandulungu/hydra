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

$hooks['app.routes'][0][] = function(App $app) {
    return array(
        array('hydra/batch/%id:batch', function(Request $request, $id, $format) {
            $batch =& $request->app->session['batch'][$id];

            // Show overview page
            if ($format == 'html') {
                $request->app->session['batch.status'][$id] = 'prepare';
                $method = "batch__{$id}__info";
                $info = (array)$request->$method($batch) + array(
                    'id' => $id,
                    'title' => "Batch processing » $id",
                );
                return new Response\FancyResponse($request, $info);
            }

            // Do the hard work!
            else {
                if (!$batch) {
                    unset($request->app->session['batch.status'][$id]);
                    unset($request->app->session['batch'][$id]);
                    return array('done' => true);
                }
                $data = reset($batch);
                if ($request->app->session['batch.status'][$id] == 'prepare') {
                    $request->app->session['batch.status'][$id] = 'process';
                    
                    $method = "batch__{$id}__prepare";
                    $text = false;
                    $error = null;
                    try {
                        $text = $request->$method($data);
                    } catch(\Exception $ex) {
                        $error = $request->app->core->debug ? "$ex" : true;
                        $request->app->monolog__logException($ex);
                    }
                    return array(
                        'done' => $text === false, 
                        'text' => is_string($text) && $text ? $text : 'Processing',
                        'error' => $error,
                    );
                    
                } else {
                    $method = "batch__{$id}__process";
                    ob_start();
                    $error = $progress = null;
                    try {
                        $progress = $request->$method($data);
                    } catch(\Exception $ex) {
                        $error = $request->app->core->debug ? "$ex" : true;
                        $request->app->monolog__logException($ex);
                    }
                    $html = ob_get_clean();
                    $done = $progress === true || $progress === null;
                    if ($done) {
                        array_shift($batch);
                        $request->app->session['batch.status'][$id] = 'prepare';
                    }
                    return array(
                        'done' => $done,
                        'progress' => $done ? null : $progress,
                        'html' => $html,
                        'error' => $error,
                    );
                }
            }
        }, array('format' => 'html|json'))
    );
};

$methods['app.normalize.batch'][0] = function(App $app, $data) {
    return isset($app->session['batch'][$data]) ? $data : null;
};
