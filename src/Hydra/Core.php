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

/**
 * Core configuration class.
 * 
 * @property bool $debug 
 * @property string $register_exception_handler
 * @property string $register_error_handler
 * @property string $app_config_file
 * @property string $app_hooks_dir
 * @property string $app_plugins_dir
 * @property string $app_src_dir
 * @property string $app_views_dir
 * @property string $core_config_file
 * @property string $core_dir
 * @property string $cache_dir
 * @property string $data_dir
 * @property string $logs_dir
 * @property string $baseurl
 * @property string $webroot
 */
class Core {
    
    protected $_config;

    function __construct(array $config = array()) {
        
        // Get base-url and web-root
        $url_rewritten = isset($_SERVER['REDIRECT_URL']);
        $baseurl = $_SERVER['SCRIPT_NAME'];
        if ($url_rewritten) {
            $uri = $_SERVER['REQUEST_URI'];
            for($i = 0; $i < strlen($uri) && $i < strlen($baseurl) && $uri[$i] == $baseurl[$i]; $i++) {}
            $baseurl = $webroot = rtrim(substr($baseurl, 0, $i), '/');
        } else {
            $webroot = dirname($baseurl);
        }
        
        $this->_config =& $config;
        $config += array(
            'debug' => false,
            'app_config_file' => '../app/config.php',
            'app_hooks_dir' => '../app/hooks',
            'app_plugins_dir' => '../app/plugins',
            'app_src_dir' => '../app/src',
            'app_views_dir' => '../app/views',
            'core_dir' => __DIR__ . '/../..',
            'cache_dir' => '../data/cache',
            'data_dir' => '../data',
            'logs_dir' => '../data/logs',
            'register_exception_handler' => true,
            'register_error_handler' => true,
            'baseurl' => $baseurl,
            'webroot' => $webroot,
        );
        
        if ($this->register_exception_handler) {
            \Symfony\Component\HttpKernel\Debug\ExceptionHandler::register($this->debug);
        }
        if ($this->register_error_handler) {
            \Symfony\Component\HttpKernel\Debug\ErrorHandler::register();
        }
        
        foreach (array('data_dir', 'cache_dir', 'logs_dir', 'app_views_dir') as $name) {
            if (!is_dir($config[$name])) {
                mkdir($config[$name]);
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
