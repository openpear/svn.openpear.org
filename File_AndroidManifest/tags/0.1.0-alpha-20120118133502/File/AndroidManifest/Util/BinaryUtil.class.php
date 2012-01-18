<?php
/**
 * BinaryUtil class
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the New BSD license that is
 * available through the world-wide-web at the following URI:
 * http://www.opensource.org/licenses/bsd-license.php. If you did not receive
 * a copy of the New BSD License and are unable to obtain it through the web,
 * please send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   0.1.0
 * @link      http://openpear.org/package/File_AndroidManifest
 */

/**
 * BinaryUtil class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class BinaryUtil
{

    /**
     * unpack binary string with little endian (16bit)
     *
     * @param binary  $data   binary data
     * @param integer $length a length of unpacked array. default is 1.
     * @return mixed  unpacked array. if $length = 1, return into string
     * @access public
     * @static
     */
    public static function unpackLE($data, $length = 1) {
        return self::unpack('v*', $data, $length);
    }

    /**
     * unpack binary string with little endian (32bit)
     *
     * @param binary  $data   binary data
     * @param integer $length a length of unpacked array. default is 1.
     * @return mixed  unpacked array. if $length = 1, return into string
     * @access public
     * @static
     */
    public static function unpackLE32($data, $length = 1) {
        return self::unpack('V*', $data, $length);
    }

    /**
     * unpack binary string with given format
     *
     * @param binary  $data   binary data
     * @param integer $length a length of unpacked array. default is 1.
     * @return mixed  unpacked array. if $length = 1, return into string
     * @access public
     * @static
     */
    public static function unpack($format, $data, $length = 1) {
        $unpacked_data = unpack($format, $data);
        if ($length < 1) {
            return null;
        } else if ($length === 1) {
            return (isset($unpacked_data[1]) ? $unpacked_data[1] : null);
        } else {
            return array_slice($unpacked_data, 0, $length);
        }
    }
}
