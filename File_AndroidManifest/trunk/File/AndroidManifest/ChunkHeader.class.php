<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
class ChunkHeader
{
    const DATA_OFFSET = 0;

    private $type;
    private $header_size;
    private $file_size;
    public function __construct($binary) {
        $offset = self::DATA_OFFSET;
        $this->type = BinaryUtil::unpackLE(substr($binary, $offset + 0));
        $this->header_size = BinaryUtil::unpackLE(substr($binary, $offset + 2));
        $this->file_size = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
    }
    public function getType() {
        return $this->type;
    }
    public function getHeaderSize() {
        return $this->header_size;
    }
    public function getFileSize() {
        return $this->file_size;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new ChunkHeader($binary);
    var_dump($header->getType());
    var_dump($header->getHeaderSize());
    var_dump($header->getFileSize());
}
