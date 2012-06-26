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

$methods['app.dump.json'][0] = function(App $app, $data) {
    if (defined('JSON_PRETTY_PRINT') && $app->core->debug) {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
    return json_encode($data);
};

$methods['app.dump.html'][0] = function(App $app, $data) {
    return Dump\Html::dump($data);
};
