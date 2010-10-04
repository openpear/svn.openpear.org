<?php
/**
 * PHP_Obfuscator_CommandLineOptions class
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

require_once 'Console/CommandLine.php';

/**
 * PHP_Obfuscator_CommandLineOptions class
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/PHP_Obfuscator
 */
class PHP_Obfuscator_CommandLineOptions
{

    /**
     * constructor
     *
     * @param  array    $argv an array of command-line options
     * @return void
     * @access public
     */
    public function __construct(array $argv) {
        $parser = new Console_CommandLine(array(
            'description' => 'obfuscate php script.',
            'version'     => '0.1.0'
        ));

        $parser->addOption('verbose', array(
            'long_name'   => '--verbose',
            'action'      => 'StoreTrue',
            'description' => 'turn on verbose output'
        ));

        $parser->addOption('filter', array(
            'multiple' => true,
            'short_name'  => '-t',
            'long_name'   => '--filter',
            'action'      => 'StoreArray',
            'description' => 'a list of filters and parameters. specify \'XXXX:args\' if use PHP_Obfuscator_Filter_XXXXFilter',
        ));

        $parser->addOption('encoder', array(
            'multiple' => true,
            'short_name'  => '-e',
            'long_name'   => '--encoder',
            'action'      => 'StoreArray',
            'description' => 'encoder names. specify \'XXXX\' if use PHP_Obfuscator_Encoder_XXXXEncoder',
        ));

        $parser->addOption('file', array(
            'description' => 'the script file name to obfuscate. if not assigned, use stdin.',
            'short_name'  => '-f',
            'long_name'   => '--file',
        ));

        try {
            $result = $parser->parse();
            $this->options = $result->options;
            $this->filename = $result->args;
        } catch (Exception $exc) {
            $parser->displayError($exc->getMessage());
        }
    }

    /**
     * Return a file name
     *
     * @return string   Return assigned file name or null
     * @access public
     */
    public function getFileName() {
        return isset($this->filename['file']) ? $this->filename['file'] : null;
    }

    /**
     * Return if verbose or not
     *
     * @return boolean  Return if verbose or not
     * @access public
     */
    public function isVerbose() {
        return $this->options['verbose'] === true;
    }

    /**
     * Return an array of assigned filters
     *
     * @return array  Return an array of assigned filters
     * @access public
     */
    public function getFilters() {
        $filters = array();
        if (!is_null($this->options['filter'])) {
            foreach ($this->options['filter'] as $filter) {
                $param = explode(':', $filter);
                if (count($param) === 0) {
                    break;
                }
                $filter = array();
                $filter['name'] = array_shift($param);
                $filter['args'] = $param;
                $filters[] = $filter;
            }
        }
        return $filters;
    }

    /**
     * Return an array of assigned encoders
     *
     * @return array  Return an array of assigned encoders
     * @access public
     */
    public function getEncoders() {
        return is_null($this->options['encoder']) ? array() : $this->options['encoder'];
    }
}
