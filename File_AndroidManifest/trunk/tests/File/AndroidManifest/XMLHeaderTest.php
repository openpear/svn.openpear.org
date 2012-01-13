<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';
require_once 'File/AndroidManifest/XMLHeader.class.php';

class XMLHeaderTest extends PHPUnit_Framework_TestCase
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
        $header = new XMLHeader(file_get_contents($manifest_file), 760);
        $this->assertEquals(384, $header->getType());
        $this->assertEquals(8, $header->getHeaderSize());
        $this->assertEquals(32, $header->getSize());
        $attributes = $header->getAttributes();
        $this->assertEquals(6, count($attributes));

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLHeader(file_get_contents($manifest_file), 952);
        $this->assertEquals(384, $header->getType());
        $this->assertEquals(8, $header->getHeaderSize());
        $this->assertEquals(44, $header->getSize());
        $attributes = $header->getAttributes();
        $this->assertEquals(9, count($attributes));
    }

}
