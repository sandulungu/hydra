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

namespace Hydra;

class Utils {
    
    /**
     * Formats a string as a slug
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
    
    /**
     * Creates a new $class($args[0], $args[1], ...) instance.
     */
    static function createClassInstance($classname, $args) {
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
            $iterator->append(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST
                )
            );
        }
        return $iterator;
    }

}