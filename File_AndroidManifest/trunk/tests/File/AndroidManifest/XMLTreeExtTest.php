<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/XMLTreeExt.class.php';

class XMLTreeExtTest extends PHPUnit_Framework_TestCase
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
        $header = new XMLTreeExt(file_get_contents($manifest_file), 832);
        $this->assertEquals(-1, $header->getNamespaceIndex());
        $this->assertEquals(10, $header->getElementIndex());
        $this->assertEquals(20, $header->getAttributeOffset());
        $this->assertEquals(20, $header->getAttributeSize());
        $this->assertEquals(3, $header->getAttributeCount());
        $this->assertEquals(0, $header->getIdIndex());
        $this->assertEquals(0, $header->getClassIndex());
        $this->assertEquals(0, $header->getStyleIndex());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/AndroidManifest.xml';
        $header = new XMLTreeExt(file_get_contents($manifest_file), 1160);
        $this->assertEquals(-1, $header->getNamespaceIndex());
        $this->assertEquals(17, $header->getElementIndex());
        $this->assertEquals(20, $header->getAttributeOffset());
        $this->assertEquals(20, $header->getAttributeSize());
        $this->assertEquals(0, $header->getAttributeCount());
        $this->assertEquals(0, $header->getIdIndex());
        $this->assertEquals(0, $header->getClassIndex());
        $this->assertEquals(0, $header->getStyleIndex());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLTreeExt(file_get_contents($manifest_file), 1036);
        $this->assertEquals(-1, $header->getNamespaceIndex());
        $this->assertEquals(13, $header->getElementIndex());
        $this->assertEquals(20, $header->getAttributeOffset());
        $this->assertEquals(20, $header->getAttributeSize());
        $this->assertEquals(3, $header->getAttributeCount());
        $this->assertEquals(0, $header->getIdIndex());
        $this->assertEquals(0, $header->getClassIndex());
        $this->assertEquals(0, $header->getStyleIndex());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLTreeExt(file_get_contents($manifest_file), 1460);
        $this->assertEquals(-1, $header->getNamespaceIndex());
        $this->assertEquals(21, $header->getElementIndex());
        $this->assertEquals(20, $header->getAttributeOffset());
        $this->assertEquals(20, $header->getAttributeSize());
        $this->assertEquals(1, $header->getAttributeCount());
        $this->assertEquals(0, $header->getIdIndex());
        $this->assertEquals(0, $header->getClassIndex());
        $this->assertEquals(0, $header->getStyleIndex());
    }

}
