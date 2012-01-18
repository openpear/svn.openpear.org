<?php
/**
 * XMLTreeExt class
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
 * XMLTreeExt class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class XMLTreeExt
{
    /**
     * index of the namespace of this element.
     * @var integer
     * @access private
     */
    private $namespace_no;

    /**
     * index of string name of this node if it is an ELEMENT; the raw
     * character data if this is a CDATA node.
     * @var integer
     * @access private
     */
    private $element_no;

    /**
     * Byte offset from the start of this structure where the attributes start.
     * @var integer
     * @access private
     */
    private $attr_offset;

    /**
     * Size of the ResXMLTree_attribute structures that follow.
     * @var integer
     * @access private
     */
    private $attr_size;

    /**
     * Number of attributes associated with an ELEMENT
     * @var integer
     * @access private
     */
    private $attr_count;

    /**
     * Index (1-based) of the "id" attribute. 0 if none.
     * @var integer
     * @access private
     */
    private $id_no;

    /**
     * Index (1-based) of the "class" attribute. 0 if none.
     * @var integer
     * @access private
     */
    private $class_no;

    /**
     * Index (1-based) of the "style" attribute. 0 if none.
     * @var integer
     * @access private
     */
    private $style_no;

    /**
     * constructor
     *
     * @param binary $binary binary string from AndroidManifest.xml
     * @param integer $offset starting offset for this chunk
     * @return void
     * @access public
     */
    public function __construct($binary, $offset) {
        $this->namespace_no = BinaryUtil::unpackLE32(substr($binary, $offset + 0));
        $this->element_no = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
        $this->attr_offset = BinaryUtil::unpackLE(substr($binary, $offset + 8));
        $this->attr_size = BinaryUtil::unpackLE(substr($binary, $offset + 10));
        $this->attr_count = BinaryUtil::unpackLE(substr($binary, $offset + 12));
        $this->id_no = BinaryUtil::unpackLE(substr($binary, $offset + 14));
        $this->class_no = BinaryUtil::unpackLE(substr($binary, $offset + 16));
        $this->style_no = BinaryUtil::unpackLE(substr($binary, $offset + 18));
    }

    /**
     * return index of the namespace of this element.
     *
     * @return integer index of the namespace of this element.
     * @access public
     */
    public function getNamespaceIndex() {
        return $this->namespace_no;
    }

    /**
     * return index of string name of this node if it is an ELEMENT
     *
     * @return integer index of string name of this node if it is an ELEMENT
     * @access public
     */
    public function getElementIndex() {
        return $this->element_no;
    }

    /**
     * return byte offset from the start of this structure where the attributes start.
     *
     * @return integer Byte offset from the start of this structure where the attributes start.
     * @access public
     */
    public function getAttributeOffset() {
        return $this->attr_offset;
    }

    /**
     * return size of the ResXMLTree_attribute structures that follow.
     *
     * @return integer Size of the ResXMLTree_attribute structures that follow.
     * @access public
     */
    public function getAttributeSize() {
        return $this->attr_size;
    }

    /**
     * return number of attributes associated with an ELEMENT
     *
     * @return integer Number of attributes associated with an ELEMENT
     * @access public
     */
    public function getAttributeCount() {
        return $this->attr_count;
    }

    /**
     * return index (1-based) of the "id" attribute
     *
     * @return integer Index (1-based) of the "id" attribute. 0 if none.
     * @access public
     */
    public function getIdIndex() {
        return $this->id_no;
    }

    /**
     * return index (1-based) of the "class" attribute
     *
     * @return integer Index (1-based) of the "class" attribute. 0 if none.
     * @access public
     */
    public function getClassIndex() {
        return $this->class_no;
    }

    /**
     * return index (1-based) of the "style" attribute
     *
     * @return integer Index (1-based) of the "style" attribute. 0 if none.
     * @access public
     */
    public function getStyleIndex() {
        return $this->style_no;
    }

    /**
     * return size of the chunk header
     *
     * @return integer Size of the chunk header. always 20.
     * @access public
     */
    public function getHeaderSize() {
        return 20;
    }
}
