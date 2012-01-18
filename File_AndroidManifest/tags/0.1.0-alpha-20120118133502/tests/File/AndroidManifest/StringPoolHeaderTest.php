<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/StringPoolHeader.class.php';

class StringPoolHeaderTest extends PHPUnit_Framework_TestCase
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

    public function testParse() {
        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/AndroidManifest.xml';
        $header = new StringPoolHeader(file_get_contents($manifest_file));
        $this->assertEquals(1, $header->getType());
        $this->assertEquals(752, $header->getHeaderSize());
        $this->assertEquals(752, $header->getSize());
        $this->assertEquals(22, $header->getStringsSize());
        $this->assertEquals(0, $header->getStyleSize());
        $this->assertEquals(0, $header->getFlag());
        $this->assertEquals(116, $header->getStringsOffset());
        $this->assertEquals(0, $header->getStylesOffset());
//      $this->assertEquals('', $header->getStringPool());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new StringPoolHeader(file_get_contents($manifest_file));
        $this->assertEquals(1, $header->getType());
        $this->assertEquals(944, $header->getHeaderSize());
        $this->assertEquals(944, $header->getSize());
        $this->assertEquals(27, $header->getStringsSize());
        $this->assertEquals(0, $header->getStyleSize());
        $this->assertEquals(0, $header->getFlag());
        $this->assertEquals(136, $header->getStringsOffset());
        $this->assertEquals(0, $header->getStylesOffset());
//      $this->assertEquals('', $header->getStringPool());
    }

}
