<?php
/**
 * AndroidManifest class
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

require_once 'File/AndroidManifest/ChunkHeader.class.php';
require_once 'File/AndroidManifest/StringPoolHeader.class.php';
require_once 'File/AndroidManifest/XMLHeader.class.php';
require_once 'File/AndroidManifest/XMLTreeNode.class.php';
require_once 'File/AndroidManifest/XMLTreeNamespace.class.php';
require_once 'File/AndroidManifest/XMLTreeExt.class.php';
require_once 'File/AndroidManifest/XMLTreeAttribute.class.php';
require_once 'File/AndroidManifest/XMLTreeAttributeValue.class.php';
require_once 'File/AndroidManifest/XMLTreeEndExt.class.php';

/**
 * AndroidManifest class
 *
 * @category  File Formats
 * @package   File_AndroidManifest
 * @author    Hideyuki Shimooka <shimooka@doyouphp.jp>
 * @copyright 2012 Hideyuki Shimooka
 * @license   http://tinyurl.com/new-bsd New BSD License
 * @version   Release: @package_version@
 * @link      http://openpear.org/package/File_AndroidManifest
 */
class AndroidManifest
{
    /**
     * parsed xml string
     * @var string
     * @access private
     */
    private $xml_string;

    /**
     * constructor
     *
     * @param binary $binary binary string of AndroidManifest.xml
     * @return void
     * @access public
     */
    public function __construct($binary) {
        $this->xml_string = $this->parseManifest($binary);
    }

    /**
     * parse binary string from AndroidManifest.xml and build into string
     *
     * @param binary $binary binary data of AndroidManifest.xml
     * @return string a XML string of AndroidManifest.xml
     * @access private
     */
    private function parseManifest($binary) {
        $xml = null;
        $output_ns = false;

        $chunk_header = new ChunkHeader($binary);
        $string_pool_header = new StringPoolHeader($binary);
        $string_pool = $string_pool_header->getStringPool();

        $offset = $chunk_header->getHeaderSize() + $string_pool_header->getSize();

        $xml_header = new XMLHeader($binary, $offset);

        $offset += $xml_header->getSize();
        $xml_tree_node = new XMLTreeNode($binary, $offset);

        $offset += $xml_tree_node->getHeaderSize();
        $xml_tree_ns = new XMLTreeNamespace($binary, $offset);
        $namespaces[$string_pool->getPoolString($xml_tree_ns->getUriIndex())] = $string_pool->getPoolString($xml_tree_ns->getPrefixIndex());

        $offset += $xml_tree_ns->getHeaderSize();
        for (;;) {
            $node = new XMLTreeNode($binary, $offset);
            switch ($node->getType()) {
            case XMLTreeNode::RES_XML_START_ELEMENT_TYPE:
                $offset += $node->getHeaderSize();
                $ext = new XMLTreeExt($binary, $offset);
                $offset += $ext->getHeaderSize();

                if ((int)$ext->getNamespaceIndex() === (int)0xFFFFFFFF) {
                    $xml .=
                        sprintf(
                            '<%s ',
                            $string_pool->getPoolString($ext->getElementIndex()));
                } else {
                    $xml .=
                        sprintf(
                            '<%s:%s ',
                            $string_pool->getPoolString($ext->getNamespaceIndex()),
                            $string_pool->getPoolString($ext->getElementIndex()));
                }
                if (!$output_ns && !is_null($string_pool->getPoolString($xml_tree_ns->getPrefixIndex()))) {
                    $output_ns = true;
                    $xml .=
                        sprintf(
                            'xmlns:%s="%s" ',
                            $string_pool->getPoolString($xml_tree_ns->getPrefixIndex()),
                            $string_pool->getPoolString($xml_tree_ns->getUriIndex()));
                }

                for ($i = 0; $i < $ext->getAttributeCount(); $i++) {
                    $attribute = new XMLTreeAttribute($binary, $offset);
                    $offset += $attribute->getHeaderSize();
                    $attribute_value = new XMLTreeAttributeValue($binary, $offset);
                    $offset += $attribute_value->getHeaderSize();

                    $name = $string_pool->getPoolString($attribute->getNameIndex());
                    if ($attribute->getNamespaceIndex() === $xml_tree_ns->getUriIndex()) {
                        $name = sprintf('%s:%s', $string_pool->getPoolString($xml_tree_ns->getPrefixIndex()), $name);
                    }
                    $value = null;
                    if ((int)$attribute->getValueIndex() === (int)0xFFFFFFFF) {
                        $value = self::formatAttribute($attribute_value->getType(), $attribute_value->getValue());
                    } else {
                        $value = $string_pool->getPoolString($attribute->getValueIndex());
                    }
                    $xml .=
                        sprintf('%s="%s" ', $name, $value);
                }
                $xml .= '>';
                break;

            case XMLTreeNode::RES_XML_END_ELEMENT_TYPE:
                $offset += $node->getHeaderSize();
                $end_ext = new XMLTreeEndExt($binary, $offset);
                $offset += $end_ext->getHeaderSize();
                if ((int)$end_ext->getNamespaceIndex() === (int)0xFFFFFFFF) {
                    $xml .=
                        sprintf('</%s>', $string_pool->getPoolString($end_ext->getNameIndex()));
                } else {
                    $xml .=
                        sprintf(
                            '</%s:%s>',
                            $string_pool->getPoolString($end_ext->getNamespaceIndex()),
                            $string_pool->getPoolString($end_ext->getNameIndex()));
                }
                break;
            case XMLTreeNode::RES_XML_END_NAMESPACE_TYPE:
                $offset += $node->getHeaderSize();
                $end_ns = new XMLTreeNamespace($binary, $offset);
                $offset += $end_ns->getHeaderSize();
                break;
            }
            if ($offset >= $chunk_header->getFileSize()) {
                break;
            }
        }
        return $xml;
    }

    /**
     * format an attribute value by its type
     *
     * @param integer $type  an attribute value
     * @param integer $value an attribute type
     * @return string formatted value
     * @access private
     * @see http://bit.ly/AxtqRz
     */
    private function formatAttribute($type, $value) {
        switch ($type) {
        case XMLTreeAttributeValue::TYPE_NULL:
            return '';
            break;
        case XMLTreeAttributeValue::TYPE_REFERENCE:
            return sprintf('@0x%08X', $value);
            break;
        case XMLTreeAttributeValue::TYPE_INT_DEC:
            return sprintf('%d', $value);
            break;
        case XMLTreeAttributeValue::TYPE_INT_HEX:
            return sprintf('0x%08X', $value);
            break;
        case XMLTreeAttributeValue::TYPE_INT_BOOLEAN:
            return sprintf('%s', ($value === 0) ? 'false' : 'true');
            break;
        default:
            return sprintf('0x%08X', $value);
            break;
        }
    }

    /**
     * return parsed xml
     *
     * @return string parsed xml
     * @access public
     */
    public function getXML() {
        return $this->xml_string;
    }

    /**
     * return parsed xml into SimpleXMLElement
     *
     * @return SimpleXMLElement parsed xml into SimpleXMLElement
     * @access public
     */
    public function getSimpleXMLElement() {
        return simplexml_load_string($this->xml_string);
    }
}
