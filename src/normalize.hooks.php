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

$methods['app.normalize.int'][0] = function(App $app, $data) {
    return (int)$data;
};

$methods['app.normalize.float'][0] = function(App $app, $data) {
    return (float)$data;
};

$methods['app.normalize.bool'][0] = function(App $app, $data) {
    return (bool)$data;
};

$methods['app.normalize.array'][0] = function(App $app, $data) {
    if (is_array($data)) {
        return $data;
    }
    if ($data && is_string($data)) {
        return explode(',', $data);
    }
    return $data ? array($data) : array();
};

$methods['app.normalize.string'][0] = function(App $app, $data) {
    return (string)$data;
};

// Prevent XSS attacks by invalidating possible HTML tags. 
// This is the default param filter.
$methods['app.normalize.safeString'][0] = function(App $app, $data) {
    return str_replace('<', '', $data);
};
