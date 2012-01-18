<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/XMLTreeNode.class.php';

class XMLTreeNodeTest extends PHPUnit_Framework_TestCase
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
        $header = new XMLTreeNode(file_get_contents($manifest_file), 792);
        $this->assertEquals(256, $header->getType());
        $this->assertEquals(16, $header->getHeaderSize());
        $this->assertEquals(24, $header->getSize());
        $this->assertEquals(2, $header->getLineOfXML());
        $this->assertEquals(-1, $header->getCommentIndex());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/AndroidManifest.xml';
        $header = new XMLTreeNode(file_get_contents($manifest_file), 1236);
        $this->assertEquals(259, $header->getType());
        $this->assertEquals(16, $header->getHeaderSize());
        $this->assertEquals(24, $header->getSize());
        $this->assertEquals(12, $header->getLineOfXML());
        $this->assertEquals(-1, $header->getCommentIndex());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLTreeNode(file_get_contents($manifest_file), 996);
        $this->assertEquals(256, $header->getType());
        $this->assertEquals(16, $header->getHeaderSize());
        $this->assertEquals(24, $header->getSize());
        $this->assertEquals(2, $header->getLineOfXML());
        $this->assertEquals(-1, $header->getCommentIndex());

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new XMLTreeNode(file_get_contents($manifest_file), 1628);
        $this->assertEquals(259, $header->getType());
        $this->assertEquals(16, $header->getHeaderSize());
        $this->assertEquals(24, $header->getSize());
        $this->assertEquals(17, $header->getLineOfXML());
        $this->assertEquals(-1, $header->getCommentIndex());
    }

}
