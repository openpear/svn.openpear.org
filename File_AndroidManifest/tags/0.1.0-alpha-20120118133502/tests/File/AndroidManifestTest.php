<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest.class.php';

class AndroidManifestTest extends PHPUnit_Framework_TestCase
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
        $this->assertXML('AndroidManifest.xml');
        $this->assertXML('MyApp_AndroidManifest.xml');
    }

    private function assertXML($manifest_filename) {
        $manifest_file = dirname(dirname(__FILE__)) . '/' . $manifest_filename;
        $parsed_file = dirname(dirname(__FILE__)) . '/' . basename($manifest_file, '.xml') . '_parsed.xml';
        $xml = new AndroidManifest(file_get_contents($manifest_file));
        $this->assertEquals(file_get_contents($parsed_file), $xml->getSimpleXMLElement()->asXML());
    }
}
