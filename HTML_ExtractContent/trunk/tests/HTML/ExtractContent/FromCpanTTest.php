<?php
require_once 'PHPUnit/Framework.php';

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/src/HTML/ExtractContent.php';

$cpanDir = new DirectoryIterator(dirname(__FILE__).'/_files/cpan/');

/**
 * Test class for HTML_ExtractContent.
 * Generated by PHPUnit on 2009-04-03 at 02:02:20.
 */
class HTML_FromCpanTTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    HTML_ExtractContent
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new HTML_ExtractContent;
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
     * @todo Implement testAnalyze().
     */
    public function testAnalyze() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testTitle().
     */
    public function testTitle() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetOpt().
     */
    public function testSetOpt() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>
