<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
class XMLHeader
{
    private $type;
    private $header_size;
    private $size;
    private $attributes;
    public function __construct($binary, $offset) {
        $this->type = BinaryUtil::unpackLE(substr($binary, $offset + 0));
        $this->header_size = BinaryUtil::unpackLE(substr($binary, $offset + 2));
        $this->size = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
        $this->attributes = array();
        for ($of = 8; $of < $this->size; $of += 4) {
            $this->attributes[] = sprintf('0x%08X', BinaryUtil::unpackLE32(substr($binary, $offset + $of, 4)));
        }
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
    public function getAttributes() {
        return $this->attributes;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new XMLHeader($binary);
    var_dump($header->getType());
    var_dump($header->getHeaderSize());
    var_dump($header->getSize());
    var_dump($header->getAttributes());
}
