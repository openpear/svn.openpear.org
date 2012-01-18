<?php
/**
 * XMLTreeAttributeValue class
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
 * XMLTreeAttributeValue class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class XMLTreeAttributeValue
{
    /**
     * Contains no data.
     */
    const TYPE_NULL = 0x00;

    /**
     * The 'data' holds a ResTable_ref, a reference to another resource
     * table entry.
     */
    const TYPE_REFERENCE = 0x01;

    /**
     * The 'data' holds an attribute resource identifier.
     */
    const TYPE_ATTRIBUTE = 0x02;

    /**
     * The 'data' holds an index into the containing resource table's
     * global value string pool.
     */
    const TYPE_STRING = 0x03;

    /**
     * The 'data' holds a single-precision floating point number.
     */
    const TYPE_FLOAT = 0x04;

    /**
     * The 'data' holds a complex number encoding a dimension value,
     * such as "100in".
     */
    const TYPE_DIMENSION = 0x05;

    /**
     * The 'data' holds a complex number encoding a fraction of a
     * container.
     */
    const TYPE_FRACTION = 0x06;

    /**
     * Beginning of integer flavors...
     */
    const TYPE_FIRST_INT = 0x10;

    /**
     * The 'data' is a raw integer value of the form n..n.
     */
    const TYPE_INT_DEC = 0x10;

    /**
     * The 'data' is a raw integer value of the form 0xn..n.
     */
    const TYPE_INT_HEX = 0x11;

    /**
     * The 'data' is either 0 or 1, for input "false" or "true" respectively.
     */
    const TYPE_INT_BOOLEAN = 0x12;

    /**
     * Beginning of color integer flavors...
     */
    const TYPE_FIRST_COLOR_INT = 0x1c;

    /**
     * The 'data' is a raw integer value of the form #aarrggbb.
     */
    const TYPE_INT_COLOR_ARGB8 = 0x1c;

    /**
     * The 'data' is a raw integer value of the form #rrggbb.
     */
    const TYPE_INT_COLOR_RGB8 = 0x1d;

    /**
     * The 'data' is a raw integer value of the form #argb.
     */
    const TYPE_INT_COLOR_ARGB4 = 0x1e;

    /**
     * The 'data' is a raw integer value of the form #rgb.
     */
    const TYPE_INT_COLOR_RGB4 = 0x1f;

    /**
     * ...end of integer flavors.
     */
    const TYPE_LAST_COLOR_INT = 0x1f;

    /**
     * ...end of integer flavors.
     */
    const TYPE_LAST_INT = 0x1;

    /**
     * Number of bytes in this structure
     * @var integer
     * @access private
     */
    private $size;

    /**
     * Type of the data value
     * @var integer
     * @access private
     */
    private $type;

    /**
     * The data for this item
     * @var integer
     * @access private
     */
    private $value;

    /**
     * constructor
     *
     * @param binary $binary binary string from AndroidManifest.xml
     * @param integer $offset starting offset for this chunk
     * @return void
     * @access public
     */
    public function __construct($binary, $offset) {
        $this->size = BinaryUtil::unpackLE(substr($binary, $offset + 0));
        $padding = BinaryUtil::unpack('C', substr($binary, $offset + 2, 1));
        $this->type = BinaryUtil::unpack('C', substr($binary, $offset + 3, 1));
        $this->value = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
    }

    /**
     * return number of bytes in this structure
     *
     * @return integer Number of bytes in this structure
     * @access public
     */
    public function getHeaderSize() {
        return $this->size;
    }

    /**
     * return type of the data value
     *
     * @return integer Type of the data value.
     * @access public
     */
    public function getType() {
        return $this->type;
    }

    /**
     * return the data for this item
     *
     * @return integer The data for this item
     * @access public
     */
    public function getValue() {
        return $this->value;
    }
}
