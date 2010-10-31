<?php
require_once dirname(__FILE__) . '/../../../../bootstrap.php';
require_once 'Services/SBM/Info/Services_SBM_Info_AbstractTest.php';
require_once 'Services/SBM/Info/Twitter_Mock.php';

class Services_SBM_Info_TwitterTest extends Services_SBM_Info_AbstractTest
{
    protected $object;

    public function setUp() {
        $this->object = new Services_SBM_Info_Twitter_Mock(TEST_SBM_INFO_URL, TEST_SBM_INFO_TITLE);
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
            $this->assertArrayHasKey('url',     $comment);
            $this->assertType('string', $comment['user']);
            $this->assertType('array',  $comment['tags']);
            $this->assertType('string', $comment['comment']);
            $this->assertType('int',    $comment['time']);
            $this->assertType('string', $comment['url']);
        }
    }

    public function testGetUnit()
    {
        $this->object->_count = 0;
        $this->assertSame('tweet', $this->object->getUnit());
        $this->object->_count = 1;
        $this->assertSame('tweet', $this->object->getUnit());
        $this->object->_count = 2;
        $this->assertSame('tweets', $this->object->getUnit());
    }

    public function testGetRank()
    {
        $this->assertSame(1, $this->object->getRank());
    }

    public function testGetEntryUrl()
    {
        $this->assertSame(
            sprintf('http://topsy.com/trackback?url=%s', TEST_SBM_INFO_URL),
            $this->object->getEntryUrl()
        );
    }

    public function testGetAddUrl()
    {
        $this->assertSame(
            sprintf('http://twitter.com/?status=%s %s', TEST_SBM_INFO_TITLE, TEST_SBM_INFO_URL),
            $this->object->getAddUrl()
        );
    }
}