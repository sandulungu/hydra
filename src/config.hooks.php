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

// Default app setings
$hooks['app.config'][-1000][] = function (&$config) {
    $config['i18n.default_lang'] = 'en';
    
    $config['session.name'] = 'hydra';
    
    $config['cookies.expiresIn'] = 60*60*24*365; // 1 year
    $config['cookies.path'] = null;
    $config['cookies.domain'] = null;
    $config['cookies.secure'] = null;
    $config['cookies.httponly'] = null;
};

// Load app configuration options.
$hooks['app.config'][0][] = function (&$config, &$dummy, App $app) {
    if (file_exists($app->core->app_config_file)) {
        require $app->core->app_config_file;
    }
};
