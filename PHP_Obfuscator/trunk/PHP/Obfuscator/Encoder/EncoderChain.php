<?php
/**
 * PHP_Obfuscator_Encoder_EncoderChain class
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

require_once 'PHP/Obfuscator/Encoder/Encoder.php';

/**
 * PHP_Obfuscator_Encoder_EncoderChain class
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/PHP_Obfuscator
 */
class PHP_Obfuscator_Encoder_EncoderChain
{

    /**
     * the encoder chain
     * @var    array
     * @access private
     */
    private $chain = array();

    /**
     * add a encoder
     *
     * @param  object PHP_Obfuscator_Encoder_Encoder $encoder   a encoder
     * @return object PHP_Obfuscator_Encoder_EncoderChain       Return the PHP_Obfuscator_Encoder_EncoderChain object
     * @access public
     */
    public function add(PHP_Obfuscator_Encoder_Encoder $filter) {
        $this->chain[] = $filter;
        return $this;
    }

    /**
     * Return encoded previous code
     *
     * @param  string $code a code for reading encoded code fragment from obfuscated code
     * @return string Return encoded code
     * @access public
     */
    public function encode($code = 'fread($__f, %d)') {
        foreach ($this->chain as $filter) {
            $filter->setStr($code);
            $code = $filter->encode();
        }
        return $code;
    }

    /**
     * Return encoded previous code
     *
     * @param  string $code a code for reading encoded code fragment from obfuscated code
     * @return string Return encoded code
     * @access public
     */
    public function decode($code = 'fread($__f, %d)') {
        $chain = $this->chain;
        krsort($chain);
        foreach ($chain as $filter) {
            $code = sprintf($filter->decode(), $code);
        }
        return "{$code}";
    }
}
