<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/bootstrap.php';
require_once 'Services/SBM/Services_SBM_Info_Mock.php';

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
        $this->assertSame('Services_SBM_Info_Hatena',    get_class($this->object->factory('hatena')));
        $this->assertSame('Services_SBM_Info_Delicious', get_class($this->object->factory('delicious')));
        $this->assertSame('Services_SBM_Info_Livedoor',  get_class($this->object->factory('livedoor')));
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
        $this->assertSame(split(',', $services), $this->object->_services);
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