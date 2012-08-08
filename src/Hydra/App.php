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
 * @property Config            $config
 * @property array             $session
 * @property Cookie            $cookie
 * @property User              $user
 * @property \Twig_Environment $twig
 * @property AnnotationsReader $annotationsReader
 *
 * @property array $mimetypes
 *
 * @property string $security__salt
 * @property string $security__token
 *
 * @property \MongoDB $mongodb
 * @property \PDO     $pdo
 *
 * @property \Monolog\Logger $monolog__main
 * @property \Monolog\Logger $monolog__debug
 *
 * @method bool   run()
 * @method mixed  cache($name, $value = null, $ttl = 0, $reset = null)
 * @method mixed  config__persist($name, $value, $reset)
 * @method string translate($string, array $params = array(), array $options = array())
 * 
 * @method mixed dump__json($data)
 * @method mixed dump__html($data)
 * 
 * @method mixed normalize__int($data)
 * @method mixed normalize__float($data)
 * @method mixed normalize__bool($data)
 * @method mixed normalize__array($data)
 * @method mixed normalize__string($data)
 * @method mixed normalize__safeString($data)
 */
class App extends Container {
    
    /**
     * A special value for $app->persist(..., $reset) that allows interactive updating of persistent data. 
     */
    const PERSIST_RESET_USE_CALLBACK = 'callback';
    
    protected $_hooks = array(), $_hookFiles = array();
    var $requests = array();
    
    /**
     * @var \Hydra\Request
     */
    var $mainRequest;
    
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
    static function getInstance(ClassLoader $autoloader = null, array $core_config = array()) {
        if (!self::$_instance) {
            if (!$autoloader) {
                throw new \LogicException('The application requires an instance of Composer\Autoload\ClassLoader.');
            }
            self::$_instance = new static($autoloader, $core_config);
        }
        return self::$_instance;
    }
    
    function __construct(ClassLoader $autoloader, array $core_config) {
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
     * Quick binding of routes.
     * 
     * @param string $http_method
     * @param string $pattern
     * @param \Closure $callback
     * @param array $requirements
     * @param array $defaults
     * @return \Hydra\App
     */
    function route($http_method, $pattern, \Closure $callback, $requirements = array(), $defaults = array()) {
        if (!in_array($http_method, array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new \DomainException("Http method should be one of: 'GET', 'POST', 'PUT', 'DELETE', but '$http_method' given in $name annotation.");
        }
        $this->routes[] = array($http_method, $pattern, $callback, $requirements, $defaults);
        return $this;
    }
    
    /**
     * Form API entry point.
     * 
     * @return \Hydra\Form 
     */
    function form(array $options, Form $form = null) {
        if (!$form) {
            $options += array('type' => 'form');
        }
        $this->hook('form.init', $options, $form);

        // Load form options. This is required for $form->type guessers to work properly.
        $form->options;

        return $form;
    }

    /**
     * Renders a sub-request.
     */
    function render($path, $method = 'GET', array $query = array(), $data = null) {
        return $this->dispatch($path, $method, $query, $data)->render();
    }
    
    /**
     * Despatches a sub-request.
     * 
     * @return Response
     */
    function dispatch($path, $method = 'GET', $query = array(), $data = null, $server = array()) {
        $request = new Request\HttpRequest($this, $path, $method, $query, $data, $server);
        if (!$this->mainRequest) {
            $request->isMain = true;
            $this->mainRequest = $request;
        }
        
        // Use a dispatcher stack to allow cross-request access
        $this->requests[] = $request;
        $response = $request->dispatch();
        array_pop($this->requests);
        
        return $response;
    }
    
    /**
     * Save/load data from/to file.
     */
    function persist($filename, $value = null, $reset = false) {
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
                $data = $value($data, $this);
            } else {
                $data = $value instanceof \Closure ? $value($this) : $value;
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
    function &hook($name, $in = null, &$out = null, $merge_result = false) {
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
    function &infoHook($name, $in = null) {
        $out = array();
        return $this->hook($name, $in, $out, true);
    }
    
    
    //==========================================================================
    
    
    /**
     * Serves main request.
     * 
     * @return bool True if a response was sent succesfully.
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

        $path = '';
        if (isset($_SERVER['PATH_INFO'])) {
            $path = ltrim($_SERVER['PATH_INFO'], '/');
        }
        
        $response = $this->dispatch($path, $_SERVER['REQUEST_METHOD'], $_GET, $data, $_SERVER);
        if ($response) {
            $response->send();
            return true;
        }
        return false;
    }

    /**
     * Translation fallback used by applications that don't need interface translation.
     */
    protected function fallback__translate($string, array $params = array(), array $options = array()) {
        return Utils::formatString($string, $params);
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
    protected function fallback__config__persist($name = null, $value = null, $unset = false) {
        static $values;
        $filename = "config";
        
        if (!isset($values)) {
            $values = (array)$this->persist($filename);
        }
        
        if ($name) {
            if ($unset) {
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
    protected function service__config() {
        $app = $this;
        return new Config($app, $app->fallback__cache('app.config', function() use ($app) {
            return $app->infoHook('app.config');
        }));
    }
    
    /**
     * Cookies provider.
     */
    protected function service__cookie() {
        return new Cookie($this);
    }

    /**
     * (Current) User provider.
     */
    protected function service__user() {
        $session_key = $this->config->session['userKey'];
        if (!isset($this->session[$session_key])) {
            $this->session[$session_key] = $this->hook('app.user', $this);
        }
        return $this->session[$session_key];
    }

    /**
     * Session provider.
     */
    function &service__session() {
        $this->hook('app.session.start', $this->config->session['name']);
        return $_SESSION;
    }

    protected function service__annotationsReader() {
        return new AnnotationsReader();
    }

    /**
     * Generates security salt, then reuses it.
     */
    protected function service__security__salt() {
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
    protected function service__security__token() {
        $session_key = $this->config->security['token.sessionKey'];
        if (isset($this->session[$session_key])) {
            return $this->session[$session_key];
        }
        $token = $this->hash(mt_rand(), false, 1);
        $this->session[$session_key] = $token;
        return $token;
    }

    /**
     * Default MongoDB service.
     */
    protected function service__mongodb() {
        $mongo = new \Mongo($this->config['mongodb']['uri']);
        return $mongodb = $mongo->selectDB($this->config['mongodb']['dbname']);
    }

    /**
     * Default PDO service.
     */
    protected function service__pdo() {
        $pdo = new \PDO($this->config['pdo']['dsn'], $this->config['pdo']['username'], $this->config['pdo']['password']);
        if ($this->config['pdo']['setNamesUtf8']) {
            $pdo->exec('SET NAMES utf8');
        }
        return $pdo;
    }

    /**
     * Gets a list of registered routes.
     */
    protected function service__routes() {
        $routes = $this->infoHook('app.routes', $this);

        // No home route defined? Show an information page.
        $routes[] = array('GET', '/', function() {
            return 'Please define your routes in <strong>web/index.php</strong> or create a controller in <strong>app/src/App/Controller/</strong> folder.';
        });

        return $routes;
    }
    
    // Register Twig service.
    protected function service__twig() {
        $loader = new \Twig_Loader_Filesystem($this->config['twig.dirs']);
        $options = $this->config['twig.options'];
        if (!is_dir($options['cache'])) {
            mkdir($options['cache']);
        }
        return new \Twig_Environment($loader, $options);
    }
}
