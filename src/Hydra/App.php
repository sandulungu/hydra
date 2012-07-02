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

use Composer\Autoload\ClassLoader;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

/**
 * Application singleton and main service container.
 * 
 * @property Config             $config
 * @property array              $session
 * @property Cookie             $cookie
 * @property \Twig_Environment  $twig
 * @property \PDO               $pdo
 * @property \MongoDB           $mongodb
 * @property AnnotationsReader  $annotationsReader
 * @property string             $security__salt
 * @property string             $security__token
 * 
 * @property \Hydra\MimeType\MimeTypeGuesser          $mimetype__guesser
 * @property \Hydra\MimeType\ExtensionMimeTypeGuesser $mimetype__extensionGuesser
 * 
 * @property \Monolog\Logger    $monolog__main
 * @property \Monolog\Logger    $monolog__debug
 * 
 * @method \Net\Curl\Curl      curl()
 * @method \Net\Curl\CurlMulti curl__multi()
 * 
 * @method bool     run()
 * @method mixed    cache($name, $value = null, $ttl = 0, $reset = null)
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
    
    const PERSIST_RESET_USE_CALLBACK = 'callback';
    
    protected $_hooks = array(), $_hookFiles = array();
    var $requests = array(), $routes__defined = false;
    
    /**
     * @var \Hydra\Core
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
    static function getInstance(ClassLoader $autoloader = NULL, array $core_config = array()) {
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
        $this->core->exception_handler->app = $this;
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
        $this->autoloader->add('App\\', "{$this->core->app_dir}/src");
        
        $core = $this->core;
        $this->_hookFiles = $this->fallback__cache('core.hook_files', function() use ($core) {
            $files = array();
            foreach (Utils::listFilesRecursive(array(__DIR__ . '/..', "$core->app_dir/plugins", "$core->app_dir/src")) as $file) {
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
        
        // Configure dynamic services
        $this->hook('app.init', $app);
    }
    
    /**
     * Renders a sub-request.
     */
    public function render($path, $method = 'GET', array $query = array(), $data = null) {
        return $this->dispatch($path, $method, $query, $data)->render();
    }
    
    /**
     * Despatches a sub-request.
     * 
     * @return Response
     */
    public function dispatch($path, $method = 'GET', $query = array(), $data = null) {
        $request = new Request\HttpRequest($this, $path, $method, $query, $data);
        $this->requests[] = $request;
        $response = $request->dispatch();
        return $response;
    }
    
    /**
     * Save/load data from/to file.
     */
    public function persist($filename, $value = null, $reset = false) {
        $filename = "{$this->core->data_dir}/$filename";
        if ($reset) {
            if (!isset($value)) {
                unlink($filename);
            }
        }
        elseif (file_exists($filename)) {
            return unserialize(file_get_contents($filename));
        }
        
        if (isset($value)) {
            if ($reset === self::PERSIST_RESET_USE_CALLBACK && $value instanceof \Closure) {
                $data = file_exists($filename) ? unserialize(file_get_contents($filename)) : null;
                $data = $value($data);
            } else {
                $data = $value instanceof \Closure ? $value() : $value;
            }
            file_put_contents($filename, serialize($data));
            return $data;
        }
    }
    
    /**
     * PBKDF2 key derivation function as defined by RSA's PKCS #5: 
     *   https://www.ietf.org/rfc/rfc2898.txt
     *   http://en.wikipedia.org/wiki/PBKDF2
     * 
     * One hash call should take ~20..100ms CPU time on a modern computer.
     * This should be enough to make good (6 characters or more, upper and 
     * lower case letters and numbers) password almost impossible to guess.
     */
    function hash($in, $raw_output = false, $cycles = 4096, $length = 128, $algo = 'sha1') {
        if (!$raw_output) {
            $length /= 2;
        }
        
        $hash = '';
        for ($i = 0; strlen($hash) < $length; $i++) {
            $F = $U = hash_hmac($algo, $in, $this->security__salt . $i, true);
            for ($j = 1; $j < $cycles; $j++) {
                $U = hash_hmac($algo, $in, $U, true);
                $F = $F ^ $U;
            }
            $hash .= $F;
        }
        
        $hash = substr($hash, 0, $length);
        return $raw_output ? $hash : bin2hex($hash);
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
    function &hook($name, $in = null, &$out = array(), $merge_result = false) {
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
                if ($merge_result && is_array($result)) {
                    $out = array_merge($out, $result);
                }
                elseif (!$merge_result && isset($result)) {
                    $out =& $result;
                    return $out;
                }
            }
        }
        return $out;
    }
    
    /**
     * Alias for $this->hook($name, $in, array(), $merge_result = true)
     */
    function infoHook($name, $in = null) {
        $out = array();
        return $this->hook($name, $in, $out, true);
    }
    
    
    //==========================================================================
    
    
    /**
     * Serves main request.
     */
    protected function fallback__run() {
        if (!$_POST) {
            $data = file_get_contents('php://input');
            if ($data) {
                $data = json_decode($data);
            } else {
                $data = null;
            }
        } else {
            $data =& $_POST;
        }

        $path = ltrim(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : @$_GET['path'], '/');
        
        $response = $this->dispatch($path, $_SERVER['REQUEST_METHOD'], $_GET, $data);
        if ($response) {
            $response->send();
            return true;
        }
        return false;
    }

    /**
     * Pluggable caching engine.
     * 
     * @param string         $name  Cache key.
     * @param \Closure|mixed $value
     * @param int            $ttl   Time to live in seconds.
     * @param bool           $reset If not set, the cache will be always resetted in debug mode.
     * @return mixed
     */
    protected function fallback__cache($name, $value = null, $ttl = 0, $reset = null) {
        $filename = "cache/$name";
        if ($reset === null && $this->core->debug) {
            $reset = true;
        }
        if ($ttl && !$reset && file_exists($filename)) {
            $reset = filemtime($filename) + $ttl <= time();
        }
        return $this->persist($filename, $value, $reset);
    }

    /**
     * Default configuration persister.
     * 
     * @see \Hydra\Config
     */
    protected function fallback__config__persist($name = null, $value = null, $reset = false) {
        static $values;
        $filename = "config";
        
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
    
    
    //==========================================================================
    
    
    /**
     * Custom runtime configuration support. Equivalent to Drupal's variables.
     */
    public function service__config() {
        $app = $this;
        return new Config($app, $app->fallback__cache('app.config', function() use ($app) {
            return $app->infoHook('app.config');
        }));
    }
    
    /**
     * Cookies provider.
     */
    public function service__cookie() {
        return new Cookie($this);
    }

    /**
     * Session provider.
     */
    public function &service__session() {
        session_name($this->config['session']['name']);
        session_start();
        return $_SESSION;
    }

    public function service__annotationsReader() {
        return new AnnotationsReader();
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
     * Generates CSRF protection token and caches it in a session cookie on the client-side.
     */
    public function service__security__token() {
        $token_cookie = $this->config->security['token.cookie'];
        if (isset($this->cookie[$token_cookie])) {
            return $this->cookie[$token_cookie];
        }
        $token = $this->hash(mt_rand(), false, 1);
        $this->cookie->set($token_cookie, $token, 0);
        return $token;
    }

    /**
     * Default mime-type guesser.
     */
    protected function service__mimetype__guesser() {
        return new MimeType\MimeTypeGuesser;
    }
    
    /**
     * Extension-based mime-type guesser.
     * 
     * Used for setting default content type for main responses.
     */
    protected function service__mimetype__extensionGuesser() {
        return new MimeType\ExtensionMimeTypeGuesser;
    }
    
}
