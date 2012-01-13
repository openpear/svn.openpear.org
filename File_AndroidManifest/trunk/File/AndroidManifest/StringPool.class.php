<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
class StringPool
{
    const DATA_OFFSET = 8;
    const SORTED_FLAG = 31;     // 1<<0
    const UTF8_FLAG = 323536;   // 1<<8

    private $header_size;
    private $strings_size;
    private $flag;
    private $strings_offset;
    private $strings;
    private $pool_strings;
    public function __construct($binary, $offset = self::DATA_OFFSET) {
        $this->header_size = BinaryUtil::unpackLE(substr($binary, $offset + 2));   // 28
        $this->strings_size = BinaryUtil::unpackLE32(substr($binary, $offset + 8));
        $this->flag = BinaryUtil::unpackLE32(substr($binary, $offset + 16));
        $this->strings_offset = BinaryUtil::unpackLE32(substr($binary, $offset + 20));

        $this->strings = BinaryUtil::unpackLE32(
                                    substr($binary, $offset + $this->header_size, 4 * $this->strings_size),
                                    $this->strings_size);

        $this->pool_strings = array();
        foreach ($this->strings as $index => $pool_offset) {
            $offset = self::DATA_OFFSET + $this->strings_offset + $pool_offset;
            $size = BinaryUtil::unpackLE(substr($binary, $offset));
            $this->pool_strings[$index] = null;
            foreach ((array)BinaryUtil::unpackLE(substr($binary, $offset + 2, $size * 2), $size) as $char) {
                $this->pool_strings[$index] .= chr($char);
            }
        }
    }
    public function getStringsSize() {
        return $this->strings_size;
    }
    public function getFlag() {
        return $this->flag;
    }
//    public function getStringsOffset() {
//        return $this->strings_offset;
//    }
//    public function getStrings() {
//        return $this->strings;
//    }
    public function getPoolStrings() {
        return $this->pool_strings;
    }
    public function getPoolString($index) {
        return isset($this->pool_strings[$index]) ? $this->pool_strings[$index] : null;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new StringPool($binary);
    var_dump($header->getStringsSize());
    var_dump($header->getFlag());
//    var_dump($header->getStringsOffset());
//    var_dump($header->getStrings());
    var_dump($header->getPoolStrings());
    var_dump($header->getPoolString(1));
}
