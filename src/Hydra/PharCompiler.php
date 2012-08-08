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

use Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles the Hydra framework.
 */
class PharCompiler {

    /**
     * Compiles the Hydra source code into one single Phar file.
     *
     * @param string $pharFile Name of the output Phar file
     */
    public function compile($pharFile = 'hydra.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'hydra.phar');
        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->name('*.twig')
            ->notName('PharCompiler.php')
            ->notName('ide_helper.php')
            ->exclude('Tests')
            ->in('src')
            ->in('views')
            ->in('../../twig/twig/lib')
            ->in('../../monolog/monolog/src')
            ->in('../../symfony/http-kernel/Symfony/Component/HttpKernel/Exception')
            ->in('../../symfony/http-foundation/Symfony/Component/HttpFoundation/File/MimeType')
            ->in('../../symfony/http-foundation/Symfony/Component/HttpFoundation/File/Exception')
        ;
        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addFile($phar, new \SplFileInfo('../../symfony/http-foundation/Symfony/Component/HttpFoundation/HeaderBag.php'));
        $this->addFile($phar, new \SplFileInfo('../../symfony/http-foundation/Symfony/Component/HttpFoundation/Response.php'));
        $this->addFile($phar, new \SplFileInfo('../../symfony/http-foundation/Symfony/Component/HttpFoundation/ResponseHeaderBag.php'));
        $this->addFile($phar, new \SplFileInfo('../../symfony/http-kernel/Symfony/Component/HttpKernel/Debug/ErrorHandler.php'));
        $this->addFile($phar, new \SplFileInfo('../../symfony/http-kernel/Symfony/Component/HttpKernel/Debug/ExceptionHandler.php'));
        
        $this->addFile($phar, new \SplFileInfo('../../autoload.php'));
        $this->addFile($phar, new \SplFileInfo('../../composer/ClassLoader.php'));
        $this->addFile($phar, new \SplFileInfo('../../composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo('../../composer/autoload_classmap.php'));

        $phar->setStub($this->getStub());
        $phar->stopBuffering();
//        $phar->compressFiles(\Phar::GZ);
    }

    protected function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(dirname(dirname(__DIR__)))).DIRECTORY_SEPARATOR, '', $file->getRealPath());
        $content = file_get_contents($file);
        if ($strip) {
            $content = self::stripWhitespace($content);
        }
        $phar->addFromString($path, $content);
    }

    protected function getStub()
    {
        return <<<'EOF'
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

Phar::mapPhar('hydra.phar');
return require_once 'phar://hydra.phar/autoload.php';

__HALT_COMPILER();
EOF;
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * Based on Kernel::stripComments(), but keeps line numbers intact.
     *
     * @param string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    static public function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }
}
