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

namespace Hydra\Dump;

use Hydra\Utils;

/**
 * Utility class giving a HTML representation of an object/array graph. 
 */
class Html {
    
    static $_objects = array();
    
    static function table($data) {
        $headers = array();
        $rows = is_object($data) ? get_object_vars($data) : $data;
        if (!$rows || !is_array($rows)) {
            return self::dump($data);
        }
        
        foreach ($rows as &$row_ref) {
            if (is_object($row_ref)) {
                $row_ref = get_object_vars($row_ref);
            }
            elseif (!is_array($row_ref)) {
                $row_ref = array($row_ref);
            }
            $headers += $row_ref;
        }
        $numeric_cols = Utils::arrayIsNumeric($headers);
        $headers = array_keys($headers);
        $numeric_rows = Utils::arrayIsNumeric($rows);
        
        $table = array();
        if (!$numeric_cols) {
            if (!$numeric_rows) {
                $table[$key][-1] = '<th></th>';
            }
            foreach ($headers as $header) {
                $value = self::dump($header, 0);
                $table[$header][-1] = "<th>$value</th>";
            }
        }
        foreach ($rows as $key => $row) {
            if (!$numeric_rows) {
                $value = self::dump($key, 0);
                $table[$key][-1] = "<th>$value</th>";
            }
            foreach ($headers as $header) {
                 $value = array_key_exists($header, $row) ? self::dump($row[$header], 0) : '';
                 $table[$key][$header] = "<td>$value</td>";
            }
        }
        $table = implode("</tr>\n<tr>", array_map(function($row) { return implode("", $row); }, $table));
        return "<table class='table table-striped table-bordered dump'><tr>$table</tr></table>";
    }
    
    static function dump($data, $indent = null) {
        $em = false;
        $type = 'unknown';
        $numeric = true;
        $prefix = $indent > 0 ? str_repeat("\t", $indent) : '';
        
        if (is_object($data)) {
            $type = 'object';
            if ($data instanceof \Closure) {
                $data = 'Closure';
                $em = true;
            }
            elseif (in_array($data, self::$_objects)) {
                $data = 'Object recursion';
                $em = true;
            }
            elseif (!method_exists($data, '__toString')) {
                self::$_objects[] = $data;
                $data = get_object_vars($data);
                $numeric = false;
                if (!$data) {
                    $data = 'Object';
                    $em = true;
                }
            }
        }
        
        if (is_array($data)) {
            if ($type == 'unknown') {
                $type = 'array';
            }
            $lines = array();
            if ($numeric) {
                $numeric = Utils::arrayIsNumeric($data);
            }
            foreach ($data as $key => $value) {
                $lines[] = ($numeric ? '' : "<strong>$key</strong>: ") . self::dump($value, $indent + 1);
            }
            $data = implode("</li>\n$prefix<li>", $lines);
            if ($data) {
                $data = "<li>$data</li>";
            }
            if (!$data) {
                $data = "Empty array";
                $em = true;
            } else {
                $data = $numeric ? "\n$prefix<ol>$data</ol>\n" : "\n$prefix<ul>$data</ul>\n";
            }
        }
        else if (is_bool($data)) {
            $type = 'bool';
            $data = $data ? 'TRUE' : 'FALSE';
            $em = true;
        }
        elseif ($data === null) {
            $type = 'null';
            $data = 'NULL';
            $em = true;
        }
        elseif (is_int($data)) {
            $type = 'int';
            $em = true;
        }
        elseif (is_float($data)) {
            $type = 'float';
            $em = true;
        }
        elseif (is_resource($data)) {
            $type = 'resource';
            $data = 'RESOURCE';
            $em = true;
        }
        elseif (is_string($data)) {
            if ($type == 'unknown') {
                $type = 'string';
            }
            $data = Utils::htmlentities($data);
            if (preg_match('`^https?://[^\s]+$`', $data)) {
                $data = "<a href='$data'>$data</a>";
            }
        }
        
        if ($em) {
            $data = "<em class='dump-$type'>$data</em>";
        } else {
            $data = "<span class='dump-$type'>$data</span>";
        }
        if ($indent === null) {
            self::$_objects = array();
            $data = "<div class='dump'>$data</div>";
        }
        return (string)$data;
    }
    
}
