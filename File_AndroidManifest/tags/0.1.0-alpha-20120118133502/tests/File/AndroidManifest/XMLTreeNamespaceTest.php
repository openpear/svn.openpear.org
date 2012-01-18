<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/XMLTreeNamespace.class.php';

class XMLTreeNamespaceTest extends PHPUnit_Framework_TestCase
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
        $header = new XMLTreeNamespace(file_get_contents($manifest_file), 808);
        $this->assertEquals(6, $header->getPrefixIndex());
        $this->assertEquals(7, $header->getUriIndex());
        $this->assertEquals(8, $header->getHeaderSize());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/AndroidManifest.xml';
        $header = new XMLTreeNamespace(file_get_contents($manifest_file), 1452);
        $this->assertEquals(6, $header->getPrefixIndex());
        $this->assertEquals(7, $header->getUriIndex());
        $this->assertEquals(8, $header->getHeaderSize());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLTreeNamespace(file_get_contents($manifest_file), 1012);
        $this->assertEquals(9, $header->getPrefixIndex());
        $this->assertEquals(10, $header->getUriIndex());
        $this->assertEquals(8, $header->getHeaderSize());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLTreeNamespace(file_get_contents($manifest_file), 1796);
        $this->assertEquals(9, $header->getPrefixIndex());
        $this->assertEquals(10, $header->getUriIndex());
        $this->assertEquals(8, $header->getHeaderSize());
    }

}
