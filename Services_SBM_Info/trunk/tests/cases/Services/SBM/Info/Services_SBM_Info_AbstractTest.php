<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/bootstrap.php';
require_once 'Services/SBM/Info/Services_SBM_Info_Abstract_Mock.php';

class Services_SBM_Info_AbstractTest extends PHPUnit_Framework_TestCase
{
    protected $object;

    public function setUp() {
        $this->object = new Services_SBM_Info_Abstract_Mock(TEST_SBM_INFO_URL, TEST_SBM_INFO_TITLE);
    }

    public function testExecute()
    {
        $this->object->execute();
        $this->assertSame($this->object->_url, $this->object->_executedUrl);
        $url = $this->object->_url . 'test';
        $this->object->setUrl($url)->execute();
        $this->assertSame($url, $this->object->_executedUrl);
    }

    public function testExtractCount()
    {
        // Before execute
        $this->assertType('int', $this->object->extractCount($this->object->_apiData));

        // After execute
        $this->object->execute();
        $this->assertType('int', $this->object->extractCount($this->object->_apiData));
    }

    public function testExtractComments()
    {
        // Before execute
        $this->assertSame(array(), $this->object->extractComments($this->object->_apiData));

        // After execute
        $this->object->execute();
        $comments = $this->object->extractComments($this->object->_apiData);
        $this->assertType('array', $comments);
        foreach ($comments as $comment) {
            $this->assertArrayHasKey('user',    $comment);
            $this->assertArrayHasKey('tags',    $comment);
            $this->assertArrayHasKey('comment', $comment);
            $this->assertArrayHasKey('time',    $comment);
            $this->assertType('string', $comment['user']);
            $this->assertType('array',  $comment['tags']);
            $this->assertType('string', $comment['comment']);
            $this->assertType('int',    $comment['time']);
        }
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

    public function testSetFetchFunction()
    {
        $callback = create_function('$url', 'return $url;');
        $this->object->setFetchFunction($callback);
        $this->assertSame($callback, $this->object->_fetchFunction);
    }

    public function testSetToObjectFunction()
    {
        $callback = create_function('$data', 'return $data;');
        $this->object->setToObjectFunction($callback);
        $this->assertSame($callback, $this->object->_toObjectFunction);
    }

    public function testGetCount()
    {
        // Before execute
        $this->assertFalse($this->object->_countExtracted);
        $this->assertType('int', $this->object->getCount());
        $this->assertTrue($this->object->_countExtracted);

        // After execute
        $this->object->execute();
        $this->assertFalse($this->object->_countExtracted);
        $this->assertType('int', $this->object->getCount());
        $this->assertTrue($this->object->_countExtracted);
    }

    public function testGetUnit()
    {
        $this->object->_count = 0;
        $this->assertSame('user', $this->object->getUnit());
        $this->object->_count = 1;
        $this->assertSame('user', $this->object->getUnit());
        $this->object->_count = 2;
        $this->assertSame('users', $this->object->getUnit());
    }

    public function testGetRank()
    {
        $threshold = 10;
        for ($i = 0; $i <= 200; $i++) {
            $this->object->_count = $i;
            $this->assertSame($threshold <= $i ? 2 : 1, $this->object->getRank());
        }
    }

    public function testGetComments()
    {
        // Before execute
        $this->assertFalse($this->object->_commentsExtracted);
        $this->assertSame(array(), $this->object->getComments());
        $this->assertTrue($this->object->_commentsExtracted);

        // After execute
        $this->object->execute();
        $this->assertFalse($this->object->_commentsExtracted);
        $this->assertSame(
            $this->object->extractComments($this->object->_apiData),
            $this->object->getComments()
        );
        $this->assertTrue($this->object->_commentsExtracted);
    }
}