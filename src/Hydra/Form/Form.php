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

use Hydra\Request;

/**
 * Base class for forms.
 */
class Form extends Field {
    
    var $method = 'POST',
        $submitButton = 'Submit';

    function bindRequest(Request $request) {
        $data = strtoupper($this->method) == 'GET' ? $request->query : $request->data;
        if (!isset($data[$this->name])) {
            return false;
        }
        return $this->bind($data[$this->name]);
    }
    
}
