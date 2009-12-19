<?php
require_once 'PHPUnit/Framework.php';

require_once 'Diggin/Http/Response/CharactorEncoding/Wrapper/Zf.php';

/**
 * Test class for Diggin_Http_Response_CharactorEncoding_Wrapper_Zf.
 */
class Diggin_Http_Response_CharactorEncoding_Wrapper_ZfTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    Diggin_Http_Response_CharactorEncoding_Wrapper_Zf
     * @access protected
     */
    protected $object;

    protected $response;

    protected $responseBody;

    protected $responseSjis;
    protected $responseBodyBody;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->header = $header = <<<HEADER
HTTP/1.1 200 OK
Content-type: text/html charset=Shift-JIS;
HEADER;

    $this->responseBodyBody = 'あいうえお';
    $sjis1= mb_convert_encoding('あいうえお', 'SJIS', 'UTF-8');

    $this->responseBody = $resBody = <<<BODY
<html lang="ja">
<head>
<body>
あいうえお
</body>
</html>
BODY;

    $responseBodySjis = <<<BODY
<html lang="ja">
<head>
<body>
$sjis1
</body>
</html>
BODY;
        // create response
        $responseString = $header."\r\n\r\n".$resBody;
        $this->response = Zend_Http_Response::fromString($responseString);

        $this->responseSjis = Zend_Http_Response::fromString($header."\r\n\r\n".$responseBodySjis);

        $wrapper = Diggin_Http_Response_CharactorEncoding_Wrapper_Zf::createWrapper($this->response,
             Diggin_Http_Response_CharactorEncoding::detect($resBody, $this->response->getHeader('content-type')));

        $this->object = $wrapper;
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
     * @todo Implement testCreateWrapper().
     */
    public function testCreateWrapper()
    {
        $this->assertTrue($this->object instanceof Zend_Http_Response);
        $this->assertTrue($this->object instanceof Diggin_Http_Response_CharactorEncoding_Wrapper_WrapperInterface);
    }

    /**
     * @todo Implement testGetBody().
     */
    public function testGetBody()
    {
        $this->assertEquals($this->object->getBody(), $this->responseBody);
    }

    /**
     * @todo Implement testSetEncodingFrom().
     */
    public function testBodyisUTF8()
    {

        $wrapper = Diggin_Http_Response_CharactorEncoding_Wrapper_Zf::createWrapper($this->responseSjis,
             'SJIS');
        $body = $wrapper->getBody();
        
        $expectBodyBody = $this->responseBodyBody;
        $expect = <<<DOC
<html lang="ja">
<head>
<body>
あいうえお
</body>
</html>
DOC;
        $this->assertEquals($body, $expect);
    }
}
?>
