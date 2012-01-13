<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
require_once 'File/AndroidManifest/StringPool.class.php';
class StringPoolHeader
{
    const DATA_OFFSET = 8;
    const SORTED_FLAG = 31;     // 1<<0
    const UTF8_FLAG = 323536;   // 1<<8

    private $type;
    private $header_size;
    private $size;
    private $strings_size;
    private $styles_size;
    private $flag;
    private $strings_offset;
    private $styles_offset;
    private $strings;
    private $pool_strings;
    private $string_pool;
    public function __construct($binary, $offset = self::DATA_OFFSET) {
        $this->type = BinaryUtil::unpackLE(substr($binary, $offset + 0));
        $this->header_size = BinaryUtil::unpackLE(substr($binary, $offset + 2));
        $this->size = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
        $this->strings_size = BinaryUtil::unpackLE32(substr($binary, $offset + 8));
        $this->styles_size = BinaryUtil::unpackLE32(substr($binary, $offset + 12));
        $this->flag = BinaryUtil::unpackLE32(substr($binary, $offset + 16));
        $this->strings_offset = BinaryUtil::unpackLE32(substr($binary, $offset + 20));
        $this->styles_offset = BinaryUtil::unpackLE32(substr($binary, $offset + 24));

        $this->string_pool = new StringPool($binary);
    }
    public function getType() {
        return $this->type;
    }
    public function getHeaderSize() {
        return $this->size;
    }
    public function getSize() {
        return $this->size;
    }
    public function getStringsSize() {
        return $this->strings_size;
    }
    public function getStyleSize() {
        return $this->styles_size;
    }
    public function getFlag() {
        return $this->flag;
    }
    public function getStringsOffset() {
        return $this->strings_offset;
    }
    public function getStylesOffset() {
        return $this->styles_offset;
    }
    public function getStringPool() {
        return $this->string_pool;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new StringPoolHeader($binary);
    var_dump($header->getType());
    var_dump($header->getHeaderSize());
    var_dump($header->getSize());
    var_dump($header->getStringsSize());
    var_dump($header->getStyleSize());
    var_dump($header->getFlag());
    var_dump($header->getStringsOffset());
    var_dump($header->getStylesOffset());
    var_dump($header->getStringPool());
}
