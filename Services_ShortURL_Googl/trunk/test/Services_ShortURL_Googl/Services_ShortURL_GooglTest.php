<?php
ini_set("include_path", dirname(__FILE__)."/../" . PATH_SEPARATOR . ini_get("include_path"));

require_once "PHPUnit/Framework/TestCase.php";
require_once "Mock.php";

/**
 * testcase for Services_ShortURL_Google
 *
 * @version $Id$
 */
class Services_ShortURL_GoogleTest extends PHPUnit_Framework_TestCase
{
    private $obj;

    public function setup() {
        $this->obj = new Services_ShortURL_Googl_Mock();
    }

    public function _testC() {
        $this->assertEquals(177692, $obj->getC(5381 << 5, 5381, ord('w')));
        $this->assertEquals(5863955, $obj->getC(177692 << 5, 177692, ord('w')));
        $this->assertEquals(119, $obj->getC(ord('w'), 0 << 6, 0 << 16, 0));
        $this->assertEquals(7806400, $obj->getC(ord('w'), 119 << 6, 119 << 16, -119));
    }

    public function testD() {
        $this->assertEquals('04294967296', $this->obj->getD('http://www.google.com/'));
        $this->assertEquals('04294967296', $this->obj->getD('http://www.php.net/'));
        $this->assertEquals('04294967296', $this->obj->getD('http://pear.php.net/'));
        $this->assertEquals('04294967296', $this->obj->getD('http://d.hatena.ne.jp/shimooka/'));
    }

    public function testE() {
        $this->assertEquals(687043177, $this->obj->getE('http://www.google.com/'));
        $this->assertEquals(2070840540, $this->obj->getE('http://www.php.net/'));
        $this->assertEquals(1895495391, $this->obj->getE('http://pear.php.net/'));
        $this->assertEquals(-265894462, $this->obj->getE('http://d.hatena.ne.jp/shimooka/'));
    }

    public function testF() {
        $this->assertEquals(1338220122, $this->obj->getF('http://www.google.com/'));
        $this->assertEquals(-339628655, $this->obj->getF('http://www.php.net/'));
        $this->assertEquals(-888702754, $this->obj->getF('http://pear.php.net/'));
        $this->assertEquals(1514055545, $this->obj->getF('http://d.hatena.ne.jp/shimooka/'));
    }

    public function testGetToken() {
        $this->assertEquals('78804486762', $this->obj->getToken('http://www.google.com/'));
        $this->assertEquals('702069990865', $this->obj->getToken('http://www.php.net/'));
        $this->assertEquals('732079848670', $this->obj->getToken('http://pear.php.net/'));
        $this->assertEquals('754197369801', $this->obj->getToken('http://d.hatena.ne.jp/shimooka/'));
    }

    public function testShorten() {
        $this->assertEquals('http://goo.gl/fbsS', $this->obj->shorten('http://www.google.com/'));
        $this->assertEquals('http://goo.gl/kJ9E', $this->obj->shorten('http://www.php.net/'));
        $this->assertEquals('http://goo.gl/LJGZ', $this->obj->shorten('http://pear.php.net/'));
        $this->assertEquals('http://goo.gl/BuQj', $this->obj->shorten('http://d.hatena.ne.jp/shimooka/'));
    }

    public function testExpand() {
        $this->assertEquals('http://www.google.com/', $this->obj->expand('http://goo.gl/fbsS'));
        $this->assertEquals('http://www.php.net/', $this->obj->expand('http://goo.gl/kJ9E'));
        $this->assertEquals('http://pear.php.net/', $this->obj->expand('http://goo.gl/LJGZ'));
        $this->assertEquals('http://d.hatena.ne.jp/shimooka/', $this->obj->expand('http://goo.gl/BuQj'));
    }
}
