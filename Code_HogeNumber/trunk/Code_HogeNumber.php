<?php
/**
  * Code_HogeNumber
  *
  * PHP version 5
  *
  * LICENSE: This source file is subject to version 3.0 of the PHP license
  * that is available through the world-wide-web at the following URI:
  * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
  * the PHP License and are unable to obtain it through the web, please
  * send a note to license@php.net so we can mail you a copy immediately.
  *
  * @category   Coding
  * @package    Code_HogeNumber
  * @author     Shuhei Suzuki <szk.shi@gmail.com>
  * @copyright  2010 Shuhei Suzuki
  * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
  * @link       http://openpear.org/package/Code_HogeNumber
  * @since      File available since Release 0.1
  */
class Code_HogeNumber {
  static private $hash_key ='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ._';

  static public function encode( $int ) {
    $str  = '';
    $hash = self::$hash_key;
    $cnt  = strlen($hash);
    $int  = (int)$int;
    do {
      $str  = $hash[$int % $cnt].$str;
      $int  = (int)($int / $cnt);
    } while( $int );
    return $str;
  }
  static function decode( $string ) {
    $cnt  = strlen($string);
    $hash = self::$hash_key;
    $kcnt = strlen($hash);
    $i = $int = 0;
    while( $i<$cnt ) {
      $j    = strpos($hash,$string[$i]);
      $i++;
      $int += pow( $kcnt,$cnt-$i ) * $j;
    }
    return $int;
  }
  static public function hash( $hash ) {
    self::$hash_key = $hash;
  }
}

