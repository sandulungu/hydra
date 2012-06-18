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

namespace Hydra\Dump;

/**
 * Utility class giving a HTML representation of an object/array graph. 
 */
class Html {
    
    static function dump($data, $indent = 0) {
        $em = $strong = false;
        $numeric = true;
        $prefix = str_repeat("\t", $indent);
        
        if (is_object($data)) {
            if (!method_exists($data, '__toString')) {
                $data = get_object_vars($data);
                $numeric = false;
            }
        }
        
        if (is_array($data)) {
            $lines = array();
            if ($numeric) {
                $numeric = array_keys($data) === range(0, count($data) - 1);
            }
            foreach ($data as $key => $value) {
                $lines[] = ($numeric ? '' : "<strong>$key</strong>: ") . self::dump($value, $indent + 1);
            }
            $data = implode("</li>\n$prefix<li>", $lines);
            if ($data) {
                $data = "<li>$data</li>";
            }
            $data = $numeric ? "\n$prefix<ol>$data</ol>\n" : "\n$prefix<ul>$data</ul>\n";
        }
        else if (is_bool($data)) {
            $data = $data ? 'TRUE' : 'FALSE';
            $em = true;
        }
        elseif ($data === null) {
            $data = 'NULL';
            $em = true;
        }
        elseif (is_int($data)) {
            $em = $strong = true;
        }
        elseif (is_float($data)) {
            $strong = true;
        }
        elseif (is_resource($data)) {
            $data = 'RESOURCE';
            $em = true;
        }
        elseif (is_string($data)) {
            if (preg_match('`^https?://[^\s]+$`', $data)) {
                $data = htmlentities($data);
                $data = "<a href='$data'>$data</a>";
            }
        }
        
        if ($em) {
            $data = "<em>$data</em>";
        }
        if ($strong) {
            $data = "<strong>$data</strong>";
        }
        return (string)$data;
    }
    
}
