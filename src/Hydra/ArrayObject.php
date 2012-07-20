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

// We have to use this kind of ugly hack because sometimes PHP really suxx.
// The reason here is to allow modification of ArrayObject's inner array inside foreach using references.
$error_reporting = error_reporting();
error_reporting($error_reporting & E_DEPRECATED ^ $error_reporting);
require_once __DIR__ . '/ArrayObject.call_time_pass_reference.php';
error_reporting($error_reporting);
