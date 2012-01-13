<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
class XMLTreeExt
{
    private $namespace_no;
    private $element_no;
    private $attr_offset;
    private $attr_size;
    private $attr_count;
    private $id_no;
    private $class_no;
    private $style_no;
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
    public function getNamespaceIndex() {
        return $this->namespace_no;
    }
    public function getElementIndex() {
        return $this->element_no;
    }
    public function getAttributeOffset() {
        return $this->attr_offset;
    }
    public function getAttributeSize() {
        return $this->attr_size;
    }
    public function getAttributeCount() {
        return $this->attr_count;
    }
    public function getIdIndex() {
        return $this->id_no;
    }
    public function getClassIndex() {
        return $this->class_no;
    }
    public function getStyleIndex() {
        return $this->style_no;
    }
    public function getHeaderSize() {
        return 20;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new XMLTreeExt($binary);
    var_dump($header->getNamespaceIndex());
    var_dump($header->getElementIndex());
    var_dump($header->getAttributeOffset());
    var_dump($header->getAttributeSize());
    var_dump($header->getAttributeCount());
    var_dump($header->getIdIndex());
    var_dump($header->getClassIndex());
    var_dump($header->getStyleIndex());
}
