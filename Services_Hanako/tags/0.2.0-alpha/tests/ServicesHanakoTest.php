<?php
ini_set("include_path", dirname(__FILE__)."/../" . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once "Services/Hanako.php";

class ServicesHanakoTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor() {
        $hanako = new Services_Hanako($this->getHTTPRequest2Object(), '03', '50810100');
    }

    public function testConstructorWithInvalidAreaCode() {
        try {
            $hanako = new Services_Hanako($this->getHTTPRequest2Object(), 'a', '50810100');
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Invalid area code : [a]', $e->getMessage());
        }
    }

    public function testConstructorWithInvalidMasterCode() {
        try {
            $hanako = new Services_Hanako($this->getHTTPRequest2Object(), '03', 'a');
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Invalid master code : [a]', $e->getMessage());
        }
    }

    public function testNowOk() {
        $hanako = new Services_Hanako($this->getHTTPRequest2Object(), '03', '50810100');
        $result = $hanako->now();

        $this->assertEquals(array("hour" => "23時",
                                  "pollen" => "0",
                                  "wd" => "北",
                                  "ws" => "1",
                                  "temp" => "15.6",
                                  "prec" => "0",
                                  "prec_bool" => "無し"), $result);
    }

    public function testNowUnexpectedStatus() {
        $response_stub = $this->getMock('HTTP_Response2', array('getStatus'));
        $response_stub->expects($this->any())
                      ->method('getStatus')
                      ->will($this->returnValue('500'));

        $request_stub = $this->getMock('HTTP_Request2', array('send', 'getResponseCode'));
        $request_stub->expects($this->any())
                     ->method('send')
                     ->will($this->returnValue($response_stub));
        $request_stub->expects($this->any())
                     ->method('getResponseCode')
                     ->will($this->returnValue('500'));

        $hanako = new Services_Hanako($request_stub, '03', '50810100');
        try {
            $hanako->now();
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Hanako: return HTTP 500', $e->getMessage());
        }
    }

    public function testNowWithoutCookie() {
        $response_stub = $this->getMock('HTTP_Response2', array('getStatus', 'getCookies'));
        $response_stub->expects($this->any())
                      ->method('getStatus')
                      ->will($this->returnValue('200'));
        $response_stub->expects($this->any())
                      ->method('getCookies')
                      ->will($this->returnValue(array()));

        $request_stub = $this->getMock('HTTP_Request2', array('send', 'getResponseCode'));
        $request_stub->expects($this->any())
                     ->method('send')
                     ->will($this->returnValue($response_stub));
        $request_stub->expects($this->any())
                     ->method('getResponseCode')
                     ->will($this->returnValue('200'));

        $hanako = new Services_Hanako($request_stub, '03', '50810100');
        try {
            $hanako->now();
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Hanako: failed to get cookie', $e->getMessage());
        }
    }

    public function testNowUnexpectedStatusInGettingContents() {
        $response_stub = $this->getMock('HTTP_Response2', array('getStatus', 'getCookies'));
        $response_stub->expects($this->any())
                      ->method('getStatus')
                      ->will($this->onConsecutiveCalls('200', '500', '500'));
        $response_stub->expects($this->any())
                      ->method('getCookies')
                      ->will($this->returnValue(
                            array(array('name' => 'name',
                                        'value' => 'value'))));

        $request_stub = $this->getMock('HTTP_Request2', array('send', 'getResponseCode'));
        $request_stub->expects($this->any())
                     ->method('send')
                     ->will($this->returnValue($response_stub));
        $request_stub->expects($this->any())
                     ->method('getResponseCode')
                     ->will($this->onConsecutiveCalls('200', '500'));


        $hanako = new Services_Hanako($request_stub, '03', '50810100');
        try {
            $hanako->now();
            $this->fail();
        } catch (Exception $e) {
            $this->assertEquals('Hanako: return HTTP 500', $e->getMessage());
        }
    }

    public function testGetVersion() {
        $hanako = new Services_Hanako($this->getHTTPRequest2Object(), '03', '50810100');
        $this->assertEquals(SERVICES_HANAKO_VERSION, $hanako->getVersion());
    }

    public function testGetUserAgent() {
        $hanako = new Services_Hanako($this->getHTTPRequest2Object(), '03', '50810100');
        $this->assertEquals(SERVICES_HANAKO_USER_AGENT, $hanako->getUserAgent());
    }

    public function testSetUserAgent() {
        $hanako = new Services_Hanako($this->getHTTPRequest2Object(), '03', '50810100');
        $hanako->setUserAgent('');
        $this->assertEquals(SERVICES_HANAKO_USER_AGENT, $hanako->getUserAgent());

        $hanako->setUserAgent('some agent');
        $this->assertEquals('some agent', $hanako->getUserAgent());

        $hanako->setUserAgent(null);
        $this->assertEquals(SERVICES_HANAKO_USER_AGENT, $hanako->getUserAgent());

        $hanako->setUserAgent('another agent');
        $this->assertEquals('another agent', $hanako->getUserAgent());

        $hanako->setUserAgent();
        $this->assertEquals(SERVICES_HANAKO_USER_AGENT, $hanako->getUserAgent());
    }

    public function testGetAreaCode() {
        $hanako = new Services_Hanako($this->getHTTPRequest2Object(), '03', '50810100');
        $this->assertEquals('03', $hanako->getAreaCode());
    }

    public function testGetMasterCode() {
        $hanako = new Services_Hanako($this->getHTTPRequest2Object(), '03', '50810100');
        $this->assertEquals('50810100', $hanako->getMasterCode());
    }


    private function getHTTPRequest2Object() {
        $request_stub = $this->getMock('HTTP_Request2', array('send', 'getResponseCode'));
        $request_stub->expects($this->any())
                     ->method('send')
                     ->will($this->returnValue(
                            $this->getHTTPResponse2Object()));
        $request_stub->expects($this->any())
                     ->method('getResponseCode')
                     ->will($this->returnValue('200'));

        return $request_stub;
    }

    private function getHTTPResponse2Object() {
        $response_stub = $this->getMock('HTTP_Response2', array('getStatus', 'getCookies', 'getBody'));
        $response_stub->expects($this->any())
                      ->method('getStatus')
                      ->will($this->returnValue('200'));
        $response_stub->expects($this->any())
                      ->method('getCookies')
                      ->will($this->returnValue(
                            array(array('name' => 'name',
                                        'value' => 'value'))));
        $response_stub->expects($this->any())
                      ->method('getBody')
                      ->will($this->returnValue(
                            file_get_contents('hanako.html')));

        return $response_stub;
    }
}
