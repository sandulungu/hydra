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

class Utils {
    
    static function uniqid() {
        return md5(uniqid(php_uname('n'), true));
    }
    
    static function formatString($string, array $params) {
        foreach ($params as $name => &$param) {
            if ($name{0} == '%') {
                $param = self::htmlentities($param);
            }
            elseif ($name{0} != '$') {
                throw new \InvalidArgumentException("Invalid param format '$param'. A parameter should start with either '$' or '%'.");
            }
        }
        return strtr($string, $params);
    }
    
    static function htmlentities($string) {
        return htmlentities($string, (defined('ENT_HTML5') ? ENT_HTML5 | ENT_SUBSTITUTE : 0) | ENT_QUOTES, 'UTF-8');
    }
    
    static function arrayIsNumeric(array &$array) {
        return array_keys($array) === range(0, count($array) - 1);
    }
    
    /**
     *
     * @param type $filename
     * @param bool $allow_empty_name If TRUE 
     * @return string File extension or NULL if the file has no extension
     */
    static function fileExt($filename) {
        $filename = str_replace('\\', '/', $filename);
        $ds_pos = strpos($filename, '/');
        $dot_pos = strrpos($filename, '.');
        if ($ds_pos === false) {
            $ds_pos = -1;
        }
        return $dot_pos !== false && $dot_pos > $ds_pos + 1 ? substr($filename, $dot_pos + 1) : null;
    }
    
    /**
     * Formats a UTF-8 string as a ASCII slug
     */
    static function sluggify($str, $preg_filter = "/[^a-z0-9]+/", $callback = 'strtolower', $default = 'untitled') {
        try {
            $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str); // translit non-ascii characters
        } catch (Exception $ex) {
        }
        if ($callback) {
            $str = $callback($str);
        }
        if ($preg_filter) {
            $str = preg_replace($preg_filter, '_', $str); // filter unallowed chars
        }
        $str = trim($str, '_ ');
        return $str ? $str : $default;
    }
    
    static function humanize($string) {
        return mb_convert_case(str_replace('_', ' ', $string), MB_CASE_TITLE);
    }
    
    /**
     * Creates a new $class($args[0], $args[1], ...) instance.
     */
    static function createClassInstance($classname, array $args) {
        $reflection = new \ReflectionClass($classname);
        return $reflection->newInstanceArgs($args);
    }

    /**
     * Gets a recursive directory iterator
     * 
     * @param  array $dirs List of folders to iterate
     * @return \SplFileInfo Iterator
     */
    static function listFilesRecursive($dirs) {
        if (!is_array($dirs)) {
            $dirs = array($dirs);
        }
        $iterator = new \AppendIterator();
        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $iterator->append(
                    new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST
                    )
                );
            }
        }
        return $iterator;
    }

}