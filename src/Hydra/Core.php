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

/**
 * Core configuration class.
 * 
 * @property bool $debug
 * @property string $register_exception_handler
 * @property string $register_error_handler
 * @property string $app_dir
 * @property string $core_dir
 * @property string $data_dir
 */
class Core {
    
    protected $_config;
    
    var $errorHandler, $exception_handler;

    function __construct(array $config = array()) {
        $this->_config =& $config;
        
        // Try to locate app folder.
        if (!isset($config['app_dir'])) {
            $cwd = getcwd();
            while (!is_dir("$cwd/app")) {
                if ($cwd == dirname($cwd)) {
                    throw new \LogicException('/app folder not found.');
                }
                $cwd = dirname($cwd);
            }
            $config['app_dir'] = "$cwd/app";
        }

        $is_web_request = isset($_SERVER['SERVER_NAME']);
        
        $config += array(
            'debug' => !$is_web_request || $_SERVER['SERVER_NAME'] == 'localhost',
            'register_exception_handler' => $is_web_request,
            'register_error_handler' => $is_web_request,
            'core_dir' => __DIR__ . '/../..',
            'data_dir' => "{$config['app_dir']}/../data",
        );
        
        $this->exception_handler = new ExceptionHandler($this->debug);
        if ($this->register_exception_handler) {
            set_exception_handler(array($this->exception_handler, 'handle'));
        }
        if ($this->register_error_handler) {
            $this->errorHandler = \Symfony\Component\HttpKernel\Debug\ErrorHandler::register();
        }
        
        foreach (array($config['data_dir'], "{$config['data_dir']}/cache", "{$config['data_dir']}/logs") as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }
    }
    
    function __isset($name) {
        return isset($this->_config[$name]);
    }
 
    function __get($name) {
        return $this->_config[$name];
    }
    
}
