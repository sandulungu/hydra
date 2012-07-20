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
 * Wrapper class for configuration options.
 * 
 * Performs transparent saving on value set/unset. 
 * 
 * @property string $i18n__defaultLang
 * @property string $vendor__webDirs
 * 
 * @property string $form__twigViews
 * @property array form__coreTypes
 *
 * @property array  $app
 * @property array  $cookie
 * @property array  $session
 * @property array  $security
 * @property array  $response
 * 
 * @property array  $assets__js
 * @property array  $assets__css
 * 
 * @property array  $mongodb
 * @property array  $pdo
 * 
 * @property array  $twig__options
 * @property array  $twig__dirs
 */
class Config extends ArrayObject {
    
    /**
     * @var App 
     */
    protected $_app;
    
    protected $_cachedData;

    function __construct($owner, array $cachedData = array()) {
        $this->_app = $owner;
        $this->_cachedData = $cachedData;
        $this->_data = array_merge(
            $this->_cachedData,
            $this->_app->config__persist()
        );
    }
    
    function __set($name, $value) {
        $this->_data = array_merge(
            $this->_cachedData,
            $this->_app->config__persist($name, $value)
        );
    }

    function __unset($name) {
        $this->_data = array_merge(
            $this->_cachedData,
            $this->_app->config__persist($name, null, true)
        );
    }

}
