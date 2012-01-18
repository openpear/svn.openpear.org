<?php
/**
 * XMLTreeNode class
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
 * XMLTreeNode class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class XMLTreeNode
{
    /**
     * this chunk type is NULL
     */
    const RES_NULL_TYPE = 0x0000;

    /**
     * this chunk type is string pool
     */
    const RES_STRING_POOL_TYPE = 0x0001;

    /**
     * this chunk type is resource table
     */
    const RES_TABLE_TYPE = 0x0002;

    /**
     * this chunk type is XML
     */
    const RES_XML_TYPE = 0x0003;

    /**
     * first chunk
     */
    const RES_XML_FIRST_CHUNK_TYPE = 0x0100;

    /**
     * start namespace
     */
    const RES_XML_START_NAMESPACE_TYPE= 0x0100;

    /**
     * end namespace
     */
    const RES_XML_END_NAMESPACE_TYPE = 0x0101;

    /**
     * start element
     */
    const RES_XML_START_ELEMENT_TYPE = 0x0102;

    /**
     * end element
     */
    const RES_XML_END_ELEMENT_TYPE = 0x0103;

    /**
     * cdata
     */
    const RES_XML_CDATA_TYPE = 0x0104;

    /**
     * last chunk
     */
    const RES_XML_LAST_CHUNK_TYPE = 0x017f;

    /**
     * resource map
     */
    const RES_XML_RESOURCE_MAP_TYPE = 0x0180;

    /**
     * package
     */
    const RES_TABLE_PACKAGE_TYPE = 0x0200;

    /**
     * type
     */
    const RES_TABLE_TYPE_TYPE = 0x0201;

    /**
     * type spec
     */
    const RES_TABLE_TYPE_SPEC_TYPE = 0x0202;

    /**
     * Type identifier for this chunk
     * @var integer
     * @access private
     */
    private $type;

    /**
     * Size of the chunk header
     * @var integer
     * @access private
     */
    private $header_size;

    /**
     * Size of the chunk header including namespace chunk
     * @var integer
     * @access private
     */
    private $size;

    /**
     * Line number in original source file at which this element appeared.
     * @var integer
     * @access private
     */
    private $lox;

    /**
     * Optional XML comment that was associated with this element; -1 if none.
     * @var integer
     * @access private
     */
    private $comment_no;

    /**
     * constructor
     *
     * @param binary $binary binary string from AndroidManifest.xml
     * @param integer $offset starting offset for this chunk
     * @return void
     * @access public
     */
    public function __construct($binary, $offset) {
        $this->type = BinaryUtil::unpackLE(substr($binary, $offset + 0));
        $this->header_size = BinaryUtil::unpackLE(substr($binary, $offset + 2));
        $this->size = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
        $this->lox = BinaryUtil::unpackLE32(substr($binary, $offset + 8));
        $this->comment_no = BinaryUtil::unpackLE32(substr($binary, $offset + 12));
    }

    /**
     * return type identifier for this chunk
     *
     * @return integer Type identifier for this chunk
     * @access public
     */
    public function getType() {
        return $this->type;
    }

    /**
     * return size of the chunk header
     *
     * @return integer Size of the chunk header
     * @access public
     */
    public function getHeaderSize() {
        return $this->header_size;
    }

    /**
     * return size of the chunk header including namespace chunk
     *
     * @return integer Size of the chunk header including namespace chunk
     * @access public
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * return line number in original source file at which this element appeared.
     *
     * @return integer Line number in original source file at which this element appeared.
     * @access public
     */
    public function getLineOfXML() {
        return $this->lox;
    }

    /**
     * return optional XML comment that was associated with this element; -1 if none.
     *
     * @return integer Optional XML comment that was associated with this element; -1 if none.
     * @access public
     */
    public function getCommentIndex() {
        return $this->comment_no;
    }
}
