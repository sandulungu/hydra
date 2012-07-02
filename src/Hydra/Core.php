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
 * @property ExceptionHandler $exception_handler
 */
class Core {
    
    protected $_config;

    function __construct(array $config = array()) {
        $this->_config =& $config;
        $config += array(
            'debug' => false,
            'register_exception_handler' => true,
            'register_error_handler' => true,
            'core_dir' => __DIR__ . '/../..',
            'data_dir' => '../data',
            'app_dir' => '../app',
        );
        
        $this->exception_handler = new ExceptionHandler($this->debug);
        if ($this->register_exception_handler) {
            set_exception_handler(array($this->exception_handler, 'handle'));
        }
        if ($this->register_error_handler) {
            \Symfony\Component\HttpKernel\Debug\ErrorHandler::register();
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
