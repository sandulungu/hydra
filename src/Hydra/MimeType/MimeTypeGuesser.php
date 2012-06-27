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

namespace Hydra\MimeType;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser as SymfonyGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\FileBinaryMimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;

class MimeTypeGuesser extends SymfonyGuesser {
    
    function __construct() {
        $this->register(new ExtensionMimeTypeGuesser());
        
        if (FileBinaryMimeTypeGuesser::isSupported()) {
            $this->register(new FileBinaryMimeTypeGuesser());
        }

        if (FileinfoMimeTypeGuesser::isSupported()) {
            $this->register(new FileinfoMimeTypeGuesser());
        }
    }

}
