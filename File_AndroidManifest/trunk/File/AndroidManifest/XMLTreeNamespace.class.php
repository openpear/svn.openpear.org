<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
class XMLTreeNamespace
{
    private $prefix;
    private $uri;
    public function __construct($binary, $offset) {
        $this->prefix = BinaryUtil::unpackLE32(substr($binary, $offset + 0));
        $this->uri = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
    }
    public function getPrefixIndex() {
        return $this->prefix;
    }
    public function getUriIndex() {
        return $this->uri;
    }
    public function getHeaderSize() {
        return 8;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new XMLTreeNamespace($binary);
    var_dump($header->getPrefixIndex());
    var_dump($header->getUriIndex());
    var_dump($header->getHeaderSize());
}
