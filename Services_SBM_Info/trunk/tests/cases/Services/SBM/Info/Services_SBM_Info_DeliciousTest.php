<?php
require_once dirname(__FILE__) . '/../../../../bootstrap.php';
require_once 'Services/SBM/Info/Services_SBM_Info_AbstractTest.php';
require_once 'Services/SBM/Info/Delicious_Mock.php';

class Services_SBM_Info_DeliciousTest extends Services_SBM_Info_AbstractTest
{
    protected $object;

    public function setUp() {
        $this->object = new Services_SBM_Info_Delicious_Mock(TEST_SBM_INFO_URL, TEST_SBM_INFO_TITLE);
    }

    public function testGetRank()
    {
        $threshold = array(10, 100);
        for ($i = 0; $i <= 200; $i++) {
            $this->object->_count = $i;
            $testValue = 1;
            if ($threshold[0] <= $i) $testValue = 2;
            if ($threshold[1] <= $i) $testValue = 3;
            $this->assertSame($testValue, $this->object->getRank());
        }
    }

    public function testGetEntryUrl()
    {
        $this->assertSame(
            sprintf('http://www.delicious.com/url/%s', md5(TEST_SBM_INFO_URL)),
            $this->object->getEntryUrl()
        );
    }

    public function testGetAddUrl()
    {
        $this->assertSame(
            sprintf('http://www.delicious.com/save?url=%s&amp;title=%s',
                urlencode(TEST_SBM_INFO_URL), urlencode(TEST_SBM_INFO_TITLE)
            ),
            $this->object->getAddUrl()
        );
    }
}