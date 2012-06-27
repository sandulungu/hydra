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

// Set default configuration options.
$hooks['app.config'][-1000][] = function (&$config, &$dummy, App $app) {
    $config['twig.options'] = array(
        'cache' => "{$app->core->cache_dir}/twig",
        'debug' => $app->core->debug,
    );
    $config['twig.dirs'] = array();
    if (is_dir($app->core->app_views_dir)) {
        $config['twig.dirs'][] = $app->core->app_views_dir;
    }
};

// Core views dir should be the last one in the list.
$hooks['app.config'][1000][] = function (&$config) {
    $config['twig.dirs'][] = __DIR__ . '/../views';
};

// Render template.
$hooks['response.render'][0][] = function ($response) {
    if (preg_match('/\.twig$/', $response->view)) {
        return $response->app->twig->render($response->view, $response->variables);
    }
};

// Register Twig service.
$services['app.twig'][0] = function(App $app) {
    $loader = new \Twig_Loader_Filesystem($app->config['twig.dirs']);
    $options = $app->config['twig.options'];
    if (!is_dir($options['cache'])) {
        mkdir($options['cache']);
    }
    return new \Twig_Environment($loader, $options);
};
