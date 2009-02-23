<?php
/**
  * array_val
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

if (!function_exists('array_val')) {
    function array_val(&$data, $key, $default = null) {
        if (!is_array($data)) {
            return $default;
        }
        return isset($data[$key])? $data[$key]: $default;
    }
}

?>
