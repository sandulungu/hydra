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
     * Creates a new $class($args[0], $args[1], ...) instance.
     */
    static function createClassInstance($class, $args) {
        $reflection = new \ReflectionClass($class);
        return $reflection->newInstanceArgs($args);
    }

    /**
     * A secure PBKDF2 hashing function.
     * 
     * @see http://en.wikipedia.org/wiki/PBKDF2
     */
    static function hash($in, $raw_output = false, $cycles = 4096, $length = 256, $algo = 'sha1') {
        $app = App::getInstance();
        
        $hash = '';
        $i = 0;
        if (!$raw_output) {
            $length /= 2;
        }
        while (strlen($hash) < $length) {
            $F = $U = hash_hmac($algo, $in, $app->security__salt . $i, true);
            for ($j = 1; $j < $cycles; $j++) {
                $U = hash_hmac($algo, $in, $U, true);
                $F = $F ^ $U;
            }
            $hash .= $U;
        }
        $hash = substr($hash, 0, $length);
        return $raw_output ? $hash : bin2hex($hash);
    }

}