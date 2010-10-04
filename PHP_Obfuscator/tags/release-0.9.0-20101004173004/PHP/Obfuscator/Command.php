<?php
/**
 * PHP_Obfuscator_Command class
 *
 * This class is called by 'php-obfuscator' command.
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
 * @version   SVN: $Id$
 * @link      http://openpear.org/package/PHP_Obfuscator
 */

require_once 'PHP/Obfuscator.php';
require_once 'PHP/Obfuscator/CommandLineOptions.php';

/**
 * PHP_Obfuscator_Command class
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/PHP_Obfuscator
 */
class PHP_Obfuscator_Command
{
    /**
     * the entrypoint of PHP_Obfuscator
     *
     * This static method is called by 'php-obfuscator' command.
     *
     * @param  array    $argv an array of command args
     * @return void
     * @access public
     * @static
     */
    public static function main(array $argv) {
        $options = new PHP_Obfuscator_CommandLineOptions($argv);
        $obfuscator = new PHP_Obfuscator();
        $obfuscator->execute(
            $options->getFileName(),
            $options->getEncoders(),
            $options->getFilters(),
            $options->isVerbose());
    }
}
