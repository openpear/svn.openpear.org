<?php
/**
 * XMLHeader class
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
 * XMLHeader class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class XMLHeader
{
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
     * Total size of this chunk
     * @var integer
     * @access private
     */
    private $size;

    /**
     * attribute table
     * @var array
     * @access private
     */
    private $attributes;

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
        $this->attributes = array();
        for ($of = 8; $of < $this->size; $of += 4) {
            $this->attributes[] = sprintf('0x%08X', BinaryUtil::unpackLE32(substr($binary, $offset + $of, 4)));
        }
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
     * @return integer Size of the chunk header. always 8.
     * @access public
     */
    public function getHeaderSize() {
        return $this->header_size;
    }

    /**
     * return total size of this chunk
     *
     * @return integer total size of this chunk
     * @access public
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * return array of unique attributes
     *
     * @return array  array of unique attributes
     * @access public
     */
    public function getAttributes() {
        return $this->attributes;
    }
}
