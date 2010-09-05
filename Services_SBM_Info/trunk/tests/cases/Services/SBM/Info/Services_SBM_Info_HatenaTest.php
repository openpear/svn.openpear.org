<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/bootstrap.php';
require_once 'Services/SBM/Info/Services_SBM_Info_AbstractTest.php';
require_once 'Services/SBM/Info/Services_SBM_Info_Hatena_Mock.php';

class Services_SBM_Info_HatenaTest extends Services_SBM_Info_AbstractTest
{
    protected $object;

    public function setUp() {
        $this->object = new Services_SBM_Info_Hatena_Mock(TEST_SBM_INFO_URL, TEST_SBM_INFO_TITLE);
    }

    public function testGetEntryUrl()
    {
        $ssl = preg_match('/^https:\/\//', TEST_SBM_INFO_URL) ? 's/' : '';
        $this->assertSame(
            sprintf('http://b.hatena.ne.jp/entry/%s',
                $ssl . preg_replace('/^https?:\/\//', '', TEST_SBM_INFO_URL)
            ),
            $this->object->getEntryUrl()
        );
    }

    public function testGetAddUrl()
    {
        $this->assertSame(
            sprintf('http://b.hatena.ne.jp/add?url=%s&amp;title=%s',
                urlencode(TEST_SBM_INFO_URL), urlencode(TEST_SBM_INFO_TITLE)
            ),
            $this->object->getAddUrl()
        );
    }
}