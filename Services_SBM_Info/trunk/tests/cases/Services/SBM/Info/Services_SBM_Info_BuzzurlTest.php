<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/bootstrap.php';
require_once 'Services/SBM/Info/Services_SBM_Info_AbstractTest.php';
require_once 'Services/SBM/Info/Services_SBM_Info_Buzzurl_Mock.php';

class Services_SBM_Info_BuzzurlTest extends Services_SBM_Info_AbstractTest
{
    protected $object;

    public function setUp() {
        $this->object = new Services_SBM_Info_Buzzurl_Mock(TEST_SBM_INFO_URL, TEST_SBM_INFO_TITLE);
    }

    public function testGetRank()
    {
        $threshold = array(3, 10);
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
            sprintf('http://buzzurl.jp/entry/%s', TEST_SBM_INFO_URL),
            $this->object->getEntryUrl()
        );
    }

    public function testGetAddUrl()
    {
        $this->assertSame(
            sprintf('http://buzzurl.jp/config/add/confirm?url=%s&title=%s',
                urlencode(TEST_SBM_INFO_URL), urlencode(TEST_SBM_INFO_TITLE)
            ),
            $this->object->getAddUrl()
        );
    }
}