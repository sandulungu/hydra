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

namespace Hydra\Validator;

use Hydra\Validator;
use Hydra\Form;

/**
 * A simple wrapper for a validation callback.
 */
class CallbackValidator extends Validator {
    
    protected $_callback;

    function __construct(Form $form, \Closure $callback, array $options = array()) {
        $this->_callback = $callback;
        parent::__construct($form, $options);
    }

    function validate(&$data) {
        $callback = $this->_callback;
        return $callback($data, $this->_form, $this->options);
    }
    
}
