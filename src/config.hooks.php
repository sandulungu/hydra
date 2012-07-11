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

mb_internal_encoding("UTF-8"); // We should never-ever have to change this.

// Default app setings.
$hooks['app.config'][-1000][] = function (&$config) {
    $config['i18n.defaultLang'] = 'en';
    $config['vendor.webDirs'] = array();
    
    $config['response']['guessPhpContentType'] = true;
    $config['response']['XSendfile'] = false;
    $config['response']['renderString'] = true;
    
    $config['session']['name'] = 'hydra';
    $config['session']['userKey'] = 'user';
    
    $config['cookie']['expiresIn'] = 60*60*24*365; // 1 year
    $config['cookie']['path'] = null;
    $config['cookie']['domain'] = null;
    $config['cookie']['secure'] = null;
    $config['cookie']['httponly'] = null;
    
    $config['assets.js'] = array();
    $config['assets.css'] = array();
    
    $config['app']['title'] = 'Hydra';
    
    $config['security']['token.cookie'] = 'hydra.token';
    $config['security']['token.autocheck'] = true;
    $config['security']['token.param'] = 'token';
    $config['security']['headers'] = array();
};

// Load app configuration options from config file.
$hooks['app.config'][0][] = function (&$config, &$dummy, App $app) {
    $app_config_file = "{$app->core->app_dir}/config.php";
    if (file_exists($app_config_file)) {
        require $app_config_file;
    }
};
