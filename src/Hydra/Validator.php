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

    var $messages = array();

    function __construct(Form $form, array $options = array()) {
        $this->_form = $form;
        foreach ($options as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name =& $value;
            }
            else {
                throw new \LogicException("Unsupported Validator option: $name.");
            }
        }
        
    }

    function isValid($data) {
        return $this->validate($data);
    }
    
    abstract function validate(&$data);
    
}
