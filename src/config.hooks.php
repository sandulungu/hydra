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

// Default app setings.
$hooks['app.config'][-1000][] = function (&$config) {
    $config['i18n.default_lang'] = 'en';
    $config['vendor.web_dirs'] = array();
    
    $config['response']['x_sendfile'] = false;
    $config['response']['render_string'] = true;
    
    $config['session']['name'] = 'hydra';
    
    $config['cookie']['expiresIn'] = 60*60*24*365; // 1 year
    $config['cookie']['path'] = null;
    $config['cookie']['domain'] = null;
    $config['cookie']['secure'] = null;
    $config['cookie']['httponly'] = null;
    
    // Do not override these if you don't know exactly what you're doing.
    $config['security']['token.cookie'] = 'hydra.token';
    $config['security']['token.autocheck'] = true;
    $config['security']['token.param'] = 'token';
    $config['security']['headers'] = array(
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1;mode=block',
    ); 
    
    $config['assets.js'] = array();
    $config['assets.css'] = array();
};

// Load app configuration options from config file.
$hooks['app.config'][0][] = function (&$config, &$dummy, App $app) {
    if (file_exists($app->core->app_config_file)) {
        require $app->core->app_config_file;
    }
};
