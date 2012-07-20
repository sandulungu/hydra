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

// Default database config.
$hooks['app.config'][-1000][] = function(&$config) {
    $config['mongodb'] = array(
        'uri' => null,
        'dbname' => 'hydra',
    );
    
    $config['pdo'] = array(
        'dsn' => 'mysql:host=localhost;dbname=hydra',
        'utf8' => true,
        'username' => 'root',
        'passwd' => '',
    );
    $config['config.persist.pdo'] = array(
        'enabled' => false,
        'table' => 'config',
        'createIfNotExists' => true,
        'schema' => 'name VARCHAR(255) NOT NULL, value BLOB, PRIMARY KEY (name)',
    );
};

// Default MongoDB service.
$services['app.mongodb'][0] = function(App $app) {
    $mongo = new \Mongo($app->config['mongodb']['uri']);
    return $mongodb = $mongo->selectDB($app->config['mongodb']['dbname']);
};

// Default PDO service.
$services['app.pdo'][0] = function(App $app) {
    $pdo = new \PDO($app->config['pdo']['dsn'], $app->config['pdo']['username'], $app->config['pdo']['passwd']);
    if ($app->config['pdo']['utf8']) {
        $pdo->exec('SET NAMES utf8');
    }
    return $pdo;
};

$hooks['app.init'][0][] = function(App $app) use (&$methods) {
    
    // PDO config variables persister.
    if ($app->config['config.persist.pdo']['enabled']) {
        $methods['app.config.persist'][0] = function(App $app, $name = null, $value = null, $reset = false) {
            static $values;
            $table = $app->config['config.persist.pdo']['table'];

            if (!isset($values)) {
                if ($app->config['config.persist.pdo']['createIfNotExists']) {
                    $data = $app->pdo
                        ->exec("CREATE TABLE IF NOT EXISTS $table ({$app->config['config.persist.pdo']['schema']})");
                }
                
                $values = array();
                $data = $app->pdo
                    ->query("SELECT name, value FROM $table")
                    ->fetchAll();
                foreach ($data as $item) {
                    $values[$item['name']] = unserialize($item['value']);
                }
            }

            if ($name) {
                if ($reset) {
                    $statement = $app->pdo
                        ->prepare("DELETE FROM $table WHERE name = :name");
                    $statement->bindParam('name', $name);
                    $statement->execute();
                    
                    unset($values[$name]);
                } else {
                    if (array_key_exists($name, $values)) {
                        $statement = $app->pdo
                            ->prepare("UPDATE $table SET value = :value WHERE name = :name");
                    } else {
                        $statement = $app->pdo
                            ->prepare("INSERT INTO $table (name, value) VALUES (:name, :value)");
                    }
                    $statement->bindParam('name', $name);
                    $statement->bindParam('value', serialize($value));
                    $statement->execute();
                    
                    $values[$name] = $value;
                }
            }
            return $values;
        };
    }
    
    // TODO: PDO cache, MongoDB config variables persister, MongoDB cache.
};