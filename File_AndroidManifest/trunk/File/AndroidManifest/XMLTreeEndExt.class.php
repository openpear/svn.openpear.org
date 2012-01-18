<?php
/**
 * XMLTreeEndExt class
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
 * XMLTreeEndExt class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class XMLTreeEndExt
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
    private $name_no;

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
        $this->name_no = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
    }

    /**
     * return index of the namespace of this element
     *
     * @return integer index of the namespace of this element
     * @access public
     */
    public function getNamespaceIndex() {
        return $this->namespace_no;
    }

    /**
     * index of string name of this node
     *
     * @return integer index of string name of this node
     * @access public
     */
    public function getNameIndex() {
        return $this->name_no;
    }

    /**
     * return this header size
     *
     * @return integer this header size
     * @access public
     */
    public function getHeaderSize() {
        return 8;
    }
}
