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

$hooks['app.config'][-1000][] = function(&$config) {
    $config['mongodb'] = array(
        'uri' => null,
        'dbname' => 'default',
    );
    $config['pdo'] = array(
        'dsn' => 'mysql:host=localhost;dbname=default',
        'utf8' => true,
        'username' => 'root',
        'passwd' => '',
    );
};

$services['app.mongodb'][0] = function(App $app) {
    $mongo = new \Mongo($app->config['mongodb']['uri']);
    return $mongodb = $mongo->selectDB($app->config['mongodb']['dbname']);
};

$services['app.pdo'][0] = function() {
    $pdo = new \PDO($app->config['pdo']['uri'], $app->config['pdo']['username'], $app->config['pdo']['passwd']);
    if ($app->config['pdo']['utf8']) {
        $pdo->exec('SET NAMES utf8');
    }
    return $pdo;
};
