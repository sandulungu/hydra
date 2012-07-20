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

// Add validators.
$hooks['form.validators'][1000][] = function (Form $form, &$validators) {
    if ($validators === null) {
        // TODO...
    }
};

// @validate annotation parser.
$methods['annotation.validate'][0] = function(AnnotationsReader $reader, $annotation) {
    // TODO...
};
