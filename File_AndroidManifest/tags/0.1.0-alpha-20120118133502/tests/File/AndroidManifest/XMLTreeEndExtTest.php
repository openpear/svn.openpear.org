<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/XMLTreeEndExt.class.php';

class XMLTreeEndExtTest extends PHPUnit_Framework_TestCase
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
        $header = new XMLTreeEndExt(file_get_contents($manifest_file), 984);
        $this->assertEquals(-1, $header->getNamespaceIndex());
        $this->assertEquals(13, $header->getNameIndex());
        $this->assertEquals(8, $header->getHeaderSize());
        $header = new XMLTreeEndExt(file_get_contents($manifest_file), 1332);
        $this->assertEquals(-1, $header->getNamespaceIndex());
        $this->assertEquals(20, $header->getNameIndex());
        $this->assertEquals(8, $header->getHeaderSize());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLTreeEndExt(file_get_contents($manifest_file), 1188);
        $this->assertEquals(-1, $header->getNamespaceIndex());
        $this->assertEquals(16, $header->getNameIndex());
        $this->assertEquals(8, $header->getHeaderSize());
        $header = new XMLTreeEndExt(file_get_contents($manifest_file), 1596);
        $this->assertEquals(-1, $header->getNamespaceIndex());
        $this->assertEquals(23, $header->getNameIndex());
        $this->assertEquals(8, $header->getHeaderSize());
    }

}
