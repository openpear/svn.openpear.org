<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/ChunkHeader.class.php';

class ChunkHeaderTest extends PHPUnit_Framework_TestCase
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
        $header = new ChunkHeader(file_get_contents($manifest_file));
        $this->assertEquals(3, $header->getType());
        $this->assertEquals(8, $header->getHeaderSize());
        $this->assertEquals(1460, $header->getFileSize());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new ChunkHeader(file_get_contents($manifest_file));
        $this->assertEquals(3, $header->getType());
        $this->assertEquals(8, $header->getHeaderSize());
        $this->assertEquals(1804, $header->getFileSize());
    }

}
