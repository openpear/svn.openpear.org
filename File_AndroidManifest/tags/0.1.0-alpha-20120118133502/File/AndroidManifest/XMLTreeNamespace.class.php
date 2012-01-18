<?php
/**
 * XMLTreeNamespace class
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
 * XMLTreeNamespace class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class XMLTreeNamespace
{

    /**
     * Index of the prefix of the namespace.
     * @var string
     * @access private
     */
    private $prefix;

    /**
     * The URI of the namespace.
     * @var integer
     * @access private
     */
    private $uri;

    /**
     * constructor
     *
     * @param binary $binary binary string from AndroidManifest.xml
     * @param integer $offset starting offset for this chunk
     * @return void
     * @access public
     */
    public function __construct($binary, $offset) {
        $this->prefix = BinaryUtil::unpackLE32(substr($binary, $offset + 0));
        $this->uri = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
    }

    /**
     * return Index of the prefix of the namespace.
     *
     * @return string Index of the prefix of the namespace.
     * @access public
     */
    public function getPrefixIndex() {
        return $this->prefix;
    }

    /**
     * return Index of the URI of the namespace.
     *
     * @return integer Index of the URI of the namespace.
     * @access public
     */
    public function getUriIndex() {
        return $this->uri;
    }

    /**
     * return size of the chunk header
     *
     * @return integer Size of the chunk header. always 8.
     * @access public
     */
    public function getHeaderSize() {
        return 8;
    }
}
