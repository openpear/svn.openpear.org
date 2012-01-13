<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
class XMLTreeEndExt
{
    private $namespace_no;
    private $name_no;
    public function __construct($binary, $offset) {
        $this->namespace_no = BinaryUtil::unpackLE32(substr($binary, $offset + 0));
        $this->name_no = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
    }
    public function getNamespaceIndex() {
        return $this->namespace_no;
    }
    public function getNameIndex() {
        return $this->name_no;
    }
    public function getHeaderSize() {
        return 8;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new XMLTreeEndExt($binary);
    var_dump($header->getNamespaceIndex());
    var_dump($header->getNameIndex());
}
