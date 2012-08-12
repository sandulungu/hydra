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
$hooks['app.config'][-1000][] = function (App $app, &$config) {
    $config['i18n.defaultLang'] = 'en';
    $config['vendor.webDirs'] = array();
    
    $config['response']['guessPhpContentType'] = true;
    $config['response']['XSendfile'] = false;
    $config['response']['renderString'] = true;
    
    $config['session']['name'] = 'hydra';
    $config['session']['section'] = 'hydra';
    
    $config['cookie']['expiresIn'] = 60*60*24*365; // 1 year
    $config['cookie']['path'] = null;
    $config['cookie']['domain'] = null;
    $config['cookie']['secure'] = null;
    $config['cookie']['httponly'] = null;
    
    $config['assets.js'] = array();
    $config['assets.css'] = array();
    
    $config['app']['title'] = 'Hydra';
    
    $config['security']['token.autocheck'] = true;
    $config['security']['token.param'] = 'token';
    $config['security']['headers'] = array();

    $config['mongodb']['default'] = array(
        'uri' => null,
        'dbname' => 'hydra',
    );

    $config['pdo']['default'] = array(
        'dsn' => 'mysql:host=localhost;dbname=hydra;charset=utf8',
        'setNamesUtf8' => true,
        'username' => 'root',
        'password' => '',
    );
};

// Load app configuration options from config file.
$hooks['app.config'][0][] = function (App $app, &$config) {
    $app_config_file = "{$app->core->app_dir}/config.php";
    if (file_exists($app_config_file)) {
        require $app_config_file;
    }
};

// Start session.
$hooks['app.session.start'][0][] = function ($session_name) {
    session_name($session_name);
    session_start();
};

// Create a default (anonymous) user object.
$hooks['app.user'][0][] = function (App $app, &$user) {
    if (!$user) {
        $user = new User\Anonymous($app);
    }
};

// Init PDO and MongoDB services.
$hooks['app.init'][0][] = function (App $app, &$services) {
    foreach ($app->config->mongodb as $name => $params) {
        $services['app.mongodb' . ($name == 'default' ? '' : ".$name")][0] = function() use ($params) {
            $mongo = new \Mongo($params['uri']);
            return $mongodb = $mongo->selectDB($params['dbname']);
        };
    }
    
    foreach ($app->config->pdo as $name => $params) {
        $services['app.pdo' . ($name == 'default' ? '' : ".$name")][0] = function() use ($params) {
            $pdo = new \PDO($params['dsn'], $params['username'], $params['password']);
            if ($params['setNamesUtf8']) {
                $pdo->exec('SET NAMES utf8');
            }
            return $pdo;
        };
    }

};
