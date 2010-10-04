<?php
/**
 * PHP_Obfuscator_Filter_ExpireRestrictionFilter class
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

require_once 'PHP/Obfuscator/Filter/Filter.php';

/**
 * PHP_Obfuscator_Filter_ExpireRestrictionFilter class
 *
 * @category  PHP
 * @package   PHP_Obfuscator
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2010 Hideyuki Shimooka
 * @license   http://www.php.net/license/3_01.txt The PHP License, version 3.01
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/PHP_Obfuscator
 */
class PHP_Obfuscator_Filter_ExpireRestrictionFilter implements PHP_Obfuscator_Filter_Filter
{

    /**
     * Expire date/time in string
     * @var    string
     * @access private
     */
    private $expire = null;

    /**
     * Set arguments for filtering
     *
     * @param  array     $args Arguments for filtering
     * @return void
     * @access public
     * @throws Exception throw if errors occur
     */
    public function setArgs(array $args) {
        if (!isset($args[0])) {
            throw new Exception("parameter for ExpireRestrictionFilter is not assigned");
        }
        $expire = null;
        try {
            $expire = new DateTime($args[0]);
        } catch (Exception $e) {
            throw new Exception("parameter for ExpireRestrictionFilter is invalid : " . $args[0]);
        }
        $expire->setTimezone(new DateTimeZone('UTC'));
        $this->expire = $expire;
    }

    /**
     * Return filtering code
     *
     * @return string Return filtering code
     * @access public
     */
    public function getCode() {
        return 'if (gmdate(\'YmdHis\') > \'' . $this->expire->format('YmdHis') . '\') { return; } ';
    }
}
