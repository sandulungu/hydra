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

namespace Hydra\Form;

use Hydra\App;

/**
 * 
 * @property array|Traversable $choices
 */
class SelectField extends Field {
    
    function __construct(App $app, array $options = array()) {
        $this->messages['invalid_choices'] = 'Invalid choice(s): $choices.';
        
        // this option should be specified explicitly
        $this->defaultOptions['choices'] = null;
        
        parent::__construct($app, $options);
    }

    function service__choices() {
        $choices =& $this->options['choices'];
        
        if ($choices instanceof \Closure) {
            $choices = $choices($this);
        }
        if (is_string($choices)) {
            $method = "form.choices.$choices";
            $choices = $this->$method($this);
        }
        
        if (!$choices) {
            throw new \LogicException("For 'list' or 'select' form fields 'choices' option is required and should countain at least an item.");
        }
        if (!is_array($choices) && !$choices instanceof \Traversable) {
            throw new \LogicException("Form choices should be an array or Traversable class.");
        }
        return $choices;
    }
    
}
