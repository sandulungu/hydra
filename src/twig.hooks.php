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

// Set default configuration options.
$hooks['app.config'][-1000][] = function (&$config, &$dummy, App $app) {
    $config['twig.options'] = array(
        'cache' => "{$app->core->data_dir}/cache/twig",
        'debug' => $app->core->debug,
        'strict_variables' => true,
    );
    $config['twig.dirs'] = array();
    $app_views_dir = "{$app->core->app_dir}/views";
    if (is_dir($app_views_dir)) {
        $config['twig.dirs'][] = $app_views_dir;
    }
};

// Core views dir should be the last one in the list.
$hooks['app.config'][1000][] = function (&$config) {
    $config['twig.dirs'][] = __DIR__ . '/../views';
};

// Render template.
$hooks['response.render'][0][] = function (Response\RenderedResponse $response) {
    if (preg_match('/\.twig$/', $response->view)) {
        return $response->app->twig->render($response->view, $response->variables);
    }
};
