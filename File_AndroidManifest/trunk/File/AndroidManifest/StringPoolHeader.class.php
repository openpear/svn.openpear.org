<?php
/**
 * StringPoolHeader class
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
require_once 'File/AndroidManifest/StringPool.class.php';

/**
 * StringPoolHeader
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class StringPoolHeader
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
     * Type identifier for this chunk
     * @var integer
     * @access private
     */
    private $type;

    /**
     * Size of the header
     * @var integer
     * @access private
     */
    private $header_size;

    /**
     * Size of the string pool
     * @var integer
     * @access private
     */
    private $size;

    /**
     * Number of strings in this pool
     * @var integer
     * @access private
     */
    private $strings_size;

    /**
     * Number of style span arrays in the pool
     * @var integer
     * @access private
     */
    private $styles_size;

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
     * Index from header of the style data.
     * @var integer
     * @access private
     */
    private $styles_offset;

    /**
     * StringPool object
     * @var object
     * @access private
     */
    private $string_pool;

    /**
     * constructor
     *
     * @param binary $binary binary string from AndroidManifest.xml
     * @param integer $offset starting offset for this chunk
     * @return void
     * @access public
     */
    public function __construct($binary, $offset = 8) {
        $this->type = BinaryUtil::unpackLE(substr($binary, $offset + 0));
        $this->header_size = BinaryUtil::unpackLE(substr($binary, $offset + 2));
        $this->size = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
        $this->strings_size = BinaryUtil::unpackLE32(substr($binary, $offset + 8));
        $this->styles_size = BinaryUtil::unpackLE32(substr($binary, $offset + 12));
        $this->flag = BinaryUtil::unpackLE32(substr($binary, $offset + 16));
        $this->strings_offset = BinaryUtil::unpackLE32(substr($binary, $offset + 20));
        $this->styles_offset = BinaryUtil::unpackLE32(substr($binary, $offset + 24));

        $this->string_pool = new StringPool($binary);
    }

    /**
     * return type identifier for this chunk
     *
     * @return integer Type identifier for this chunk
     * @access public
     * @see http://bit.ly/xfjXFd
     */
    public function getType() {
        return $this->type;
    }

    /**
     * return size of the chunk header
     *
     * @return integer Size of the chunk header. always 8.
     * @access public
     */
    public function getHeaderSize() {
        return $this->size;
    }

    /**
     * return size of the string pool
     *
     * @return integer size of the string pool
     * @access public
     */
    public function getSize() {
        return $this->size;
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
     * return number of style span arrays in the pool
     *
     * @return integer number of style span arrays in the pool
     * @access public
     */
    public function getStyleSize() {
        return $this->styles_size;
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
     * return index from header of the string data
     *
     * @return index from header of the string data
     * @access public
     */
    public function getStringsOffset() {
        return $this->strings_offset;
    }

    /**
     * return index from header of the style data
     *
     * @return integer index from header of the style data
     * @access public
     */
    public function getStylesOffset() {
        return $this->styles_offset;
    }

    /**
     * return StringPool object
     *
     * @return object StringPool StringPool object
     * @access public
     */
    public function getStringPool() {
        return $this->string_pool;
    }
}
