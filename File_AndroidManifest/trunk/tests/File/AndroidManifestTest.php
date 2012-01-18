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

    /**
     * source code from https://github.com/gabu/AndroidSDK-RecipeBook
     */
    public function testRecipe021() {
        $this->assertXML('Recipe021_AndroidManifest.xml');
    }

    public function testRecipe025() {
        $this->assertXML('Recipe025_AndroidManifest.xml');
    }

    public function testRecipe028() {
        $this->assertXML('Recipe028_AndroidManifest.xml');
    }

    public function testRecipe035() {
        $this->assertXML('Recipe035_AndroidManifest.xml');
    }

    public function testRecipe062() {
        $this->assertXML('Recipe062_AndroidManifest.xml');
    }

    public function testRecipe066() {
        $this->assertXML('Recipe066_AndroidManifest.xml');
    }

    public function testRecipe095() {
        $this->assertXML('Recipe095_AndroidManifest.xml');
    }

    public function testRecipe097() {
        $this->assertXML('Recipe097_AndroidManifest.xml');
    }

    public function testRecipe103() {
        $this->assertXML('Recipe103_AndroidManifest.xml');
    }

    public function testRecipe104() {
        $this->assertXML('Recipe104_AndroidManifest.xml');
    }
}
