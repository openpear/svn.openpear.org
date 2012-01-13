<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/XMLTreeAttribute.class.php';

class XMLTreeAttributeTest extends PHPUnit_Framework_TestCase
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
        $header = new XMLTreeAttribute(file_get_contents($manifest_file), 852);
        $this->assertEquals(7, $header->getNamespaceIndex());
        $this->assertEquals(0, $header->getNameIndex());
        $this->assertEquals(-1, $header->getValueIndex());
        $this->assertEquals(12, $header->getHeaderSize());
        $header = new XMLTreeAttribute(file_get_contents($manifest_file), 1104);
        $this->assertEquals(7, $header->getNamespaceIndex());
        $this->assertEquals(4, $header->getNameIndex());
        $this->assertEquals(-1, $header->getValueIndex());
        $this->assertEquals(12, $header->getHeaderSize());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLTreeAttribute(file_get_contents($manifest_file), 1056);
        $this->assertEquals(10, $header->getNamespaceIndex());
        $this->assertEquals(0, $header->getNameIndex());
        $this->assertEquals(-1, $header->getValueIndex());
        $this->assertEquals(12, $header->getHeaderSize());
        $header = new XMLTreeAttribute(file_get_contents($manifest_file), 1308);
        $this->assertEquals(10, $header->getNamespaceIndex());
        $this->assertEquals(4, $header->getNameIndex());
        $this->assertEquals(-1, $header->getValueIndex());
        $this->assertEquals(12, $header->getHeaderSize());
    }

}
