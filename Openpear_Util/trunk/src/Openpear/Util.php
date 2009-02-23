<?php
/**
  * Openpear_Util
  *
  * PHP version 5
  *
  * LICENSE: This source file is subject to version 3.0 of the PHP license
  * that is available through the world-wide-web at the following URI:
  * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
  * the PHP License and are unable to obtain it through the web, please
  * send a note to license@php.net so we can mail you a copy immediately.
  *
  * @category   Utils
  * @package    Openpear_Util
  * @author     KOYAMA Tetsuji <koyama@hoge.org>
  * @copyright  2008-2009 KOYAMA Tetsuji
  * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
  * @version    svn: $Id$
  * @link       http://openpear.org/package/Openpear_Util
  * @since      File available since Release 0.1
  */

/*
 *  Error codes
 */
define('OPENPEAR_UTIL_ERROR_EMPTY_NAME',       1);
define('OPENPEAR_UTIL_ERROR_SOURCE_NOT_FOUND', 2);

/*
 * Own Exception
 */
class Openpear_Util_Exception extends Exception {
}

/*
 *  Openpear_Util class
 */
class Openpear_Util {

    public static function import($name) {
        if (empty($name)) {
            throw new Openpear_Util_Exception('empty name',
                                              OPENPEAR_UTIL_ERROR_EMPTY_NAME);
        }
        
        $dir = dirname(__FILE__) .
            DIRECTORY_SEPARATOR .
            'Util' .
            DIRECTORY_SEPARATOR;
        $path = $dir . $name . '.php';

        if (!file_exists($path)) {
            throw new Openpear_Util_Exception('source file not found: '. $path,
                                       OPENPEAR_UTIL_ERROR_SOURCE_NOT_FOUND);
        }
        include($path);
    }
}

?>
