<?php
error_reporting(E_ALL);
require_once 'PHPUnit/Framework.php';

require_once 'File/AndroidManifest/StringPool.class.php';

class StringPoolTest extends PHPUnit_Framework_TestCase
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
        $header = new StringPool(file_get_contents($manifest_file));
        $this->assertEquals(22, $header->getStringsSize());
        $this->assertEquals(0, $header->getFlag());
//      $this->assertEquals(0, $header->getStringsOffset());
//      $this->assertEquals(0, $header->getStrings());
        $this->assertEquals(22, count($header->getPoolStrings()));
        $this->assertEquals('versionName', $header->getPoolString(1));
        $this->assertEquals('com.example.sampleproject', $header->getPoolString(11));

        $manifest_file = dirname(dirname(dirname(__FILE__))) . '/MyApp_AndroidManifest.xml';
        $header = new StringPool(file_get_contents($manifest_file));
        $this->assertEquals(27, $header->getStringsSize());
        $this->assertEquals(0, $header->getFlag());
//      $this->assertEquals(0, $header->getStringsOffset());
//      $this->assertEquals(0, $header->getStrings());
        $this->assertEquals(27, count($header->getPoolStrings()));
        $this->assertEquals('versionName', $header->getPoolString(1));
        $this->assertEquals('jp.klab.sample.myapp', $header->getPoolString(14));
    }

}
