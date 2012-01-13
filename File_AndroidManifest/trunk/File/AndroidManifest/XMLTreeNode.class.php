<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
class XMLTreeNode
{
    // @see http://android.git.linaro.org/gitweb?p=platform/frameworks/base.git;a=blob;f=include/utils/ResourceTypes.h;h=10baa11a5114a4efd3ddb1d7fc4d870d8a2e8401;hb=HEAD#l179
    const RES_NULL_TYPE = 0x0000;
    const RES_STRING_POOL_TYPE = 0x0001;
    const RES_TABLE_TYPE = 0x0002;
    const RES_XML_TYPE = 0x0003;

    // Chunk types in RES_XML_TYPE
    const RES_XML_FIRST_CHUNK_TYPE = 0x0100;
    const RES_XML_START_NAMESPACE_TYPE= 0x0100;
    const RES_XML_END_NAMESPACE_TYPE = 0x0101;
    const RES_XML_START_ELEMENT_TYPE = 0x0102;
    const RES_XML_END_ELEMENT_TYPE = 0x0103;
    const RES_XML_CDATA_TYPE = 0x0104;
    const RES_XML_LAST_CHUNK_TYPE = 0x017f;
    // This contains a uint32_t array mapping strings in the string
    // pool back to resource identifiers.  It is optional.
    const RES_XML_RESOURCE_MAP_TYPE = 0x0180;

    // Chunk types in RES_TABLE_TYPE
    const RES_TABLE_PACKAGE_TYPE = 0x0200;
    const RES_TABLE_TYPE_TYPE = 0x0201;
    const RES_TABLE_TYPE_SPEC_TYPE = 0x0202;

    private $type;
    private $header_size;
    private $size;
    private $lox;
    private $comment_no;
    public function __construct($binary, $offset) {
        $this->type = BinaryUtil::unpackLE(substr($binary, $offset + 0));
        $this->header_size = BinaryUtil::unpackLE(substr($binary, $offset + 2));
        $this->size = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
        $this->lox = BinaryUtil::unpackLE32(substr($binary, $offset + 8));
        $this->comment_no = BinaryUtil::unpackLE32(substr($binary, $offset + 12));
    }
    public function getType() {
        return $this->type;
    }
    public function getHeaderSize() {
        return $this->header_size;
    }
    public function getSize() {
        return $this->size;
    }
    public function getLineOfXML() {
        return $this->lox;
    }
    public function getCommentIndex() {
        return $this->comment_no;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new XMLTreeNode($binary);
    var_dump($header->getType());
    var_dump($header->getHeaderSize());
    var_dump($header->getSize());
    var_dump($header->getLineOfXML());
    var_dump($header->getCommentIndex());
}
