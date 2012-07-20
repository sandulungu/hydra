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

// Set form options.
$hooks['form.init'][-1000][] = function (&$options) {
    // TODO...
};

// @form annotation parser.
$methods['annotation.form'][0] = function(AnnotationsReader $reader, $annotation) {
    // TODO...
};
