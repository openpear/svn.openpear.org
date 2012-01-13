<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/Util/BinaryUtil.class.php';

class BinaryUtilTest extends PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testUnpackLE() {
        $data = "\x01\x02\x03\x04\x05\x06\x07\x08";
        $this->assertEquals(513, BinaryUtil::unpackLE($data));
        $this->assertEquals(array(513, 1027), BinaryUtil::unpackLE($data, 2));
        $this->assertEquals(array(513, 1027, 1541, 2055), BinaryUtil::unpackLE($data, 4));
    }
    public function testUnpackLE32() {
        $data = "\x01\x02\x03\x04\x05\x06\x07\x08";
        $this->assertEquals(67305985, BinaryUtil::unpackLE32($data));
        $this->assertEquals(array(67305985, 134678021), BinaryUtil::unpackLE32($data, 2));
    }
    public function testUnpack() {
        $data = "\x01\x02\x03\x04\x05\x06\x07\x08";
        $this->assertEquals(1, BinaryUtil::unpack('C*', $data));
        $this->assertEquals(range(1, 8), BinaryUtil::unpack('C*', $data, 10));
    }

}
