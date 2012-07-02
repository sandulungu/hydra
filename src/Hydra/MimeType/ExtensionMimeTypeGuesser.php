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

namespace Hydra\MimeType;

use Hydra\Utils;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser as SymfonyGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

class ExtensionMimeTypeGuesser extends SymfonyGuesser implements MimeTypeGuesserInterface {
    function guess($path) {
        $ext = Utils::fileExt($path, true);
        if ($ext == 'jpg' || $ext == 'jpe') {
            $ext = 'jpeg';
        }
        return array_search($ext, $this->defaultExtensions);
    }
}
