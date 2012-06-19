<?php
/**
 * Monolog services configuration.
 * 
 * By default there are 2 log services configurated:
 *  - app.monolog.debug: Used to show debugging info in browser, using FirePHP and ChromePHP extension.
 *  - app.monolog.main:  Main application log. In production, it will only log requests generating warnings.
 * 
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
    $config['monolog.mainLogFile'] = $app->core->logs_dir . 'main.log';
    
    $config['monolog.loggers'] = array(
        'debug' => array(
            'handlers' => array('firephp', 'chromephp'),
        ),
        'main' => array(
            'handlers' => array('main'),
        ),
    );
    
    $config['monolog.processors'] = array();
    
    $config['monolog.handlers'] = array(
        'firephp' => array('FirePHP'),
        'chromephp' => array('ChromePHP'),
        'main.stream' => array(
            'stream', 
            &$config['monolog.mainLogFile'], 
        ),
    );
    
    if ($app->core->debug) {
        $config['monolog.handlers']['main'] =& $config['monolog.handlers']['main.stream'];
    } else {
        $config['monolog.handlers']['main'] = array(
            'fingerscrossed', 
            function() use ($app) {
                return $app['monolog.handlers.main.stream'];
            },
        );
    }
};

// Register logger services.
$hooks['app.config'][2000][] = function (&$config, &$dummy, App $app) use (&$services) {
    
    foreach ($config['monolog.handlers'] as $name => $params) {
        $services["app.monolog.handlers.$name"][] = function() use ($name, $params, $app) {
            $class = array_shift($params);
            if (strpos($class, '\\') === false) {
                $class = "Monolog\Handler\\{$class}Handler";
            }
            return Utils::createClassInstance($class, $params);
        };
    }
    
    foreach ($config['monolog.processors'] as $name => $params) {
        $services["app.monolog.processors.$name"][] = function() use ($name, $params, $app) {
            $class = array_shift($params);
            if (strpos($class, '\\') === false) {
                $class = "Monolog\Processor\\{$class}Processor";
            }
            return Utils::createClassInstance($class, $params);
        };
    }
    
    foreach ($config['monolog.loggers'] as $name => $log_config) {
        $services["app.monolog.$name"][] = function() use ($name, $log_config, $app) {
            $log_config += array(
                'handlers' => array(),
                'processors' => array(),
            );
            $log = new \Monolog\Logger($name);
            foreach ($log_config['handlers'] as $handler) {
                $log->pushHandler($app["monolog.handlers.$handler"]);
            }
            foreach ($log_config['processors'] as $processor) {
                $log->pushProcessor($app["monolog.handlers.$processor"]);
            }
            return $log;
        };
    }
};
