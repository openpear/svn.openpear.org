<?php
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'Services/SBM/Info_Mock.php';

class Services_SBM_InfoTest extends PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new Services_SBM_Info_Mock(TEST_SBM_INFO_URL, TEST_SBM_INFO_TITLE);
    }

    public function test__call()
    {
        $this->assertType('int',    $this->object->getCount('hatena'));
        $this->assertType('string', $this->object->getUnit('hatena'));
        $this->assertType('int',    $this->object->getRank('hatena'));
        $this->assertType('array',  $this->object->getComments('hatena'));
        $this->assertType('string', $this->object->getEntryUrl('hatena'));
        $this->assertType('string', $this->object->getAddUrl('hatena'));

        $services = array('Hatena', 'Delicious', 'Livedoor', 'Buzzurl');
        $this->object->setServices($services);

        foreach (array('Count', 'Unit', 'Rank', 'EntryUrl', 'AddUrl') as $key) {
            $method = 'get' . $key;
            $info = $this->object->$method();
            foreach ($services as $serviceName) {
                $this->assertArrayHasKey($serviceName, $info);
                $this->assertSame($this->object->$method($serviceName), $info[$serviceName]);
            }
        }
    }

    public function testFactory()
    {
        $this->assertSame('Services_SBM_Info_Hatena_Mock',    get_class($this->object->factory('hatena')));
        $this->assertSame('Services_SBM_Info_Delicious_Mock', get_class($this->object->factory('delicious')));
        $this->assertSame('Services_SBM_Info_Livedoor_Mock',  get_class($this->object->factory('livedoor')));
    }

    public function testSetUrl()
    {
        $url = TEST_SBM_INFO_URL . 'test';
        $this->assertSame($this->object, $this->object->setUrl($url));
        $this->assertSame($url, $this->object->_url);
    }

    public function testSetTitle()
    {
        $title = TEST_SBM_INFO_TITLE . 'test';
        $this->assertSame($this->object, $this->object->setTitle($title));
        $this->assertSame($title, $this->object->_title);
    }

    public function testSetServices()
    {
        $services = 'test1,test2,test3';
        $this->assertSame($this->object, $this->object->setServices($services));
        $this->assertSame(explode(',', $services), $this->object->_services);
    }

    public function testSetFetchFunction()
    {
        $callback = create_function('', '');
        $this->assertSame($this->object, $this->object->setFetchFunction($callback));
        foreach ($this->object->_services as $serviceName) {
            $this->assertSame($callback, $this->object->factory($serviceName)->_fetchFunction);
        }
    }

    public function testSetToObjectFunction()
    {
        $callback = create_function('', '');
        $this->assertSame($this->object, $this->object->setToObjectFunction($callback));
        foreach ($this->object->_services as $serviceName) {
            $this->assertSame($callback, $this->object->factory($serviceName)->_toObjectFunction);
        }
    }

    public function testSetProxy()
    {
        $host = 'localhost';
        $port = '8080';
        $this->assertSame($this->object, $this->object->setProxy($host, $port));
        foreach ($this->object->_services as $serviceName) {
            $Service = $this->object->factory($serviceName);
            $this->assertSame($host, $Service->_proxyHost);
            $this->assertSame($port, $Service->_proxyPort);
        }
    }

    public function testSetErrorLog()
    {
        $errorLog = './error.log';
        $this->assertSame($this->object, $this->object->setErrorLog($errorLog));
        $this->assertSame($errorLog, $this->object->_errorLog);
    }

    public function testGetAll()
    {
        $array = $this->object->getAll();
        foreach ($array as $serviceName => $data) {
            $this->assertArrayHasKey('count',     $data);
            $this->assertArrayHasKey('unit',      $data);
            $this->assertArrayHasKey('rank',      $data);
            $this->assertArrayHasKey('entry_url', $data);
            $this->assertArrayHasKey('add_url',   $data);
            $this->assertArrayNotHasKey('comments', $data);
        }

        $array = $this->object->getAll(true);
        foreach ($array as $serviceName => $data) {
            $this->assertArrayHasKey('count',     $data);
            $this->assertArrayHasKey('unit',      $data);
            $this->assertArrayHasKey('rank',      $data);
            $this->assertArrayHasKey('entry_url', $data);
            $this->assertArrayHasKey('add_url',   $data);
            $this->assertArrayHasKey('comments' , $data);
        }
    }

    public function testGetFailedServices()
    {
        $this->object->setUrl('http://localhost/');
        $this->object->setProxy('_', 1234);
        $this->object->execute();
        $this->assertNotEmpty($this->object->getFailedServices());
        $this->assertSame($this->object->_failedServices, $this->object->getFailedServices());
    }

    public function testCamelize()
    {
        $this->assertSame('Example',  $this->object->camelize('example'));
        $this->assertSame('Example',  $this->object->camelize('EXAMPLE'));
        $this->assertSame('Example',  $this->object->camelize('examPLE'));
        $this->assertSame('Example',  $this->object->camelize('    example    '));
        $this->assertSame('HogeFuga', $this->object->camelize('hoge fuga'));
        $this->assertSame('HogeFuga', $this->object->camelize('hoge_fuga', '_'));
    }
}