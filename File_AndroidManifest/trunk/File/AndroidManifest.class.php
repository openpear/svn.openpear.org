<?php
require_once 'File/AndroidManifest/ChunkHeader.class.php';
require_once 'File/AndroidManifest/StringPoolHeader.class.php';
require_once 'File/AndroidManifest/XMLHeader.class.php';
require_once 'File/AndroidManifest/XMLTreeNode.class.php';
require_once 'File/AndroidManifest/XMLTreeNamespace.class.php';
require_once 'File/AndroidManifest/XMLTreeExt.class.php';
require_once 'File/AndroidManifest/XMLTreeAttribute.class.php';
require_once 'File/AndroidManifest/XMLTreeAttributeValue.class.php';
require_once 'File/AndroidManifest/XMLTreeEndExt.class.php';

class AndroidManifest
{
    private $chunk_header;
    private $string_pool_header;
    private $xml_header;
    private $xml_tree_node;
    private $xml_tree_namespace;
    private $xml_tree_node2;
    private $xml_tree_ext;
    private $xml_tree_attribute;
    private $xml_tree_attr_value;

    private $xml_string;
    public function __construct($binary) {
        $output_namespace = false;

        $this->chunk_header = new ChunkHeader($binary);
        $this->string_pool_header = new StringPoolHeader($binary);
        $string_pool = $this->string_pool_header->getStringPool();

        $offset = $this->chunk_header->getHeaderSize() + $this->string_pool_header->getSize();

        $this->xml_header = new XMLHeader($binary, $offset);

        $offset += $this->xml_header->getSize();
        $this->xml_tree_node = new XMLTreeNode($binary, $offset);

        $offset += $this->xml_tree_node->getHeaderSize();
        $this->xml_tree_namespace = new XMLTreeNamespace($binary, $offset);
//var_dump($string_pool->getPoolString($this->xml_tree_namespace->getPrefixIndex()));
//var_dump($string_pool->getPoolString($this->xml_tree_namespace->getUriIndex()));
        $namespaces[$string_pool->getPoolString($this->xml_tree_namespace->getUriIndex())] = $string_pool->getPoolString($this->xml_tree_namespace->getPrefixIndex());

        $offset += $this->xml_tree_namespace->getHeaderSize();
        for (;;) {
            $node = new XMLTreeNode($binary, $offset);
//var_dump($node->getCommentIndex());
//var_dump($node->getLineOfXML());
            switch ($node->getType()) {
            case XMLTreeNode::RES_XML_START_ELEMENT_TYPE:
                $offset += $node->getHeaderSize();
                $ext = new XMLTreeExt($binary, $offset);
//var_dump($ext);
//var_dump($ext->getNamespaceIndex());
//var_dump($string_pool->getPoolString($ext->getNamespaceIndex()));
//var_dump($string_pool->getPoolString($ext->getElementIndex()));
//var_dump($string_pool->getPoolString($ext->getIdIndex()));
//var_dump($string_pool->getPoolString($ext->getClassIndex()));
//var_dump($string_pool->getPoolString($ext->getStyleIndex()));
                $offset += $ext->getHeaderSize();

                if ((int)$ext->getNamespaceIndex() === (int)0xFFFFFFFF) {
                    $this->xml_string .=
                        sprintf('<%s ', $string_pool->getPoolString($ext->getElementIndex()));
                } else {
                    $this->xml_string .=
                        sprintf(
                            '<%s:%s ',
                            $string_pool->getPoolString($ext->getNamespaceIndex()),
                            $string_pool->getPoolString($ext->getElementIndex()));
                }
                if (!$output_namespace && !is_null($string_pool->getPoolString($this->xml_tree_namespace->getPrefixIndex()))) {
                    $output_namespace = true;
                    $this->xml_string .=
                        sprintf(
                            'xmlns:%s="%s" ',
                            $string_pool->getPoolString($this->xml_tree_namespace->getPrefixIndex()),
                            $string_pool->getPoolString($this->xml_tree_namespace->getUriIndex()));
                }
//var_dump($string_pool->getPoolString($this->xml_tree_namespace->getPrefixIndex()));
//var_dump($string_pool->getPoolString($this->xml_tree_namespace->getUriIndex()));

                for ($i = 0; $i < $ext->getAttributeCount(); $i++) {
                    $attribute = new XMLTreeAttribute($binary, $offset);
//var_dump($attribute);
//var_dump($string_pool->getPoolString($attribute->getNamespaceIndex()));
//var_dump($string_pool->getPoolString($attribute->getNameIndex()));
//var_dump($attribute->getValueIndex());
//var_dump($string_pool->getPoolString($attribute->getValueIndex()));
                    $offset += $attribute->getHeaderSize();
                    $attribute_value = new XMLTreeAttributeValue($binary, $offset);
//var_dump($attribute_value);
                    $offset += $attribute_value->getHeaderSize();

                    $name = $string_pool->getPoolString($attribute->getNameIndex());
//var_dump($attribute->getNamespaceIndex());
//var_dump($this->xml_tree_namespace->getPrefixIndex());
//var_dump($this->xml_tree_namespace->getUriIndex());
                    if ($attribute->getNamespaceIndex() === $this->xml_tree_namespace->getUriIndex()) {
                        $name = sprintf('%s:%s', $string_pool->getPoolString($this->xml_tree_namespace->getPrefixIndex()), $name);
                    }
                    $value = null;
//var_dump($attribute->getNamespaceIndex());
                    if ((int)$attribute->getValueIndex() === (int)0xFFFFFFFF) {
                        $value = self::formatAttribute($attribute_value->getType(), $attribute_value->getValue());
                    } else {
                        $value = $string_pool->getPoolString($attribute->getValueIndex());
                    }
                    $this->xml_string .=
                        sprintf('%s="%s" ', $name, $value);
                }
                $this->xml_string .= '>';
                break;

            case XMLTreeNode::RES_XML_END_ELEMENT_TYPE:
                $offset += $node->getHeaderSize();
                $end_ext = new XMLTreeEndExt($binary, $offset);
//var_dump($end_ext);
//var_dump($string_pool->getPoolString($end_ext->getNamespaceIndex()));
//var_dump($string_pool->getPoolString($end_ext->getNameIndex()));
//var_dump((int)$end_ext->getNamespaceIndex() === (int)0xFFFFFFFF);
                $offset += $end_ext->getHeaderSize();
                if ((int)$end_ext->getNamespaceIndex() === (int)0xFFFFFFFF) {
                    $this->xml_string .=
                        sprintf('</%s>', $string_pool->getPoolString($end_ext->getNameIndex()));
                } else {
                    $this->xml_string .=
                        sprintf(
                            '</%s:%s>',
                            $string_pool->getPoolString($end_ext->getNamespaceIndex()),
                            $string_pool->getPoolString($end_ext->getNameIndex()));
                }
                break;
            case XMLTreeNode::RES_XML_END_NAMESPACE_TYPE:
                $offset += $node->getHeaderSize();
                $xml_tree_namespace = new XMLTreeNamespace($binary, $offset);
                $offset += $xml_tree_namespace->getHeaderSize();
                break;
            }
            if ($offset >= $this->chunk_header->getFileSize()) {
                break;
            }
        }
    }
/*
    public function getChunkHeader() {
        return $this->chunk_header;
    }
    public function getStringPoolHeader() {
        return $this->string_pool_header;
    }
    public function getXMLHeader() {
        return $this->xml_header;
    }
    public function getXMLTreeNode() {
        return $this->xml_tree_node;
    }
    public function getXMLTreeNamespace() {
        return $this->xml_tree_namespace;
    }
    public function getXMLTreeNode2() {
        return $this->xml_tree_node2;
    }
    public function getXMLTreeExt() {
        return $this->xml_tree_ext;
    }
    public function getXMLTreeAttribute() {
        return $this->xml_tree_attribute;
    }
    public function getXMLTreeAttributeValue() {
        return $this->xml_tree_attr_value;
    }
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
        default: // 他はとりあえず適当
            return sprintf('0x%08X', $value);
            break;
        }
    }
    public function getSimpleXMLElement() {
        return simplexml_load_string($this->xml_string);
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    if (!isset($argv[1])) {
        echo 'usage : php AndroidManifest.class.php [manifest_file]';
        exit;
    }
    $binary = file_get_contents($argv[1]);
    $xml = new AndroidManifest($binary);
//    var_dump($xml->getChunkHeader());
//    var_dump($xml->getStringPoolHeader());
//    var_dump($xml->getXMLHeader());
//    var_dump($xml->getXMLTreeNode());
//    var_dump($xml->getXMLTreeNamespace());
//    var_dump($xml->getXMLTreeNode2());
//    var_dump($xml->getXMLTreeExt());
//    var_dump($xml->getXMLTreeAttribute());
//    var_dump($xml->getXMLTreeAttributeValue());
    var_dump($xml->getSimpleXMLElement()->asXML());
}
