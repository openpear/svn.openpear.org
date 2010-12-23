<?php
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Diggin/Scraper/Adapter/Htmlscraping.php';

require_once 'Zend/Http/Response.php';

/**
 * Test class for Diggin_Scraper_Adapter_Htmlscraping.
 * Generated by PHPUnit on 2008-12-14 at 13:29:52.
 */
class Diggin_Scraper_Adapter_HtmlscrapingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Diggin_Scraper_Adapter_Htmlscraping
     * @access protected
     */
    protected $object;
    
    protected $response;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new Diggin_Scraper_Adapter_Htmlscraping;
        
        
        $responseHeader =            "HTTP/1.1 200 OK"        ."\r\n".
           "Date: Sat, 02 Aug 2008 15:17:11 GMT"."\r\n".
           "Server: Apache/2.2.6 (Win32) mod_ssl/2.2.6 OpenSSL/0.9.8e PHP/5.2.5"."\r\n".
           "Last-modified: Sun, 29 Jun 2008 21:20:50 GMT"."\r\n".
           "Accept-ranges: bytes"   . "\r\n" .
           "Content-length: 1000"   . "\r\n" .
           "Connection: close"      . "\r\n" .
           "Content-type: text/html; charset=utf-8;";
        $responseBody = '<html lang="ja">'.PHP_EOL.
                           '<head>'.PHP_EOL.
                           '<body>'.PHP_EOL.
                           'this is test&amp;test<br />'.PHP_EOL.
                           '</body>'.PHP_EOL.
                           '</html>';
        $response_str = "$responseHeader\r\n\r\n$responseBody";
        
        $this->response = Zend_Http_Response::fromString($response_str);
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

    /**
     * @todo Implement testGetXmlObject().
     */
    public function testGetXmlObject() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testGetXhtml()
    {

        //$this->object->getXhtml($response);
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testNosetConfigAmpasandEscape()
    {
    
        $asxml = $this->object->getSimplexml($this->response)->asXML();
        $asx = explode('test', $asxml);
        $this->assertEquals('&amp;', $asx[1]);
    }

    public function testAmpasandEscape()
    {
        $this->object->setConfig(array('url' => 'http://test.org/',
                                       'pre_ampersand_escape' => true));
        $xhtml = $this->object->getXhtml($this->response);
        $xh = explode('test', $xhtml);
        $this->assertEquals('&amp;amp;', $xh[1]);

        $asxml = $this->object->getSimplexml($this->response)->asXML();
        $asx = explode('test', $asxml);
        $this->assertEquals('&amp;', $asx[1]);
        
        
        $this->object->setConfig(array('url' => 'http://test.org/',
                                       'pre_ampersand_escape' => false));
        $xhtml = $this->object->getXhtml($this->response);
        $xh2 = explode('test', $xhtml);
        
        $this->assertEquals('&amp;', $xh2[1]);
    }
    
    /**
     * 
     */
    public function testReadData() {
        
        $this->object->setConfig(array('url' => 'http://test.org/'));
        
        $this->assertEquals($this->object->getSimplexml($this->response),
                            $this->object->readData($this->response));
    }

    /**
     * testSetConfig().
     */
    public function testSetConfig() {
        $obj = new Diggin_Scraper_Adapter_Htmlscraping();
        
        $obj->setConfig(array('url' => 'http://example.com/'));
        
        $this->assertAttributeEquals(
                array('tidy' => array('output-xhtml' => true,
                                'wrap' => 0,
                                /**'wrap-script-literals' => true*/),
                'pre_ampersand_escape' => false,
                'url' => 'http://example.com/'),

         'config', $obj); 
    }
    
    public function testSetConfigThrowException() {
        $obj = new Diggin_Scraper_Adapter_Htmlscraping();
        
        $this->setExpectedException('Diggin_Scraper_Adapter_Exception');
        $this->getExpectedException($obj->setConfig(false));
    }
}
