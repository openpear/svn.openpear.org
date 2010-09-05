<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/bootstrap.php';
require_once 'Services/SBM/Info/Services_SBM_Info_AbstractTest.php';
require_once 'Services/SBM/Info/Services_SBM_Info_Livedoor_Mock.php';

class Services_SBM_Info_LivedoorTest extends Services_SBM_Info_AbstractTest
{
    protected $object;

    public function setUp() {
        $this->object = new Services_SBM_Info_Livedoor_Mock(TEST_SBM_INFO_URL, TEST_SBM_INFO_TITLE);
    }

    public function testGetRank()
    {
        $threshold = 3;
        for ($i = 0; $i <= 200; $i++) {
            $this->object->_count = $i;
            $this->assertSame($threshold <= $i ? 2 : 1, $this->object->getRank());
        }
    }

    public function testGetEntryUrl()
    {
        $this->assertSame(
            sprintf('http://clip.livedoor.com/page/%s', TEST_SBM_INFO_URL),
            $this->object->getEntryUrl()
        );
    }

    public function testGetAddUrl()
    {
        $this->assertSame(
            sprintf('http://clip.livedoor.com/clip/add?link=%s&amp;title=%s&amp;jump=ref',
                urlencode(TEST_SBM_INFO_URL), urlencode(TEST_SBM_INFO_TITLE)
            ),
            $this->object->getAddUrl()
        );
    }
}