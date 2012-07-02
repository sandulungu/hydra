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
                    $text = $request->$method($data);
                    return array(
                        'done' => $text === false, 
                        'text' => is_string($text) && $text ? $text : 'Processing',
                    );
                    
                } else {
                    $method = "batch__{$id}__process";
                    ob_start();
                    
                    $ex = null;
                    try {
                        $progress = $request->$method($data);
                        $done = $progress === true || $progress === null;
                    } 
                    catch (\Exception $ex) {}
                    if ($ex || $done) {
                        array_shift($batch);
                        $request->app->session['batch.status'][$id] = 'prepare';
                    }
                    if ($ex) {
                        throw $ex;
                    }
                    
                    $html = ob_get_clean();
                    return array(
                        'done' => $done,
                        'progress' => $done ? null : $progress,
                        'html' => $html,
                    );
                }
            }
        }, array('format' => 'html|json'))
    );
};

$methods['app.normalize.batch'][0] = function(App $app, $data) {
    return isset($app->session['batch'][$data]) ? $data : null;
};
