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

use Symfony\Component\HttpFoundation\File\Mimetype\MimeTypeGuesser as SymfonyGuesser;
use Symfony\Component\HttpFoundation\File\Mimetype\FileBinaryMimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\Mimetype\FileinfoMimeTypeGuesser;

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
