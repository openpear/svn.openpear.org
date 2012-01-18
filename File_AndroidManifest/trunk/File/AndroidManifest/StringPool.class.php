<?php
/**
 * StringPool class
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

require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';

/**
 * StringPool class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class StringPool
{
    /**
     * the string index is sorted by the string values (based on strcmp16()).
     */
    const SORTED_FLAG = 31;     // 1<<0

    /**
     * String pool is encoded in UTF-8
     */
    const UTF8_FLAG = 323536;   // 1<<8

    /**
     * Size of the chunk
     * @var integer
     * @access private
     */
    private $header_size;

    /**
     * Number of strings in this pool
     * @var integer
     * @access private
     */
    private $strings_size;

    /**
     * Flag
     * @var integer
     * @access private
     */
    private $flag;

    /**
     * Index from header of the string data.
     * @var integer
     * @access private
     */
    private $strings_offset;

    /**
     * array of offset to unique strings
     * @var array
     * @access private
     */
    private $strings;

    /**
     * Raw array of unique strings
     * @var array
     * @access private
     */
    private $pool_strings;

    /**
     * constructor
     *
     * @param binary $binary binary string from AndroidManifest.xml
     * @param integer $offset starting offset for this chunk
     * @return void
     * @access public
     */
    public function __construct($binary, $offset = 8) {
        $this->header_size = BinaryUtil::unpackLE(substr($binary, $offset + 2));   // 28
        $this->strings_size = BinaryUtil::unpackLE32(substr($binary, $offset + 8));
        $this->flag = BinaryUtil::unpackLE32(substr($binary, $offset + 16));
        $this->strings_offset = BinaryUtil::unpackLE32(substr($binary, $offset + 20));

        $this->strings = BinaryUtil::unpackLE32(
                                    substr($binary, $offset + $this->header_size, 4 * $this->strings_size),
                                    $this->strings_size);

        $this->pool_strings = array();
        foreach ($this->strings as $index => $pool_offset) {
            $offset = 8 + $this->strings_offset + $pool_offset;
            $size = BinaryUtil::unpackLE(substr($binary, $offset));
            $this->pool_strings[$index] = null;
            foreach ((array)BinaryUtil::unpackLE(substr($binary, $offset + 2, $size * 2), $size) as $char) {
                $this->pool_strings[$index] .= chr($char);
            }
        }
    }

    /**
     * return number of strings in this pool
     *
     * @return integer number of strings in this pool
     * @access public
     */
    public function getStringsSize() {
        return $this->strings_size;
    }

    /**
     * return flag
     *
     * @return integer flag value
     * @access public
     */
    public function getFlag() {
        return $this->flag;
    }

    /**
     * return array of unique strings
     *
     * @return array array of unique strings
     * @access public
     */
    public function getPoolStrings() {
        return $this->pool_strings;
    }

    /**
     * return pooled string
     *
     * @param integer $index index of array
     * @return array pooled string
     * @access public
     */
    public function getPoolString($index) {
        return isset($this->pool_strings[$index]) ? $this->pool_strings[$index] : null;
    }
}
