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

/**
 * Phar compiler
 */

$autoloader = require_once 'vendor/autoload.php';

$compiler = new Hydra\PharCompiler();
$compiler->compile();
