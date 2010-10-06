<?php
/**
 * PHP_Obfuscator class
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy
 * the PHP License and are unable to obtain it through the web,
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   CVS: $Id$
 * @link      http://openpear.org/package/PHP_Obfuscator
 * @see       References to other sections (if any)...
 */

require_once 'PHP/Obfuscator/Encoder/EncoderChain.php';
require_once 'PHP/Obfuscator/Filter/FilterChain.php';
require_once 'PHP/Obfuscator/Filter/ExpireRestrictionFilter.php';

/**
 * PHP_Obfuscator class
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/PHP_Obfuscator
 * @see       References to other sections (if any)...
 */
class PHP_Obfuscator
{

    /**
     * execute obfuscation
     *
     * @param  string    $file_name assigned file name to obfuscate
     * @param  array     $encoders  a list of encoders
     * @param  array     $filters   a list of filters
     * @param  boolean   $verbose   use verbose mode or not. default is false
     * @param  string    $comment   comment string. default is null
     * @return void
     * @access public
     * @throws Exception throw if errors occur
     */
    public function execute($file_name, array $encoders, array $filters, $verbose = false, $comment = null) {
        $contents = null;
        if (is_null($file_name) || $file_name === '') {
            $fp = fopen('php://stdin', 'rb');
            if (!$fp) {
                throw new Exception("failed to open STDIN");
            }
            while (!feof($fp)) {
                $contents .= fgets($fp, 4096);
            }
            fclose($fp);
        } else {
            if (!is_readable($file_name)) {
                throw new Exception("file {$file_name} is not readable");
            }
            $contents = file_get_contents($file_name);
        }

        $encoder_chain = new PHP_Obfuscator_Encoder_EncoderChain();
        foreach ($encoders as $encoder) {
            $class_name = "PHP_Obfuscator_Encoder_{$encoder}Encoder";
            $this->loadClass($class_name);
            $encoder_chain->add(new $class_name());
        }

        $filter_chain = new PHP_Obfuscator_Filter_FilterChain('?>' . $contents, $encoder_chain, $comment);
        foreach ($filters as $filter) {
            $class_name = "PHP_Obfuscator_Filter_" . $filter['name'] . "Filter";
            $this->loadClass($class_name);
            $obj = new $class_name();
            $obj->setArgs($filter['args']);
            $filter_chain->add($obj);
        }
        echo $filter_chain->process();
    }

    private function loadClass($class_name) {
        $class_file = str_replace('_', '/', $class_name) . '.php';
        if (!$this->isIncludeable($class_file)) {
            throw new Exception("class file {$class_file} is not readable");
        }
        include_once $class_file;
        if (!class_exists($class_name)) {
            throw new Exception("class {$class_name} does not exist");
        }
    }

    private function isIncludeable($file) {
        if (!defined('PATH_SEPARATOR')) {
            define('PATH_SEPARATOR', strtolower(substr(PHP_OS, 0, 3)) == 'win' ? ';' : ':');
        }
        foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . $file) &&
                  is_readable($path . DIRECTORY_SEPARATOR . $file)) {
                return true;
            }
        }
        return false;
    }

}
