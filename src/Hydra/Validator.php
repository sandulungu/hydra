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
 * Base Validator class.
 */
abstract class Validator {
    
    /**
     * @var Form
     */
    protected $_form;

    // This one is public, as it is not fully known what exactly plugins will use options for.
    var $options;

    function __construct(Form $form, array $options = array()) {
        $this->_form = $form;
        $this->options = $options;
    }

    function isValid($data) {
        return $this->validate($data);
    }
    
    abstract function validate(&$data);
    
}
