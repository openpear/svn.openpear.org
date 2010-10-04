<?php
/**
 * PHP_Obfuscator_Encoder_StrtrEncoder class
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

require_once 'PHP/Obfuscator/Encoder/AbstractEncoder.php';

/**
 * PHP_Obfuscator_Encoder_StrtrEncoder class
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/PHP_Obfuscator
 */
class PHP_Obfuscator_Encoder_StrtrEncoder extends PHP_Obfuscator_Encoder_AbstractEncoder
{

    /**
     * characters for replacing
     */
    const CHRS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    /**
     * replace key
     * @var    string
     * @access private
     */
    private $key = null;

    /**
     * constructor
     *
     * @param  array  $args Arguments for code encoding
     * @return void
     * @access public
     */
    public function __construct(array $args = array()) {
        parent::__construct($args);
        $this->key = str_shuffle(self::CHRS);
    }

    /**
     * Return encoded previous code
     *
     * @return string Return encoded code
     * @access public
     */
    public function encode() {
        return strtr($this->str, $this->key, self::CHRS);
    }

    /**
     * Return the code for decoding
     *
     * @return string The code for decoding in 'sprintf/printf' format
     * @access public
     */
    public function decode() {
        return 'strtr(%s, \'' . self::CHRS . '\', \'' . $this->key . '\')';
    }
}
