<?php
require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';
class XMLTreeAttributeValue
{
    // @see http://android.git.linaro.org/gitweb?p=platform/frameworks/base.git;a=blob;f=include/utils/ResourceTypes.h;h=10baa11a5114a4efd3ddb1d7fc4d870d8a2e8401;hb=HEAD#l233
    // Contains no data.
    const TYPE_NULL = 0x00;
    // The 'data' holds a ResTable_ref, a reference to another resource
    // table entry.
    const TYPE_REFERENCE = 0x01;
    // The 'data' holds an attribute resource identifier.
    const TYPE_ATTRIBUTE = 0x02;
    // The 'data' holds an index into the containing resource table's
    // global value string pool.
    const TYPE_STRING = 0x03;
    // The 'data' holds a single-precision floating point number.
    const TYPE_FLOAT = 0x04;
    // The 'data' holds a complex number encoding a dimension value,
    // such as "100in".
    const TYPE_DIMENSION = 0x05;
    // The 'data' holds a complex number encoding a fraction of a
    // container.
    const TYPE_FRACTION = 0x06;

    // Beginning of integer flavors...
    const TYPE_FIRST_INT = 0x10;

    // The 'data' is a raw integer value of the form n..n.
    const TYPE_INT_DEC = 0x10;
    // The 'data' is a raw integer value of the form 0xn..n.
    const TYPE_INT_HEX = 0x11;
    // The 'data' is either 0 or 1, for input "false" or "true" respectively.
    const TYPE_INT_BOOLEAN = 0x12;

    // Beginning of color integer flavors...
    const TYPE_FIRST_COLOR_INT = 0x1c;

    // The 'data' is a raw integer value of the form #aarrggbb.
    const TYPE_INT_COLOR_ARGB8 = 0x1c;
    // The 'data' is a raw integer value of the form #rrggbb.
    const TYPE_INT_COLOR_RGB8 = 0x1d;
    // The 'data' is a raw integer value of the form #argb.
    const TYPE_INT_COLOR_ARGB4 = 0x1e;
    // The 'data' is a raw integer value of the form #rgb.
    const TYPE_INT_COLOR_RGB4 = 0x1f;

    // ...end of integer flavors.
    const TYPE_LAST_COLOR_INT = 0x1f;

    // ...end of integer flavors.
    const TYPE_LAST_INT = 0x1;

    private $size;
    private $type;
    private $value;
    public function __construct($binary, $offset) {
        $this->size = BinaryUtil::unpackLE(substr($binary, $offset + 0));
        $padding = BinaryUtil::unpack('C', substr($binary, $offset + 2, 1));
        $this->type = BinaryUtil::unpack('C', substr($binary, $offset + 3, 1));
        $this->value = BinaryUtil::unpackLE32(substr($binary, $offset + 4));
    }
    public function getHeaderSize() {
        return $this->size;
    }
    public function getType() {
        return $this->type;
    }
    public function getValue() {
        return $this->value;
    }
}
if (isset($argv[0]) && __FILE__ === realpath($argv[0])) {
    $binary = file_get_contents('./MyApp_AndroidManifest.xml');
    $header = new XMLTreeAttributeValue($binary, 1068);
    var_dump($header->getHeaderSize());
    var_dump($header->getType());
    var_dump($header->getValue());
}
