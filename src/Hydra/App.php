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

use Composer\Autoload\ClassLoader;

/**
 * Application singleton and main service container.
 * 
 * @property array              $session
 * @property Config             $config
 * @property \Twig_Environment  $twig
 * @property \PDO               $pdo
 * @property \MongoDB           $mongodb
 * @property string             $security__salt
 * 
 * @property \Monolog\Logger    $monolog__main
 * @property \Monolog\Logger    $monolog__debug
 * 
 * @method bool     run()
 * @method mixed    cache($name, $value = null, $reset = false)
 * @method mixed    config__persist($name, $value, $reset)
 * 
 * @method mixed    dump__json($data)
 * @method mixed    dump__html($data)
 * 
 * @method mixed    normalize__int($data)
 * @method mixed    normalize__float($data)
 * @method mixed    normalize__bool($data)
 * @method mixed    normalize__array($data)
 * @method mixed    normalize__string($data)
 * 
 * @method \Hydra\App route__get($pattern, $callback, $requirements = array(), $defaults = array())
 * @method \Hydra\App route__post($pattern, $callback, $requirements = array(), $defaults = array())
 * @method \Hydra\App route__put($pattern, $callback, $requirements = array(), $defaults = array())
 * @method \Hydra\App route__delete($pattern, $callback, $requirements = array(), $defaults = array())
 */
class App extends Container {
    
    protected $_hooks = array(), $_hookFiles = array();
    var $requests = array(), $routes = array();
    
    /**
     * @var Core
     */
    var $core;

    /**
     * @var ClassLoader
     */
    var $autoloader;
    
    /**
     * @var App
     */
    static protected $_instance;
    
    /**
     * @return App
     */
    static function getInstance(ClassLoader $autoloader = NULL, $core_config = array()) {
        if (!self::$_instance) {
            if (!$autoloader) {
                throw new \LogicException('The application requires an instance of Composer\Autoload\ClassLoader.');
            }
            self::$_instance = new static($autoloader, $core_config);
        }
        return self::$_instance;
    }
    
    function __construct(ClassLoader $autoloader, $core_config) {
        if (self::$_instance) {
            throw new \LogicException('The application is a singleton. Use Hydra\App::getInstance().');
        }
        $this->autoloader = $autoloader;
        $this->core = new Core($core_config);
        parent::__construct('app');
        $this->_init();
    }
 
    /**
     * Load module provided factories and hooks. 
     * Modules may want to use this together with caching for lazy loading custom data and routines.
     * 
     * Factories may be used by custom modules for extending core classes.
     * After being initialized, they behave like native object properties.
     * Factories may return Callbacks, ex: 'cache', creating pseudo-methods.
     * Some core functions, like hook(), cannot be extended this way because of performance considerations.
     */
    protected function _init() {
        $this->autoloader->add('App\\', $this->core->app_src_dir);
        
        $app_dir = $this->core->app_hooks_dir;
        $this->_hookFiles = $this->fallback__cache('core.hook_files', function() use ($app_dir) {
            $files = array();
            $iterator = new \AppendIterator();
            $iterator->append(new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(__DIR__ . '/..'),
                    \RecursiveIteratorIterator::CHILD_FIRST));
            $iterator->append(new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($app_dir),
                    \RecursiveIteratorIterator::CHILD_FIRST));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $filename = (string)$file;
                    if (preg_match('/\.hooks.php$/', $filename)) {
                        $files[] = (string)$file;
                    }
                }
            }
            return $files;
        });

        // Use late static binding. 
        // This way, we'll be able to issue service and remote method calls even during initialization.
        // It will be useful for services that have dependencies and will handle lazy initialization of dependencies properly even during bootstrap.
        $methods =& static::$_containerCallbacks['methods'];
        $services =& static::$_containerCallbacks['services'];
        
        $app = $this;
        $hooks =& $this->_hooks;
        foreach ($this->_hookFiles as $file) {
            require $file;
        }
        
        // Load app configuration
        $this->config;
    }
    
    /**
     * Serves main request.
     */
    public function fallback__run() {
        if (!$_POST) {
            $data = file_get_contents('php://input');
            if ($data) {
                $data = json_decode($data);
            } else {
                $data = null;
            }
        } else {
            $data = $_POST;
        }

        $path = ltrim(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : @$_GET['path'], '/');
        
        $response = $this->dispatch($path, $_SERVER['REQUEST_METHOD'], $_GET, $data);
        if ($response) {
            $response->output();
            return true;
        }
        return false;
    }
    
    /**
     * Renders a sub-request.
     */
    public function render($path, $method = 'GET', $query = array(), $data = null) {
        return $this->dispatch($path, $method, $query, $data)->render();
    }
    
    /**
     * Despatches a sub-request.
     * 
     * @return Response
     */
    public function dispatch($path, $method = 'GET', $query = array(), $data = null) {
        $request = new Request($this, $path, $_SERVER['REQUEST_METHOD'], $_GET, $data);
        $this->requests[] = $request;
        $response = $request->dispatch();
        return $response;
    }
    
    /**
     * Custom runtime configuration support. Equivalent to Drupal's variables.
     */
    public function service__config() {
        $app = $this;
        return new Config($app, $app->fallback__cache('app.config', function() use ($app) {
            return $app->hook('app.config');
        }));
    }
    
    /**
     * Cookies provider.
     */
    public function &service__cookies() {
        return new Cookies();
    }

    /**
     * Session provider.
     */
    public function &service__session() {
        session_name($this->config['session.name']);
        session_start();
        return $_SESSION;
    }

    /**
     * Generates security salt, then reuses it.
     */
    public function service__security__salt() {
        $filename = "{$this->core->data_dir}/security.salt";
        if (!file_exists($filename)) {
            $characterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            for ($i = 0, $salt = ''; $i < 128; $i++) {
                $salt .= $characterList{mt_rand(0, (strlen($characterList) - 1))};
            }
            file_put_contents($filename, $salt);
            return $salt;
        } else {
            return file_get_contents($filename);
        }
    }
    
    /**
     * Pluggable caching engine.
     */
    protected function fallback__cache($name, $value = null, $reset = false) {
        return $this->persist("{$this->core->cache_dir}/$name", $value, $this->core->debug || $reset);
    }

    /**
     * Default configuration persister.
     * 
     * @see \Hydra\Config
     */
    protected function fallback__config__persist($name = null, $value = null, $reset = false) {
        static $values;
        $filename = "{$this->core->data_dir}/config";
        
        if (!isset($values)) {
            $values = (array)$this->persist($filename);
        }
        
        if ($name) {
            if ($reset) {
                unset($values[$name]);
            } else {
                $values[$name] = $value;
            }
            $this->persist($filename, $values, true);
        }
        return $values;
    }
    
    /**
     * Save/load data from/to file.
     */
    public function persist($filename, $value = null, $reset = false) {
        if ($reset) {
            if (!isset($value)) {
                unlink($filename);
            }
        }
        elseif (file_exists($filename)) {
            return unserialize(file_get_contents($filename));
        }
        
        if (isset($value)) {
            $data = $value instanceof \Closure ? $value() : $value;
            file_put_contents($filename, serialize($data));
            return $data;
        }
    }
    
    /**
     * Calls registered hooks passing the 2 parameters.
     * 
     * If a hook returns an array, it is merged into $out parameter.
     *
     * @param string|array $name One or more hooks to execute
     * @param mixed $in
     * @param mixed $out
     * @return mixed $out 
     */
    function &hook($name, $in = null, &$out = array()) {
        if (empty($this->_hooks[$name])) {
            return $out;
        }
        
        if (is_string($name)) {
            $hooks =& $this->_hooks[$name];
        } else {
            $hooks = array();
            foreach ($name as $name_) {
                foreach ($this->_hooks[$name_] as $weight => $hook) {
                    $hooks[$weight][] = $hook;
                }
            }
        }
        ksort($hooks); // hooks may have different weights
        if (!isset($in)) {
            $in =& $out;
        }
        foreach ($hooks as &$same_weight_hooks) {
            foreach ($same_weight_hooks as $callback) {
                $result =& $callback($in, $out, $this);
                if (isset($result)) {
                    $out =& $result;
                    return $out;
                }
            }
        }
        return $out;
    }
    
}
