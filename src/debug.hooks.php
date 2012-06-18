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

$start = microtime(true);

// Set default configuration options.
$hooks['response.output'][1000][] = function (&$config, &$dummy, App $app) use ($start) {
    $app->monolog__debug->info("Generation time: " . (microtime(true) - $start));
    $app->monolog__debug->info("Total time: " . (microtime(true) - $_SERVER['REQUEST_TIME']));
};
